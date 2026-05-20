<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\PengajuanKenaikan;
use App\Models\PangkatGolongan;
use App\Models\Berkas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PangkatController extends Controller
{
    // ══════════════════════════════════════
    // Helper: urutanMap
    // ══════════════════════════════════════
    private $urutanMap = [
        'I/a'=>1,'I/b'=>2,'I/c'=>3,'I/d'=>4,
        'II/a'=>5,'II/b'=>6,'II/c'=>7,'II/d'=>8,
        'III/a'=>9,'III/b'=>10,'III/c'=>11,'III/d'=>12,
        'IV/a'=>13,'IV/b'=>14,'IV/c'=>15,'IV/d'=>16,'IV/e'=>17
    ];

    // ══════════════════════════════════════
    // Helper: Dapatkan Pegawai Login
    // ══════════════════════════════════════
    private function getPegawai(): Pegawai
    {
        // Sementara di-hardcode 5 sampai fitur Login/Auth berjalan
        $idPegawai = session('id_pegawai', 1);
        return Pegawai::with(['pangkatGolongan', 'jabatanFungsional'])->findOrFail($idPegawai);
    }

    // ══════════════════════════════════════
    // Helper: Upload Berkas
    // ══════════════════════════════════════
    private function uploadBerkas(Request $request, PengajuanKenaikan $pengajuan, $idPegawai, $berkasAda = null): void 
    {
        $berkasConfig = [
            'sk_cpns'   => ['label' => 'SK CPNS'],
            'sk_pns'    => ['label' => 'SK PNS'],
            'pak'       => ['label' => 'PAK'],
            'publikasi' => ['label' => 'Publikasi'],
        ];

        foreach ($berkasConfig as $key => $cfg) {
            if (!$request->hasFile($key)) continue;

            $file     = $request->file($key);
            $namaFile = $key . '_' . $idPegawai . '_' . time() . '.pdf';
            // Simpan file ke folder storage public
            $path     = $file->storeAs("uploads/pangkat/{$pengajuan->id_pengajuan}", $namaFile, 'public');

            if ($berkasAda && $berkasAda->has($key)) {
                // Update berkas yang sudah ada
                $lama = $berkasAda->get($key);
                if (Storage::disk('public')->exists($lama->file_path)) {
                    Storage::disk('public')->delete($lama->file_path);
                }
                $lama->update([
                    'nama_berkas'  => $cfg['label'],
                    'file_path'    => $path,
                ]);
            } else {
                // Buat data berkas baru
                Berkas::create([
                    'id_berkas'    => 'BRK-' . time() . '-' . strtoupper($key),
                    'id_pengajuan' => $pengajuan->id_pengajuan,
                    'id_pegawai'   => $idPegawai,
                    'jenis_berkas' => $key,
                    'nama_berkas'  => $cfg['label'],
                    'file_path'    => $path,
                ]);
            }
        }
    }

    // ══════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════
    public function index()
    {
        $pegawai = $this->getPegawai();

        $pengajuan = PengajuanKenaikan::with(['berkas', 'pangkatGolongan'])
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->where('jenis_pengajuan', 'kenaikan_pangkat')
            ->orderByDesc('id_pengajuan')
            ->get();

        // Modifikasi data untuk view sesuai format HTML sebelumnya
        foreach ($pengajuan as $p) {
            $p->jenis_pangkat = $p->pangkatGolongan->jenis_pangkat ?? '-';
            $p->status_pengajuan = strtoupper($p->status_pengajuan);
        }

        return view('dosen.pangkat.index', compact('pegawai', 'pengajuan'));
    }

    // ══════════════════════════════════════
    // CREATE
    // ══════════════════════════════════════
    public function create()
    {
        $pegawai = $this->getPegawai();

        if (strtolower($pegawai->status_pegawai) !== 'pns') {
            return redirect()->route('dosen.pangkat-golongan.index')
                             ->with('error', 'Hanya pegawai PNS yang dapat mengajukan kenaikan pangkat.');
        }

        $urutan_sekarang = $this->urutanMap[$pegawai->id_panggol ?? ''] ?? 0;
        $jenis_pegawai   = !empty($pegawai->nidn) ? 'dosen' : 'tendik';
        $urutan_min      = ($jenis_pegawai === 'dosen') ? 9 : 5;
        $urutan_target   = ($urutan_sekarang === 0) ? $urutan_min : $urutan_sekarang + 1;
        $sudahPuncak     = ($urutan_sekarang >= 17);

        $adaPending = PengajuanKenaikan::where('id_pegawai', $pegawai->id_pegawai)
            ->whereIn('status_pengajuan', ['menunggu', 'verifikasi', 'persetujuan'])
            ->exists();

        $pangkatList = PangkatGolongan::all()->sortBy(function($p) {
            return $this->urutanMap[$p->id_panggol] ?? 99;
        });

        $semuaPangkat = [];
        foreach ($pangkatList as $p) {
            $urutan         = $this->urutanMap[$p->id_panggol] ?? 0;
            $p->urutan      = $urutan;
            $p->bisa        = ($urutan === $urutan_target) && !$sudahPuncak;
            $p->lebihRendah = ($urutan_sekarang > 0) && ($urutan <= $urutan_sekarang);
            $semuaPangkat[] = $p;
        }

        $jabfungToPangkat = [
            'Asisten Ahli'  => ['min' => 10, 'max' => 10, 'label' => 'III/b'],
            'Lektor'        => ['min' => 11, 'max' => 12, 'label' => 'III/c – III/d'],
            'Lektor Kepala' => ['min' => 13, 'max' => 14, 'label' => 'IV/a – IV/b'],
            'Guru Besar'    => ['min' => 15, 'max' => 17, 'label' => 'IV/c – IV/e'],
        ];

        $jabfungSekarang = $pegawai->jabatanFungsional->jenis_jabfung ?? null;
        $jabfungInfo     = $jabfungSekarang ? ($jabfungToPangkat[$jabfungSekarang] ?? null) : null;

        $identitas_label = ($jenis_pegawai === 'dosen') ? 'NIDN' : 'NIP';
        $identitas_value = ($jenis_pegawai === 'dosen') ? ($pegawai->nidn ?? '-') : ($pegawai->nip ?? '-');

        return view('dosen.pangkat.form', compact(
            'pegawai', 'semuaPangkat', 'urutan_sekarang', 'urutan_target',
            'sudahPuncak', 'adaPending', 'jabfungSekarang', 'jabfungInfo',
            'identitas_label', 'identitas_value'
        ));
    }

    // ══════════════════════════════════════
    // STORE
    // ══════════════════════════════════════
    public function store(Request $request)
    {
        $pegawai = $this->getPegawai();

        // Gunakan validasi bawaan Laravel (The Laravel Way)
        $request->validate([
            'target_panggol' => 'required|string',
            'nomor_usulan'   => 'required|string|max:100',
            'sk_cpns'        => 'required|file|mimes:pdf|max:5120',
            'sk_pns'         => 'required|file|mimes:pdf|max:5120',
            'pak'            => 'required|file|mimes:pdf|max:5120',
            'publikasi'      => 'nullable|file|mimes:pdf|max:10240',
        ], [
            'sk_cpns.required' => 'SK CPNS wajib diunggah.',
            'sk_pns.required'  => 'SK PNS wajib diunggah.',
            'pak.required'     => 'PAK wajib diunggah.'
        ]);

        $urutan_sekarang = $this->urutanMap[$pegawai->id_panggol ?? ''] ?? 0;
        $jenis_pegawai   = !empty($pegawai->nidn) ? 'dosen' : 'tendik';
        $urutan_min      = ($jenis_pegawai === 'dosen') ? 9 : 5;
        $urutan_target   = ($urutan_sekarang === 0) ? $urutan_min : $urutan_sekarang + 1;

        $urutanDiajukan = $this->urutanMap[$request->target_panggol] ?? 0;

        if ($urutanDiajukan !== $urutan_target) {
            return back()->with('error', 'Pangkat tidak valid. Hanya satu langkah kenaikan yang diperbolehkan.');
        }

        $jabfungSekarang = $pegawai->jabatanFungsional->jenis_jabfung ?? null;
        $jabfungToPangkat = [
            'Asisten Ahli'  => ['min' => 10, 'max' => 10],
            'Lektor'        => ['min' => 11, 'max' => 12],
            'Lektor Kepala' => ['min' => 13, 'max' => 14],
            'Guru Besar'    => ['min' => 15, 'max' => 17],
        ];

        if ($jabfungSekarang && isset($jabfungToPangkat[$jabfungSekarang])) {
            if ($urutanDiajukan > $jabfungToPangkat[$jabfungSekarang]['max']) {
                return back()->with('error', "Pangkat melebihi batas jabfung Anda ({$jabfungSekarang}).");
            }
        }

        DB::beginTransaction();
        try {
            $lastId = PengajuanKenaikan::max('id_pengajuan') ?? 0;
            
            $pengajuan = PengajuanKenaikan::create([
                'id_pengajuan'        => $lastId + 1,
                'id_pegawai'          => $pegawai->id_pegawai,
                'jenis_pengajuan'     => 'kenaikan_pangkat',
                'target_panggol'      => $request->target_panggol,
                'status_pengajuan'    => 'menunggu',
                'keterangan_tambahan' => $request->nomor_usulan,
            ]);

            $this->uploadBerkas($request, $pengajuan, $pegawai->id_pegawai);

            DB::commit();
            return redirect()->route('dosen.pangkat-golongan.index')->with('success', 'Pengajuan berhasil dikirim.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ══════════════════════════════════════
    // SHOW & EDIT & UPDATE & DESTROY (Menggunakan Eloquent)
    // ══════════════════════════════════════
    public function show($id)
    {
        $pegawai = $this->getPegawai();
        
        $data = PengajuanKenaikan::with(['berkas', 'pangkatGolongan'])
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->findOrFail($id);

        $data->nama_lengkap  = $pegawai->nama_lengkap;
        $data->nip           = $pegawai->nip;
        $data->nidn          = $pegawai->nidn;
        $data->jenis_pangkat = $data->pangkatGolongan->jenis_pangkat ?? '-';

        $berkasAda = $data->berkas->keyBy('jenis_berkas')->toArray();

        $statusMap = [
            'menunggu'          => ['label' => 'Menunggu Verifikasi',  'class' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split'],
            'verifikasi'        => ['label' => 'Sedang Diverifikasi',  'class' => 'bg-info text-dark',    'icon' => 'bi-search'],
            'persetujuan'       => ['label' => 'Menunggu Persetujuan', 'class' => 'bg-primary',           'icon' => 'bi-person-check'],
            'disetujui'         => ['label' => 'Disetujui',            'class' => 'bg-success',           'icon' => 'bi-check-circle-fill'],
            'tolak_verifikasi'  => ['label' => 'Ditolak Verifikasi',   'class' => 'bg-danger',            'icon' => 'bi-x-circle-fill'],
            'tolak_persetujuan' => ['label' => 'Ditolak Persetujuan',  'class' => 'bg-danger',            'icon' => 'bi-x-circle-fill'],
        ];
        $st = $statusMap[$data->status_pengajuan] ?? ['label' => $data->status_pengajuan, 'class' => 'bg-secondary', 'icon' => 'bi-question-circle'];

        $jenis_pegawai = !empty($pegawai->nidn) ? 'dosen' : 'tendik';
        $identitas     = ($jenis_pegawai === 'dosen') ? 'NIDN' : 'NIP';
        $idValue       = ($jenis_pegawai === 'dosen') ? ($pegawai->nidn ?? '-') : ($pegawai->nip ?? '-');

        $berkasError = [];
        $riwayat     = [];

        return view('dosen.pangkat.show', compact('data', 'berkasAda', 'riwayat', 'st', 'identitas', 'idValue', 'berkasError'));
    }

    public function edit($id)
    {
        $pegawai = $this->getPegawai();
        $mode    = request('mode', 'edit');

        $data = PengajuanKenaikan::with(['berkas', 'pangkatGolongan'])
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->findOrFail($id);

        if ($mode === 'revisi' && $data->status_pengajuan !== 'tolak_verifikasi') {
            return redirect()->route('dosen.pangkat-golongan.index')->with('error', 'Hanya pengajuan ditolak yang dapat direvisi.');
        }
        if ($mode === 'edit' && $data->status_pengajuan !== 'menunggu') {
            return redirect()->route('dosen.pangkat-golongan.index')->with('error', 'Hanya pengajuan Menunggu yang dapat diedit.');
        }

        $data->nama_lengkap       = $pegawai->nama_lengkap;
        $data->nip                = $pegawai->nip;
        $data->nidn               = $pegawai->nidn;
        $data->jenis_pangkat_skrg = $pegawai->pangkatGolongan->jenis_pangkat ?? 'Belum ada';

        $urutan_sekarang = $this->urutanMap[$pegawai->id_panggol ?? ''] ?? 0;
        $jenis_pegawai   = !empty($pegawai->nidn) ? 'dosen' : 'tendik';
        $urutan_min      = ($jenis_pegawai === 'dosen') ? 9 : 5;
        $urutan_target   = ($urutan_sekarang === 0) ? $urutan_min : $urutan_sekarang + 1;
        $sudahPuncak     = ($urutan_sekarang >= 17);

        $berkasAda = $data->berkas->keyBy('jenis_berkas')->toArray();

        $semuaPangkat = [];
        foreach (PangkatGolongan::all() as $p) {
            $urutan         = $this->urutanMap[$p->id_panggol] ?? 0;
            $p->urutan      = $urutan;
            $p->bisa        = ($urutan === $urutan_target) && !$sudahPuncak;
            $p->lebihRendah = ($urutan_sekarang > 0) && ($urutan <= $urutan_sekarang);
            $semuaPangkat[] = $p;
        }

        $jabfungSekarang = $pegawai->jabatanFungsional->jenis_jabfung ?? null;
        $jabfungToPangkat = [
            'Asisten Ahli'  => ['min' => 10, 'max' => 10],
            'Lektor'        => ['min' => 11, 'max' => 12],
            'Lektor Kepala' => ['min' => 13, 'max' => 14],
            'Guru Besar'    => ['min' => 15, 'max' => 17],
        ];
        $jabfungInfo = $jabfungSekarang ? ($jabfungToPangkat[$jabfungSekarang] ?? null) : null;

        return view('dosen.pangkat.form', compact('data', 'mode', 'berkasAda', 'semuaPangkat', 'urutan_sekarang', 'urutan_target', 'jabfungSekarang', 'jabfungInfo'));
    }

    public function update(Request $request, $id)
    {
        $pegawai = $this->getPegawai();
        $mode    = $request->mode ?? 'edit';

        $pengajuan = PengajuanKenaikan::with('berkas')->where('id_pegawai', $pegawai->id_pegawai)->findOrFail($id);
        $berkasAda = $pengajuan->berkas->keyBy('jenis_berkas');

        $request->validate([
            'target_panggol' => 'required|string',
            'nomor_usulan'   => 'required|string|max:100',
            'sk_cpns'        => ($berkasAda->has('sk_cpns') ? 'nullable' : 'required') . '|file|mimes:pdf|max:5120',
            'sk_pns'         => ($berkasAda->has('sk_pns')  ? 'nullable' : 'required') . '|file|mimes:pdf|max:5120',
            'pak'            => ($berkasAda->has('pak')     ? 'nullable' : 'required') . '|file|mimes:pdf|max:5120',
            'publikasi'      => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $urutanDiajukan  = $this->urutanMap[$request->target_panggol] ?? 0;
        $urutan_sekarang = $this->urutanMap[$pegawai->id_panggol ?? ''] ?? 0;
        $urutan_target   = ($urutan_sekarang === 0) ? 9 : $urutan_sekarang + 1;

        if ($urutanDiajukan !== $urutan_target) {
            return back()->with('error', 'Pangkat tidak valid.');
        }

        DB::beginTransaction();
        try {
            $pengajuan->update([
                'target_panggol'      => $request->target_panggol,
                'status_pengajuan'    => 'menunggu',
                'keterangan_tambahan' => $request->nomor_usulan,
            ]);

            $this->uploadBerkas($request, $pengajuan, $pegawai->id_pegawai, $berkasAda);

            DB::commit();
            return redirect()->route('dosen.pangkat-golongan.index')->with('success', 'Data berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $pegawai = $this->getPegawai();
        
        $pengajuan = PengajuanKenaikan::with('berkas')->where('id_pegawai', $pegawai->id_pegawai)->findOrFail($id);

        if (!in_array($pengajuan->status_pengajuan, ['menunggu', 'draft'])) {
            return redirect()->route('dosen.pangkat-golongan.index')->with('error', 'Hanya pengajuan berstatus Menunggu yang dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            foreach ($pengajuan->berkas as $b) {
                if (Storage::disk('public')->exists($b->file_path)) {
                    Storage::disk('public')->delete($b->file_path);
                }
            }
            $pengajuan->berkas()->delete();
            $pengajuan->delete();

            DB::commit();
            return redirect()->route('dosen.pangkat-golongan.index')->with('success', 'Pengajuan berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dosen.pangkat-golongan.index')->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}