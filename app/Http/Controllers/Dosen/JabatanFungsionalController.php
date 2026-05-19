<?php

namespace App\Http\Controllers\Dosen;
use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\JabatanFungsional;
use App\Models\PengajuanKenaikan;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JabatanFungsionalController extends Controller
{
    // ── Helper: ambil pegawai login ──────────────────────────
    private function getPegawai()
    {
        $idPegawai = session('id_pegawai', 5);
        return Pegawai::with('jabatanFungsional')->findOrFail($idPegawai);
    }

    // ── Helper: tentukan jenis dari NIDN ─────────────────────
    private function getJenis($pegawai): string
    {
        return $pegawai->nidn ? 'dosen' : 'tendik';
    }

    // ────────────────────────────────────────────────────────
    // READ — Daftar pengajuan + detail (digabung, tanpa iframe)
    // ────────────────────────────────────────────────────────
    public function index()
    {
        $pegawai = $this->getPegawai();

        $pengajuan = PengajuanKenaikan::with(['jabatanFungsional', 'berkas'])
            ->jabfung()
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->orderByDesc('id_pengajuan')
            ->get();

        return view('jabatanfungsional.readjabatanfungsional', compact('pegawai', 'pengajuan'));
    }

    // ────────────────────────────────────────────────────────
    // SHOW — Detail pengajuan (inline, bukan iframe)
    // ────────────────────────────────────────────────────────
    public function show($id)
    {
        $pegawai = $this->getPegawai();
        $jenis   = $this->getJenis($pegawai);

        $pengajuan = PengajuanKenaikan::with(['jabatanFungsional', 'berkas', 'pegawai.jabatanFungsional'])
            ->jabfung()
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->findOrFail($id);

        $dosenLadder = [];
        if ($jenis === 'dosen') {
            $dosenLadder = JabatanFungsional::dosen()->get();
        }

        $pengajuan = PengajuanKenaikan::with(['jabatanFungsional', 'berkas'])
            ->jabfung()
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->orderByDesc('id_pengajuan')
            ->get();

        return view('jabatanfungsional.readjabatanfungsional', compact(
            'pegawai', 'pengajuan', 'dosenLadder'
        ));
    }

    // ────────────────────────────────────────────────────────
    // CREATE — Form pengajuan baru
    // ────────────────────────────────────────────────────────
    public function create()
    {
        $pegawai = $this->getPegawai();
        $jenis   = $this->getJenis($pegawai);

        if ($pegawai->status_pegawai !== 'ASN') {
            return redirect()->route('jabatanfungsional.index')
                ->with('error', 'Fitur ini hanya untuk pegawai berstatus ASN.');
        }

        $jabfungNow = $pegawai->jabatanFungsional;
        $urutanNow  = $jabfungNow ? (int) $jabfungNow->urutan : 0;

        $maxUrutan   = JabatanFungsional::dosen()->max('urutan') ?? 4;
        $sudahPuncak = ($jenis === 'dosen' && $urutanNow >= $maxUrutan);

        $semuaJabfung = ($jenis === 'dosen')
            ? JabatanFungsional::dosen()->get()
            : JabatanFungsional::tendik()->get();

        $urutanTarget  = ($jenis === 'dosen') ? max(1, $urutanNow + 1) : null;
        $jabfungTarget = ($jenis === 'dosen')
            ? $semuaJabfung->firstWhere('urutan', $urutanTarget)
            : null;

        $adaPending = PengajuanKenaikan::jabfung()
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->whereIn('status_pengajuan', ['menunggu', 'verifikasi', 'persetujuan'])
            ->exists();

        return view('jabatanfungsional.formjabatanfungsional', compact(
            'pegawai', 'jenis', 'jabfungNow', 'urutanNow',
            'sudahPuncak', 'semuaJabfung', 'urutanTarget',
            'jabfungTarget', 'adaPending'
        ));
    }

    // ────────────────────────────────────────────────────────
    // STORE — Simpan pengajuan baru
    // ────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $pegawai      = $this->getPegawai();
        $jenis        = $this->getJenis($pegawai);
        $jabfungNow   = $pegawai->jabatanFungsional;
        $urutanNow    = $jabfungNow ? (int) $jabfungNow->urutan : 0;
        $urutanTarget = max(1, $urutanNow + 1);

        $request->validate([
            'id_jabfung'   => 'required|exists:JABATAN_FUNGSIONAL,id_jabfung',
            'nomor_usulan' => 'required|string|max:100',
            'sk_cpns'      => 'required|file|mimes:pdf|max:5120',
            'sk_pns'       => 'required|file|mimes:pdf|max:5120',
            'pak'          => 'required|file|mimes:pdf|max:5120',
            'publikasi'    => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $jabfungBaru = JabatanFungsional::findOrFail($request->id_jabfung);

        if ($jenis === 'dosen' && (int) $jabfungBaru->urutan !== $urutanTarget) {
            return back()->withErrors(['id_jabfung' => 'Dosen hanya bisa naik satu jenjang.']);
        }
        if ($jenis === 'tendik' && $jabfungBaru->jenis_jabfung !== 'tendik') {
            return back()->withErrors(['id_jabfung' => 'Jabatan fungsional tidak valid untuk tendik.']);
        }
        if ($jabfungNow && $jabfungBaru->id_jabfung === $jabfungNow->id_jabfung) {
            return back()->withErrors(['id_jabfung' => 'Tidak bisa mengajukan jabfung yang sama.']);
        }

        DB::beginTransaction();
        try {
            $lastId = DB::table('pengajuan_kenaikan')->max('id_pengajuan') ?? 0;

$pengajuan = PengajuanKenaikan::create([
    'id_pengajuan'      => $lastId + 1,
    'id_pegawai'        => $pegawai->id_pegawai,
                'jenis_pengajuan'   => 'jabfung',
                'target_jabfung'    => $jabfungBaru->id_jabfung,
                'status_pengajuan'  => 'menunggu',
                'nomor_usulan'      => $request->nomor_usulan,
                'tanggal_pengajuan' => now()->toDateString(),
            ]);

            $this->uploadBerkas($request, $pengajuan, $pegawai->id_pegawai);

            DB::commit();
            return redirect()->route('jabatanfungsional.index')->with('success', 'sukses');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('jabatanfungsional.index')->with('error', $e->getMessage());
        }
    }

    // ────────────────────────────────────────────────────────
    // EDIT — Form edit pengajuan (status = menunggu)
    // ────────────────────────────────────────────────────────
    public function edit($id)
    {
        $pegawai   = $this->getPegawai();
        $jenis     = $this->getJenis($pegawai);
        $pengajuan = PengajuanKenaikan::with(['jabatanFungsional', 'berkas'])
            ->jabfung()
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->findOrFail($id);

        if ($pengajuan->status_pengajuan !== 'menunggu') {
            return redirect()->route('jabatanfungsional.index')
                ->with('error', 'Hanya pengajuan berstatus Menunggu yang dapat diedit.');
        }

        $jabfungNow   = $pegawai->jabatanFungsional;
        $urutanNow    = $jabfungNow ? (int) $jabfungNow->urutan : 0;
        $urutanTarget = max(1, $urutanNow + 1);

        $semuaJabfung = ($jenis === 'dosen')
            ? JabatanFungsional::dosen()->get()
            : JabatanFungsional::tendik()->get();

        $berkasAda = $pengajuan->berkas->keyBy('jenis_berkas');

        return view('jabatanfungsional.formjabatanfungsional', compact(
            'pegawai', 'pengajuan', 'jenis', 'jabfungNow',
            'urutanNow', 'urutanTarget', 'semuaJabfung', 'berkasAda'
        ));
    }

    // ────────────────────────────────────────────────────────
    // UPDATE — Simpan perubahan pengajuan (status menunggu)
    // ────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $pegawai   = $this->getPegawai();
        $jenis     = $this->getJenis($pegawai);
        $pengajuan = PengajuanKenaikan::jabfung()
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->findOrFail($id);

        if ($pengajuan->status_pengajuan !== 'menunggu') {
            return redirect()->route('jabatanfungsional.index')
                ->with('error', 'Pengajuan ini tidak bisa diedit.');
        }

        $jabfungNow   = $pegawai->jabatanFungsional;
        $urutanNow    = $jabfungNow ? (int) $jabfungNow->urutan : 0;
        $urutanTarget = max(1, $urutanNow + 1);

        $berkasAda = $pengajuan->berkas->keyBy('jenis_berkas');

        $rules = [
            'id_jabfung'   => 'required|exists:JABATAN_FUNGSIONAL,id_jabfung',
            'nomor_usulan' => 'required|string|max:100',
            'sk_cpns'      => ($berkasAda->has('sk_cpns')  ? 'nullable' : 'required') . '|file|mimes:pdf|max:5120',
            'sk_pns'       => ($berkasAda->has('sk_pns')   ? 'nullable' : 'required') . '|file|mimes:pdf|max:5120',
            'pak'          => ($berkasAda->has('pak')       ? 'nullable' : 'required') . '|file|mimes:pdf|max:5120',
            'publikasi'    => 'nullable|file|mimes:pdf|max:10240',
        ];
        $request->validate($rules);

        $jabfungBaru = JabatanFungsional::findOrFail($request->id_jabfung);

        if ($jenis === 'dosen' && (int) $jabfungBaru->urutan !== $urutanTarget) {
            return back()->withErrors(['id_jabfung' => 'Dosen hanya bisa naik satu jenjang.']);
        }

        DB::beginTransaction();
        try {
            $pengajuan->update([
                'target_jabfung'   => $jabfungBaru->id_jabfung,
                'nomor_usulan'     => $request->nomor_usulan,
                'status_pengajuan' => 'menunggu',
            ]);

            $this->uploadBerkas($request, $pengajuan, $pegawai->id_pegawai, $berkasAda);

            DB::commit();
            return redirect()->route('jabatanfungsional.index')->with('success', 'perbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('jabatanfungsional.index')->with('error', $e->getMessage());
        }
    }

    // ────────────────────────────────────────────────────────
    // REVISI — Form revisi (status = tolak_verifikasi)
    // ────────────────────────────────────────────────────────
    public function revisi($id)
    {
        $pegawai   = $this->getPegawai();
        $jenis     = $this->getJenis($pegawai);
        $pengajuan = PengajuanKenaikan::with(['jabatanFungsional', 'berkas'])
            ->jabfung()
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->findOrFail($id);

        if ($pengajuan->status_pengajuan !== 'tolak_verifikasi') {
            return redirect()->route('jabatanfungsional.index')
                ->with('error', 'Hanya pengajuan yang ditolak yang dapat direvisi.');
        }

        $jabfungNow   = $pegawai->jabatanFungsional;
        $urutanNow    = $jabfungNow ? (int) $jabfungNow->urutan : 0;
        $urutanTarget = max(1, $urutanNow + 1);

        $semuaJabfung = ($jenis === 'dosen')
            ? JabatanFungsional::dosen()->get()
            : JabatanFungsional::tendik()->get();

        $berkasAda        = $pengajuan->berkas->keyBy('jenis_berkas');
        $berkasBermasalah = $pengajuan->berkas_bermasalah ?? [];

        return view('jabatanfungsional.formjabatanfungsional', compact(
            'pegawai', 'pengajuan', 'jenis', 'jabfungNow',
            'urutanNow', 'urutanTarget', 'semuaJabfung',
            'berkasAda', 'berkasBermasalah'
        ));
    }

    // ────────────────────────────────────────────────────────
    // SIMPAN REVISI
    // ────────────────────────────────────────────────────────
    public function simpanRevisi(Request $request, $id)
    {
        $pegawai   = $this->getPegawai();
        $pengajuan = PengajuanKenaikan::jabfung()
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->findOrFail($id);

        if ($pengajuan->status_pengajuan !== 'tolak_verifikasi') {
            return redirect()->route('jabatanfungsional.index')
                ->with('error', 'Pengajuan ini tidak bisa direvisi.');
        }

        $berkasBermasalah = $pengajuan->berkas_bermasalah ?? [];

        $rules = ['publikasi' => 'nullable|file|mimes:pdf|max:10240'];
        foreach (['sk_cpns', 'sk_pns', 'pak'] as $key) {
            if (in_array($key, $berkasBermasalah)) {
                $rules[$key] = 'required|file|mimes:pdf|max:5120';
            }
        }
        $request->validate($rules);

        $berkasAda = $pengajuan->berkas->keyBy('jenis_berkas');

        DB::beginTransaction();
        try {
            $this->uploadBerkas($request, $pengajuan, $pegawai->id_pegawai, $berkasAda, $berkasBermasalah);

            $pengajuan->update([
                'status_pengajuan'    => 'menunggu',
                'berkas_bermasalah'   => null,
                'keterangan_tambahan' => null,
            ]);

            DB::commit();
            return redirect()->route('jabatanfungsional.index')->with('success', 'revisi');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('jabatanfungsional.index')->with('error', $e->getMessage());
        }
    }

    // ────────────────────────────────────────────────────────
    // DESTROY — Hapus pengajuan (hanya status menunggu)
    // ────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $pegawai   = $this->getPegawai();
        $pengajuan = PengajuanKenaikan::jabfung()
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->findOrFail($id);

        if ($pengajuan->status_pengajuan !== 'menunggu') {
            return redirect()->route('jabatanfungsional.index')
                ->with('error', 'Pengajuan yang sudah diproses tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            foreach ($pengajuan->berkas as $b) {
                if ($b->file_path && Storage::disk('public')->exists($b->file_path)) {
                    Storage::disk('public')->delete($b->file_path);
                }
            }
            $pengajuan->berkas()->delete();
            $pengajuan->delete();

            DB::commit();
            return redirect()->route('jabatanfungsional.index')->with('success', 'hapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('jabatanfungsional.index')->with('error', $e->getMessage());
        }
    }

    // ────────────────────────────────────────────────────────
    // PRIVATE — Helper upload berkas
    // ────────────────────────────────────────────────────────
    private function uploadBerkas(Request $request, PengajuanKenaikan $pengajuan, $idPegawai, $berkasAda = null, $onlyKeys = null)
    {
        $berkasConfig = [
            'sk_cpns'   => ['label' => 'SK CPNS',  'max' => 5],
            'sk_pns'    => ['label' => 'SK PNS',   'max' => 5],
            'pak'       => ['label' => 'PAK',       'max' => 5],
            'publikasi' => ['label' => 'Publikasi', 'max' => 10],
        ];

        foreach ($berkasConfig as $key => $cfg) {
            if ($onlyKeys !== null && !in_array($key, $onlyKeys)) continue;
            if (!$request->hasFile($key)) continue;

            $file     = $request->file($key);
            $idBerkas = 'BRK-' . strtoupper($key) . '-' . $idPegawai . '-' . time();
            $namaFile = $key . '_' . $idPegawai . '_' . time() . '.pdf';
            $path     = $file->storeAs("berkas_jabfung/{$pengajuan->id_pengajuan}", $namaFile, 'public');

            if ($berkasAda && $berkasAda->has($key)) {
                $lama = $berkasAda->get($key);
                if (Storage::disk('public')->exists($lama->file_path)) {
                    Storage::disk('public')->delete($lama->file_path);
                }
                $lama->update([
                    'nama_berkas'  => $cfg['label'],
                    'jenis_berkas' => $key,
                    'file_path'    => $path,
                ]);
            } else {
                Berkas::create([
                    'id_berkas'    => $idBerkas,
                    'nama_berkas'  => $cfg['label'],
                    'jenis_berkas' => $key,
                    'file_path'    => $path,
                    'id_pegawai'   => $idPegawai,
                    'id_pengajuan' => $pengajuan->id_pengajuan,
                    'id_jabfung'   => $pengajuan->target_jabfung,
                ]);
            }
        }
    }
}