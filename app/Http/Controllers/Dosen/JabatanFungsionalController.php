<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\PengajuanKenaikan;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Verifikasi;
use Carbon\Carbon;

class JabatanFungsionalController extends Controller
{
    // ── Hardcode data jabfung (tabel JABATAN_FUNGSIONAL kosong) ──

    private const JABFUNG_DOSEN = [
        1 => 'Asisten Ahli',
        2 => 'Lektor',
        3 => 'Lektor Kepala',
        4 => 'Guru Besar',
    ];

    private const JABFUNG_TENDIK = [
        'Pengadministrasi Akademik',
        'Pengadministrasi Keuangan',
        'Pengadministrasi Umum',
        'Pranata Komputer',
        'Pranata Laboratorium Pendidikan',
        'Pustakawan',
        'Teknisi/Laboran',
    ];

    // ── Helper: ambil pegawai login ──────────────────────────

    private function getPegawai(): Pegawai
    {
        $idPegawai = session('id_pegawai', 1);
        return Pegawai::findOrFail($idPegawai);
    }

    // ── Helper: tentukan jenis dari NIDN ─────────────────────

    private function getJenis(Pegawai $pegawai): string
    {
        return $pegawai->nidn ? 'dosen' : 'tendik';
    }

    // ── Helper: urutan jabfung dosen dari nama ────────────────

    private function getUrutanDosen(?string $namaJabfung): int
    {
        return (int) array_search($namaJabfung, self::JABFUNG_DOSEN);
    }

    // ── Helper: build data jabfung untuk view ─────────────────

    private function buildJabfungData(Pegawai $pegawai, string $jenis): array
    {
        $jabfungNow = $pegawai->jabatanFungsional;
        $namaJabfungNow = $jabfungNow?->jenis_jabfung;

        if ($jenis === 'dosen') {
            $urutanNow = $this->getUrutanDosen($namaJabfungNow);
            $urutanTarget = max(1, $urutanNow + 1);
            $maxUrutan = count(self::JABFUNG_DOSEN);
            $sudahPuncak = $urutanNow >= $maxUrutan;
            $namaTarget = self::JABFUNG_DOSEN[$urutanTarget] ?? null;

            return [
                'namaJabfungNow' => $namaJabfungNow,
                'urutanNow' => $urutanNow,
                'urutanTarget' => $urutanTarget,
                'sudahPuncak' => $sudahPuncak,
                'namaTarget' => $namaTarget,
                'semuaJabfung' => self::JABFUNG_DOSEN,   // [urutan => nama]
                'listTendik' => [],
            ];
        }

        return [
            'namaJabfungNow' => $namaJabfungNow,
            'urutanNow' => 0,
            'urutanTarget' => 0,
            'sudahPuncak' => false,
            'namaTarget' => null,
            'semuaJabfung' => [],
            'listTendik' => self::JABFUNG_TENDIK,
        ];
    }

    // ────────────────────────────────────────────────────────
    // INDEX — Daftar pengajuan jabfung milik pegawai login
    // ────────────────────────────────────────────────────────

    public function index()
    {
        $pegawai = $this->getPegawai();

        $pengajuan = PengajuanKenaikan::with(['berkas', 'verifikasi'])
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->where('jenis_pengajuan', 'jabfung')
            ->orderByDesc('id_pengajuan')
            ->get();

        $namaJabfungNow = $pegawai->jabatanFungsional?->jenis_jabfung;

        return view('dosen.jabatanfungsional.readjabatanfungsional', compact(
            'pegawai',
            'pengajuan',
            'namaJabfungNow'
        ));
    }

    // ────────────────────────────────────────────────────────
    // CREATE — Form pengajuan baru
    // ────────────────────────────────────────────────────────

    public function create()
    {
        $pegawai = $this->getPegawai();
        $jenis = $this->getJenis($pegawai);

        // Perbaikan logika: Gunakan in_array atau pengecekan yang benar
        if (!in_array($pegawai->status_pegawai, ['ASN', 'PNS'])) {
            return redirect()->route('dosen.jabatanfungsional.index')
                ->with('error', 'Fitur ini hanya untuk pegawai berstatus ASN / PNS.');
        }

        $data = $this->buildJabfungData($pegawai, $jenis);

        $adaPending = PengajuanKenaikan::where('id_pegawai', $pegawai->id_pegawai)
            ->where('jenis_pengajuan', 'jabfung')
            ->whereIn('status_pengajuan', ['menunggu', 'verifikasi', 'persetujuan'])
            ->exists();

        return view('dosen.jabatanfungsional.formjabatanfungsional', array_merge(
            compact('pegawai', 'jenis', 'adaPending'),
            $data
        ));
    }

    // ────────────────────────────────────────────────────────
    // STORE — Simpan pengajuan baru
    // ────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $pegawai = $this->getPegawai();
        $jenis = $this->getJenis($pegawai);
        $data = $this->buildJabfungData($pegawai, $jenis);

        $request->validate([
            'nama_jabfung_target' => 'required|string',
            'nomor_usulan' => 'required|string|max:100',
            'sk_cpns' => 'required|file|mimes:pdf|max:5120',
            'sk_pns' => 'required|file|mimes:pdf|max:5120',
            'pak' => 'required|file|mimes:pdf|max:5120',
            'publikasi' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $namaTarget = $request->nama_jabfung_target;

        if ($jenis === 'dosen') {
            if ($namaTarget !== ($data['namaTarget'] ?? null)) {
                return back()->withErrors(['nama_jabfung_target' => 'Dosen hanya bisa naik satu jenjang.']);
            }
        }

        if ($jenis === 'tendik') {
            if ($namaTarget === $data['namaJabfungNow']) {
                return back()->withErrors(['nama_jabfung_target' => 'Tidak bisa mengajukan jabfung yang sama.']);
            }
            if (!in_array($namaTarget, self::JABFUNG_TENDIK)) {
                return back()->withErrors(['nama_jabfung_target' => 'Jabatan fungsional tidak valid.']);
            }
        }

        DB::beginTransaction();
        try {
            $lastId = DB::table('PENGAJUAN_KENAIKAN')->max('id_pengajuan') ?? 0;
            $pengajuan = PengajuanKenaikan::create([
                'id_pengajuan' => $lastId + 1,
                'id_pegawai' => $pegawai->id_pegawai,
                'jenis_pengajuan' => 'jabfung',
                'target_jabfung' => $pegawai->id_jabfung,
                'keterangan_tambahan' => $namaTarget,
                'nomor_usulan' => $request->nomor_usulan,
                'status_pengajuan' => 'menunggu',
            ]);

            $this->uploadBerkas($request, $pengajuan, $pegawai->id_pegawai);

            $berkasUtama = \App\Models\Berkas::where('id_pengajuan', $pengajuan->id_pengajuan)->first();

            if ($berkasUtama) {
                Verifikasi::create([
                    'id_berkas' => $berkasUtama->id_berkas,
                    'jenis_verifikasi' => Verifikasi::JENIS_JABFUNG, // Pastikan konstanta ini ada di model
                    'status_verifikasi' => 'Menunggu Diproses',
                    'tanggal_pengajuan' => Carbon::today(),
                    'tanggal_proses' => null,
                    'keterangan' => '-',
                ]);
            }

            DB::commit();
            return redirect()->route('dosen.jabatanfungsional.index')->with('success', 'sukses');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dosen.jabatanfungsional.index')->with('error', $e->getMessage());
        }
    }

    // ────────────────────────────────────────────────────────
    // EDIT — Form edit pengajuan (status = menunggu)
    // ────────────────────────────────────────────────────────

    public function edit($id)
    {
        $pegawai = $this->getPegawai();
        $jenis = $this->getJenis($pegawai);
        $pengajuan = PengajuanKenaikan::with(['berkas', 'verifikasi'])
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->where('jenis_pengajuan', 'jabfung')
            ->findOrFail($id);

        if ($pengajuan->status_pengajuan !== 'menunggu') {
            return redirect()->route('dosen.jabatanfungsional.index')
                ->with('error', 'Hanya pengajuan berstatus Menunggu yang dapat diedit.');
        }

        $data = $this->buildJabfungData($pegawai, $jenis);
        $berkasAda = $pengajuan->berkas->keyBy('jenis_berkas');

        return view('dosen.jabatanfungsional.formjabatanfungsional', array_merge(
            compact('pegawai', 'jenis', 'pengajuan', 'berkasAda'),
            $data
        ));
    }

    // ────────────────────────────────────────────────────────
    // UPDATE — Simpan perubahan pengajuan
    // ────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $pegawai = $this->getPegawai();
        $jenis = $this->getJenis($pegawai);
        $pengajuan = PengajuanKenaikan::where('id_pegawai', $pegawai->id_pegawai)
            ->where('jenis_pengajuan', 'jabfung')
            ->findOrFail($id);

        if ($pengajuan->status_pengajuan !== 'menunggu') {
            return redirect()->route('dosen.jabatanfungsional.index')
                ->with('error', 'Pengajuan ini tidak bisa diedit.');
        }

        $berkasAda = $pengajuan->berkas->keyBy('jenis_berkas');
        $data = $this->buildJabfungData($pegawai, $jenis);

        $rules = [
            'nama_jabfung_target' => 'required|string',
            'nomor_usulan' => 'required|string|max:100',
            'sk_cpns' => ($berkasAda->has('sk_cpns') ? 'nullable' : 'required') . '|file|mimes:pdf|max:5120',
            'sk_pns' => ($berkasAda->has('sk_pns') ? 'nullable' : 'required') . '|file|mimes:pdf|max:5120',
            'pak' => ($berkasAda->has('pak') ? 'nullable' : 'required') . '|file|mimes:pdf|max:5120',
            'publikasi' => 'nullable|file|mimes:pdf|max:10240',
        ];
        $request->validate($rules);

        $namaTarget = $request->nama_jabfung_target;

        if ($jenis === 'dosen' && $namaTarget !== ($data['namaTarget'] ?? null)) {
            return back()->withErrors(['nama_jabfung_target' => 'Dosen hanya bisa naik satu jenjang.']);
        }

        DB::beginTransaction();
        try {
            $pengajuan->update([
                'keterangan_tambahan' => $namaTarget,
                'nomor_usulan' => $request->nomor_usulan,
                'status_pengajuan' => 'menunggu',
            ]);

            $this->uploadBerkas($request, $pengajuan, $pegawai->id_pegawai, $berkasAda);

            DB::commit();
            return redirect()->route('dosen.jabatanfungsional.index')->with('success', 'perbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dosen.jabatanfungsional.index')->with('error', $e->getMessage());
        }
    }

    // ────────────────────────────────────────────────────────
    // REVISI — Form revisi (status = tolak_verifikasi)
    // ────────────────────────────────────────────────────────

    public function revisi($id)
    {
        $pegawai = $this->getPegawai();
        $jenis = $this->getJenis($pegawai);
        $pengajuan = PengajuanKenaikan::with(['berkas', 'verifikasi'])
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->where('jenis_pengajuan', 'jabfung')
            ->findOrFail($id);

        if ($pengajuan->status_pengajuan !== 'tolak_verifikasi') {
            return redirect()->route('dosen.jabatanfungsional.index')
                ->with('error', 'Hanya pengajuan yang ditolak yang dapat direvisi.');
        }

        $data = $this->buildJabfungData($pegawai, $jenis);
        $berkasAda = $pengajuan->berkas->keyBy('jenis_berkas');
        $berkasBermasalah = $pengajuan->berkas_bermasalah;

        return view('dosen.jabatanfungsional.formjabatanfungsional', array_merge(
            compact('pegawai', 'jenis', 'pengajuan', 'berkasAda', 'berkasBermasalah'),
            $data
        ));
    }

    // ────────────────────────────────────────────────────────
    // SIMPAN REVISI
    // ────────────────────────────────────────────────────────

    public function simpanRevisi(Request $request, $id)
    {
        $pegawai = $this->getPegawai();
        $pengajuan = PengajuanKenaikan::with(['berkas', 'verifikasi'])
            ->where('id_pegawai', $pegawai->id_pegawai)
            ->where('jenis_pengajuan', 'jabfung')
            ->findOrFail($id);

        if ($pengajuan->status_pengajuan !== 'tolak_verifikasi') {
            return redirect()->route('dosen.jabatanfungsional.index')
                ->with('error', 'Pengajuan ini tidak bisa direvisi.');
        }

        $berkasBermasalah = $pengajuan->berkas_bermasalah;

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

            $verifikasiTerakhir = $pengajuan->verifikasi->sortByDesc('id_verifikasi')->first();
            if ($verifikasiTerakhir) {
                $verifikasiTerakhir->update(['berkas_bermasalah' => null]);
            }

            $pengajuan->update([
                'status_pengajuan' => 'menunggu',
            ]);

            DB::commit();
            return redirect()->route('dosen.jabatanfungsional.index')->with('success', 'revisi');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dosen.jabatanfungsional.index')->with('error', $e->getMessage());
        }
    }

    // ────────────────────────────────────────────────────────
    // DESTROY — Hapus pengajuan (hanya status menunggu)
    // ────────────────────────────────────────────────────────

    public function destroy($id)
    {
        $pegawai = $this->getPegawai();
        $pengajuan = PengajuanKenaikan::where('id_pegawai', $pegawai->id_pegawai)
            ->where('jenis_pengajuan', 'jabfung')
            ->findOrFail($id);

        if ($pengajuan->status_pengajuan !== 'menunggu') {
            return redirect()->route('dosen.jabatanfungsional.index')
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
            return redirect()->route('dosen.jabatanfungsional.index')->with('success', 'hapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dosen.jabatanfungsional.index')->with('error', $e->getMessage());
        }
    }

    // ────────────────────────────────────────────────────────
    // PRIVATE — Helper upload berkas
    // ────────────────────────────────────────────────────────

    private function uploadBerkas(
        Request $request,
        PengajuanKenaikan $pengajuan,
        $idPegawai,
        $berkasAda = null,
        $onlyKeys = null
    ): void {
        $berkasConfig = [
            'sk_cpns' => ['label' => 'SK CPNS', 'max' => 5],
            'sk_pns' => ['label' => 'SK PNS', 'max' => 5],
            'pak' => ['label' => 'PAK', 'max' => 5],
            'publikasi' => ['label' => 'Publikasi', 'max' => 10],
        ];

        foreach ($berkasConfig as $key => $cfg) {
            if ($onlyKeys !== null && !in_array($key, (array) $onlyKeys))
                continue;
            if (!$request->hasFile($key))
                continue;

            $file = $request->file($key);
            $namaFile = $key . '_' . $idPegawai . '_' . time() . '.pdf';
            $path = $file->storeAs("berkas_jabfung/{$pengajuan->id_pengajuan}", $namaFile, 'public');

            if ($berkasAda && $berkasAda->has($key)) {
                $lama = $berkasAda->get($key);
                if (Storage::disk('public')->exists($lama->file_path)) {
                    Storage::disk('public')->delete($lama->file_path);
                }
                $lama->update([
                    'nama_berkas' => $cfg['label'],
                    'jenis_berkas' => $key,
                    'file_path' => $path,
                ]);
            } else {
                // id_berkas auto increment — tidak perlu di-set manual
                Berkas::create([
                    'nama_berkas' => $cfg['label'],
                    'jenis_berkas' => $key,
                    'file_path' => $path,
                    'id_pegawai' => $idPegawai,
                    'id_pengajuan' => $pengajuan->id_pengajuan,
                ]);
            }
        }
    }
}