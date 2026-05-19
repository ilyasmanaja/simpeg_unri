<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\SuratTugas;
use App\Models\Anggota;
use App\Models\Berkas;
use App\Models\Pegawai;

class SuratTugasController extends Controller
{
    private const KOLOM_ANGGOTA = 'id_surat_tugas';

    private function pegawaiLogin(): ?Pegawai
    {
        $user = Auth::user(); 
        if (!$user || !$user->id_pegawai) return null;

        return Pegawai::find($user->id_pegawai);
    }

    public function index()
    {
        $pegawai = $this->pegawaiLogin();

        $surat = SuratTugas::with(['anggota', 'berkasAktif'])
                    ->latest('id_surat_tugas')
                    ->get();

        return view('pengajuansurattugas.read_pengajuan_surat_tugas', compact('surat', 'pegawai'));
    }

    public function create()
    {
        $pegawai = $this->pegawaiLogin();

        return view('pengajuansurattugas.form_pengajuan_surat_tugas', [
            'pegawai' => $pegawai,
            'anggota' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'waktu_pelaksanaan' => ['required', 'date', 'after_or_equal:today'],
            'lama_pelaksanaan'  => 'required|numeric|min:1|max:30',
            'perihal'           => 'required|string',
            'berkas'            => 'required|file|max:10240',
            'nama_anggota'      => 'nullable|array',
            'nama_anggota.*'    => 'nullable|string',
        ], [
            'waktu_pelaksanaan.after_or_equal' => 'Tanggal pelaksanaan tidak boleh kurang dari hari ini.',
            'berkas.required'                  => 'Berkas pendukung wajib diupload.',
            'berkas.max'                       => 'Ukuran file maksimal 10MB.',
        ]);

        if ($request->has('nama_anggota')) {
            $namaList = array_filter($request->nama_anggota);
            if (count($namaList) !== count(array_unique(array_map('strtolower', $namaList)))) {
                return back()->withInput()
                             ->withErrors(['nama_anggota' => 'Terdapat duplikasi nama anggota.']);
            }
        }

        $surat = SuratTugas::create([
            'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
            'lama_pelaksanaan'  => $request->lama_pelaksanaan,
            'perihal'           => $request->perihal,
            'status'            => 'menunggu diproses',
            'alasan_penolakan'  => null,
            'berkas_bermasalah' => false,
        ]);

        $this->simpanBerkas($request, $surat->id_surat_tugas);

        $this->simpanAnggota($request, $surat->id_surat_tugas);

        return redirect()->route('surat.index')
                ->with('success', 'Pengajuan berhasil disimpan.');
    }

    public function edit($id)
    {
        $surat   = SuratTugas::with('berkasAktif')->findOrFail($id);
        $pegawai = $this->pegawaiLogin();
        $anggota = $this->formatAnggota($id);

        return view('pengajuansurattugas.form_pengajuan_surat_tugas', [
            'surat'   => $surat,
            'pegawai' => $pegawai,
            'anggota' => $anggota,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'waktu_pelaksanaan' => ['required', 'date', 'after_or_equal:today'],
            'lama_pelaksanaan'  => 'required|numeric|min:1|max:30',
            'perihal'           => 'required|string',
            'berkas'            => 'nullable|file|max:10240',
            'nama_anggota'      => 'nullable|array',
            'nama_anggota.*'    => 'nullable|string',
        ], [
            'waktu_pelaksanaan.after_or_equal' => 'Tanggal pelaksanaan tidak boleh kurang dari hari ini.',
            'berkas.max'                       => 'Ukuran file maksimal 10MB.',
        ]);

        if ($request->has('nama_anggota')) {
            $namaList = array_filter($request->nama_anggota);
            if (count($namaList) !== count(array_unique(array_map('strtolower', $namaList)))) {
                return back()->withInput()
                             ->withErrors(['nama_anggota' => 'Terdapat duplikasi nama anggota.']);
            }
        }

        $surat = SuratTugas::findOrFail($id);

        if ($request->hasFile('berkas')) {
            $this->hapusBerkasSurat($id);
            $this->simpanBerkas($request, $id);
        }

        $surat->waktu_pelaksanaan = $request->waktu_pelaksanaan;
        $surat->lama_pelaksanaan  = $request->lama_pelaksanaan;
        $surat->perihal           = $request->perihal;
        $surat->save();

        Anggota::where(self::KOLOM_ANGGOTA, $id)->delete();
        $this->simpanAnggota($request, $id);

        return redirect()->route('surat.index')
                ->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function ajukanKembali($id)
    {
        $surat   = SuratTugas::with('berkasAktif')->findOrFail($id);
        $pegawai = $this->pegawaiLogin();
        $anggota = $this->formatAnggota($id);

        return view('pengajuansurattugas.form_pengajuan_surat_tugas', [
            'surat'   => $surat,
            'pegawai' => $pegawai,
            'anggota' => $anggota,
        ]);
    }

    public function prosesAjukanKembali(Request $request, $id)
    {
        $surat       = SuratTugas::findOrFail($id);
        $statusLower = strtolower(trim($surat->status ?? ''));

        if ($statusLower === 'ditolak (verifikasi)') {

            $request->validate([
                'berkas' => 'required|file|max:10240',
            ], [
                'berkas.required' => 'Berkas wajib diupload ulang.',
                'berkas.max'      => 'Ukuran file maksimal 10MB.',
            ]);

            $this->hapusBerkasSurat($id);
            $this->simpanBerkas($request, $id);

            $surat->status            = 'menunggu diproses';
            $surat->alasan_penolakan  = null;
            $surat->berkas_bermasalah = false;
            $surat->save();

            return redirect()->route('surat.index')
                    ->with('success', 'Berkas berhasil diupload ulang. Pengajuan diajukan kembali.');
        }

        $request->validate([
            'waktu_pelaksanaan' => ['required', 'date', 'after_or_equal:today'],
            'lama_pelaksanaan'  => 'required|numeric|min:1|max:30',
            'perihal'           => 'required|string',
            'berkas'            => 'nullable|file|max:10240',
            'nama_anggota'      => 'nullable|array',
            'nama_anggota.*'    => 'nullable|string',
        ], [
            'waktu_pelaksanaan.after_or_equal' => 'Tanggal pelaksanaan tidak boleh kurang dari hari ini.',
            'berkas.max'                       => 'Ukuran file maksimal 10MB.',
        ]);

        if ($request->has('nama_anggota')) {
            $namaList = array_filter($request->nama_anggota);
            if (count($namaList) !== count(array_unique(array_map('strtolower', $namaList)))) {
                return back()->withInput()
                             ->withErrors(['nama_anggota' => 'Terdapat duplikasi nama anggota.']);
            }
        }

        if ($request->hasFile('berkas')) {
            $this->hapusBerkasSurat($id);
            $this->simpanBerkas($request, $id);
        }

        $surat->waktu_pelaksanaan = $request->waktu_pelaksanaan;
        $surat->lama_pelaksanaan  = $request->lama_pelaksanaan;
        $surat->perihal           = $request->perihal;
        $surat->status            = 'menunggu diproses';
        $surat->alasan_penolakan  = null;
        $surat->berkas_bermasalah = false;
        $surat->save();

        Anggota::where(self::KOLOM_ANGGOTA, $id)->delete();
        $this->simpanAnggota($request, $id);

        return redirect()->route('surat.index')
                ->with('success', 'Pengajuan berhasil diajukan kembali.');
    }

    public function destroy($id)
    {
        $surat = SuratTugas::findOrFail($id);

        $this->hapusBerkasSurat($id);

        Anggota::where(self::KOLOM_ANGGOTA, $id)->delete();
        $surat->delete();

        return redirect()->route('surat.index')
                ->with('success', 'Data berhasil dihapus.');
    }
    
    public function viewBerkas($idBerkas)
    {
        $berkas = Berkas::findOrFail($idBerkas);
        $path   = public_path('uploads/' . $berkas->file_path);

        if (!File::exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file($path);
    }

    // ================================================================
    // PRIVATE HELPERS
    // ================================================================

    /**
     * Simpan file ke disk dan catat ke tabel BERKAS.
     */
    private function simpanBerkas(Request $request, $idSurat): ?Berkas
    {
        if (!$request->hasFile('berkas')) return null;

        $uploadPath = public_path('uploads');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $file       = $request->file('berkas');
        $namaFile   = time() . '_' . $file->getClientOriginalName();
        $file->move($uploadPath, $namaFile);

        $pegawai = $this->pegawaiLogin();

        return Berkas::create([
            'nama_berkas'    => $file->getClientOriginalName(),
            'jenis_berkas'   => 'surat_tugas',
            'file_path'      => $namaFile,
            'id_pegawai'     => $pegawai?->id_pegawai,
            'id_surat_tugas' => $idSurat,
        ]);
    }

    /**
     * Hapus semua berkas surat tugas dari disk dan tabel BERKAS.
     */
    private function hapusBerkasSurat($idSurat): void
    {
        $berkasLama = Berkas::where('id_surat_tugas', $idSurat)->get();

        foreach ($berkasLama as $b) {
            $path = public_path('uploads/' . $b->file_path);
            if (File::exists($path)) {
                File::delete($path);
            }
            $b->delete();
        }
    }

    /**
     * Format anggota untuk dikirim ke view.
     */
    private function formatAnggota($idSurat): array
    {
        return Anggota::where(self::KOLOM_ANGGOTA, $idSurat)
            ->get()
            ->map(fn($a) => [
                'nama'      => $a->nama_anggota ?? '',
                'id_pegawai' => $a->id_pegawai,
            ])
            ->toArray();
    }

    /**
     * Simpan daftar anggota ke tabel ANGGOTA.
     */
    private function simpanAnggota(Request $request, $idSurat): void
    {
        if (!$request->has('nama_anggota')) return;

        foreach ($request->nama_anggota as $nama) {
            if (empty(trim($nama ?? ''))) continue;

            Anggota::create([
                'nama_anggota'      => $nama,
                self::KOLOM_ANGGOTA => $idSurat,
            ]);
        }
    }
}