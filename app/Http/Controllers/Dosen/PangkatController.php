<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
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
    // Helper: upload file
    // ══════════════════════════════════════
    private function uploadFile($file, $maxMB = 5)
    {
        if (!$file || !$file->isValid()) return null;

        if ($file->getSize() > $maxMB * 1024 * 1024) {
            throw new \Exception("File melebihi batas {$maxMB}MB.");
        }
        if ($file->getMimeType() !== 'application/pdf') {
            throw new \Exception("File harus berformat PDF.");
        }

        $namaFile = time() . '_' . uniqid() . '.pdf';
        $file->storeAs('uploads/pangkat', $namaFile, 'public');

        return [
            'nama_berkas' => $namaFile,
            'file_path' => '/storage/uploads/pangkat/' . $namaFile,
            'ukuran'      => $file->getSize(),
        ];
    }

    // ══════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════
    public function index()
    {
        $id_pegawai_login = 5;

        $pegawai = DB::selectOne("
            SELECT p.id_pegawai, p.nama_lengkap, p.status_pegawai,
                   p.nidn, p.nip, p.id_panggol,
                   pg.id_panggol AS kode_pangkat, pg.jenis_pangkat
            FROM pegawai p
            LEFT JOIN pangkat_golongan pg ON p.id_panggol = pg.id_panggol
            WHERE p.id_pegawai = ?
        ", [$id_pegawai_login]);

        $pengajuan = DB::select("
            SELECT pp.*,
                   pg.id_panggol AS kode_pangkat, pg.jenis_pangkat
            FROM pengajuan_kenaikan pp
            JOIN pangkat_golongan pg ON pp.target_panggol = pg.id_panggol
            WHERE pp.id_pegawai = ?
            ORDER BY pp.id_pengajuan DESC
        ", [$id_pegawai_login]);

        foreach ($pengajuan as $p) {
            $p->berkas = collect(DB::select("
                SELECT jenis_berkas, nama_berkas, file_path
                FROM berkas WHERE id_pengajuan = ?
            ", [$p->id_pengajuan]));

            $p->status_pengajuan = strtoupper($p->status_pengajuan);
        }

        return view('dosen.pangkat.index', compact('pegawai', 'pengajuan'));
    }

    // ══════════════════════════════════════
    // CREATE
    // ══════════════════════════════════════
    public function create()
    {
        $id_pegawai_login = 5;

        $pegawai = DB::selectOne("
            SELECT p.id_pegawai, p.nama_lengkap, p.nidn, p.nip,
                   p.status_pegawai, p.id_panggol, p.id_jabfung,
                   pg.jenis_pangkat,
                   jf.jenis_jabfung
            FROM pegawai p
            LEFT JOIN pangkat_golongan pg ON p.id_panggol = pg.id_panggol
            LEFT JOIN jabatan_fungsional jf ON p.id_jabfung = jf.id_jabfung
            WHERE p.id_pegawai = ?
        ", [$id_pegawai_login]);

        if (!$pegawai || strtolower($pegawai->status_pegawai) !== 'pns') {
            return redirect()->route('pangkat-golongan.index')
                             ->with('error', 'Hanya pegawai PNS yang dapat mengajukan kenaikan pangkat.');
        }

        $urutan_sekarang = $this->urutanMap[$pegawai->id_panggol ?? ''] ?? 0;
        $jenis_pegawai   = !empty($pegawai->nidn) ? 'dosen' : 'tendik';
        $urutan_min      = ($jenis_pegawai === 'dosen') ? 9 : 5;
        $urutan_target   = ($urutan_sekarang === 0) ? $urutan_min : $urutan_sekarang + 1;
        $sudahPuncak     = ($urutan_sekarang >= 17);

        // FIX: pakai status_pengajuan bukan status
        $adaPending = DB::selectOne("
            SELECT COUNT(*) as total FROM pengajuan_kenaikan
            WHERE id_pegawai = ? AND status_pengajuan IN ('menunggu','verifikasi','persetujuan')
        ", [$id_pegawai_login])->total > 0;

        $semuaPangkat = DB::select("
            SELECT id_panggol, jenis_pangkat
            FROM pangkat_golongan
            ORDER BY FIELD(id_panggol,
                'I/a','I/b','I/c','I/d',
                'II/a','II/b','II/c','II/d',
                'III/a','III/b','III/c','III/d',
                'IV/a','IV/b','IV/c','IV/d','IV/e')
        ");

        $semuaPangkat = array_map(function($p) use ($urutan_target, $urutan_sekarang, $sudahPuncak) {
            $urutan         = $this->urutanMap[$p->id_panggol] ?? 0;
            $p->urutan      = $urutan;
            $p->bisa        = ($urutan === $urutan_target) && !$sudahPuncak;
            $p->lebihRendah = ($urutan_sekarang > 0) && ($urutan <= $urutan_sekarang);
            return $p;
        }, $semuaPangkat);

        $jabfungToPangkat = [
            'Asisten Ahli'  => ['min' => 10, 'max' => 10, 'label' => 'III/b'],
            'Lektor'        => ['min' => 11, 'max' => 12, 'label' => 'III/c – III/d'],
            'Lektor Kepala' => ['min' => 13, 'max' => 14, 'label' => 'IV/a – IV/b'],
            'Guru Besar'    => ['min' => 15, 'max' => 17, 'label' => 'IV/c – IV/e'],
        ];

        $jabfungSekarang = $pegawai->jenis_jabfung ?? null;
        $jabfungInfo     = $jabfungSekarang ? ($jabfungToPangkat[$jabfungSekarang] ?? null) : null;

        $identitas_label = ($jenis_pegawai === 'dosen') ? 'NIDN' : 'NIP';
        $identitas_value = ($jenis_pegawai === 'dosen') ? ($pegawai->nidn ?? '-') : ($pegawai->nip ?? '-');

        return view('dosen.pangkat.create', compact(
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
        $id_pegawai     = 5;
        $target_panggol = $request->target_panggol;
        $nomor_usulan   = trim($request->nomor_usulan ?? '');

        $pegawai = DB::selectOne("
            SELECT p.id_pegawai, p.status_pegawai, p.id_panggol, p.nidn, p.nip
            FROM pegawai p
            WHERE p.id_pegawai = ?
        ", [$id_pegawai]);

        if (!$pegawai) return back()->with('error', 'Data pegawai tidak ditemukan.');

        if (strtolower($pegawai->status_pegawai) !== 'pns') {
            return back()->with('error', 'Hanya pegawai PNS yang dapat mengajukan kenaikan pangkat.');
        }

        $urutan_sekarang = $this->urutanMap[$pegawai->id_panggol ?? ''] ?? 0;
        $jenis_pegawai   = !empty($pegawai->nidn) ? 'dosen' : 'tendik';
        $urutan_min      = ($jenis_pegawai === 'dosen') ? 9 : 5;
        $urutan_target   = ($urutan_sekarang === 0) ? $urutan_min : $urutan_sekarang + 1;
        $sudahPuncak     = ($urutan_sekarang >= 17);

        if ($sudahPuncak) return back()->with('error', 'Pangkat sudah berada di level tertinggi (IV/e).');
        if (empty($target_panggol)) return back()->with('error', 'Pangkat yang diajukan harus dipilih.');

        $pangkatDiajukan = DB::selectOne(
            "SELECT id_panggol, jenis_pangkat FROM pangkat_golongan WHERE id_panggol = ?",
            [$target_panggol]
        );

        if (!$pangkatDiajukan) return back()->with('error', 'Pangkat tidak ditemukan.');

        $urutanDiajukan = $this->urutanMap[$pangkatDiajukan->id_panggol] ?? 0;
        if ($urutanDiajukan !== $urutan_target) {
            return back()->with('error', 'Pangkat tidak valid. Hanya satu langkah kenaikan yang diperbolehkan.');
        }

        // ── Validasi jabfung ──────────────────────────────────────
        $jabfungToPangkat = [
            'Asisten Ahli'  => ['min' => 10, 'max' => 10],
            'Lektor'        => ['min' => 11, 'max' => 12],
            'Lektor Kepala' => ['min' => 13, 'max' => 14],
            'Guru Besar'    => ['min' => 15, 'max' => 17],
        ];

        // Ambil jabfung pegawai
        $jabfung = DB::selectOne("
            SELECT jf.jenis_jabfung
            FROM pegawai p
            LEFT JOIN jabatan_fungsional jf ON p.id_jabfung = jf.id_jabfung
            WHERE p.id_pegawai = ?
        ", [$id_pegawai]);

        $jabfungSekarang = $jabfung->jenis_jabfung ?? null;

        if ($jabfungSekarang && isset($jabfungToPangkat[$jabfungSekarang])) {
            $maxDiizinkan = $jabfungToPangkat[$jabfungSekarang]['max'];
            if ($urutanDiajukan > $maxDiizinkan) {
                return back()->with('error', 
                    "Pangkat yang diajukan melebihi batas jabfung Anda ({$jabfungSekarang}). " .
                    "Silakan ajukan kenaikan jabfung terlebih dahulu."
                );
            }
        }
        // ─────────────────────────────────────────────────────────

        // FIX: pakai status_pengajuan
        $pending = DB::selectOne("
            SELECT COUNT(*) as total FROM pengajuan_kenaikan
            WHERE id_pegawai = ? AND status_pengajuan IN ('menunggu','verifikasi','persetujuan')
        ", [$id_pegawai]);

        if ($pending->total > 0) return back()->with('error', 'Masih ada pengajuan yang sedang diproses.');
        if (empty($nomor_usulan)) return back()->with('error', 'Nomor SK/Usulan wajib diisi.');

        try {
            $skCpns    = $this->uploadFile($request->file('sk_cpns'),   5);
            $skPns     = $this->uploadFile($request->file('sk_pns'),    5);
            $pak       = $this->uploadFile($request->file('pak'),       5);
            $publikasi = $this->uploadFile($request->file('publikasi'), 10);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        if (!$skCpns || !$skPns || !$pak) {
            return back()->with('error', 'SK CPNS, SK PNS, dan PAK wajib diunggah.');
        }

        DB::beginTransaction();
        try {
            $id_pengajuan = DB::table('pengajuan_kenaikan')->insertGetId([
            'id_pegawai'          => $id_pegawai,
            'jenis_pengajuan'     => 'kenaikan_pangkat',
            'target_panggol'      => $target_panggol,
            'status_pengajuan'    => 'menunggu',
            'keterangan_tambahan' => $nomor_usulan,
            'tanggal_pengajuan'   => now()->toDateString(),
        ]);

            // FIX: berkas table sesuai kolom: id_berkas, nama_berkas, jenis_berkas,
            //      nomor_berkas, file_path, id_pegawai, id_pengajuan
            foreach ([
            'sk_cpns'   => $skCpns,
            'sk_pns'    => $skPns,
            'pak'       => $pak,
            'publikasi' => $publikasi
        ] as $jenis => $file) {
            if (!$file) continue;
            DB::table('berkas')->insert([
                'id_berkas'    => 'BRK-' . time() . '-' . strtoupper($jenis),
                'id_pengajuan' => $id_pengajuan,
                'id_pegawai'   => $id_pegawai,
                'jenis_berkas' => $jenis,
                'nama_berkas'  => $file['nama_berkas'],
                'file_path'    => $file['file_path'],
            ]);
        }

            DB::commit();
        } catch (\Exception $e) {
    DB::rollBack();
    dd($e->getMessage(), $e->getFile(), $e->getLine());
}

        return redirect()->route('pangkat-golongan.index')
                         ->with('success', 'Pengajuan berhasil dikirim ke operator untuk diverifikasi.');
    }

    // ══════════════════════════════════════
    // SHOW
    // ══════════════════════════════════════
    public function show($id)
    {
        $id_pegawai_login = 5;

        $data = DB::selectOne("
            SELECT pp.*,
                   pg.id_panggol AS kode_pangkat,
                   pg.jenis_pangkat,
                   peg.nama_lengkap, peg.nidn, peg.nip,
                   pgn.id_panggol AS kode_pangkat_skrg,
                   pgn.jenis_pangkat AS jenis_pangkat_skrg
            FROM pengajuan_kenaikan pp
            JOIN pangkat_golongan pg  ON pp.target_panggol = pg.id_panggol
            JOIN pegawai peg          ON pp.id_pegawai = peg.id_pegawai
            LEFT JOIN pangkat_golongan pgn ON peg.id_panggol = pgn.id_panggol
            WHERE pp.id_pengajuan = ? AND pp.id_pegawai = ?
        ", [$id, $id_pegawai_login]);

        if (!$data) {
            return redirect()->route('pangkat-golongan.index')->with('error', 'Data tidak ditemukan.');
        }

        $berkasArr = DB::select(
            "SELECT jenis_berkas, nama_berkas, file_path FROM berkas WHERE id_pengajuan = ?",
            [$id]
        );
        $berkasAda = [];
        foreach ($berkasArr as $b) {
            $berkasAda[$b->jenis_berkas] = (array) $b;
        }

        $statusMap = [
            'menunggu'          => ['label' => 'Menunggu Verifikasi',  'class' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split'],
            'verifikasi'        => ['label' => 'Sedang Diverifikasi',  'class' => 'bg-info text-dark',    'icon' => 'bi-search'],
            'persetujuan'       => ['label' => 'Menunggu Persetujuan', 'class' => 'bg-primary',           'icon' => 'bi-person-check'],
            'disetujui'         => ['label' => 'Disetujui',            'class' => 'bg-success',           'icon' => 'bi-check-circle-fill'],
            'tolak_verifikasi'  => ['label' => 'Ditolak Verifikasi',   'class' => 'bg-danger',            'icon' => 'bi-x-circle-fill'],
            'tolak_persetujuan' => ['label' => 'Ditolak Persetujuan',  'class' => 'bg-danger',            'icon' => 'bi-x-circle-fill'],
        ];
        $st = $statusMap[$data->status_pengajuan] ?? ['label' => $data->status_pengajuan, 'class' => 'bg-secondary', 'icon' => 'bi-question-circle'];

        $jenis_pegawai = !empty($data->nidn) ? 'dosen' : 'tendik';
        $identitas     = ($jenis_pegawai === 'dosen') ? 'NIDN' : 'NIP';
        $idValue       = ($jenis_pegawai === 'dosen') ? ($data->nidn ?? '-') : ($data->nip ?? '-');

        $berkasError = [];
        $riwayat     = [];

        return view('dosen.pangkat.show', compact(
            'data', 'berkasAda', 'riwayat', 'st', 'identitas', 'idValue', 'berkasError'
        ));
    }

    // ══════════════════════════════════════
    // EDIT
    // ══════════════════════════════════════
    public function edit($id)
    {
        $id_pegawai_login = 5;
        $mode = request('mode', 'edit');

        $data = DB::selectOne("
            SELECT pp.*,
                   pg.id_panggol AS kode_pangkat_diajukan,
                   pg.jenis_pangkat AS jenis_pangkat_diajukan,
                   peg.nama_lengkap, peg.nidn, peg.nip,
                   peg.status_pegawai, peg.id_panggol, peg.id_jabfung,
                   pgn.id_panggol AS kode_pangkat_skrg,
                   pgn.jenis_pangkat AS jenis_pangkat_skrg
            FROM pengajuan_kenaikan pp
            JOIN pangkat_golongan pg  ON pp.target_panggol = pg.id_panggol
            JOIN pegawai peg          ON pp.id_pegawai = peg.id_pegawai
            LEFT JOIN pangkat_golongan pgn ON peg.id_panggol = pgn.id_panggol
            WHERE pp.id_pengajuan = ? AND pp.id_pegawai = ?
        ", [$id, $id_pegawai_login]);

        if (!$data) {
            return redirect()->route('pangkat-golongan.index')->with('error', 'Data tidak ditemukan.');
        }

        // FIX: pakai status_pengajuan bukan status
        if ($mode === 'revisi' && $data->status_pengajuan !== 'tolak_verifikasi') {
            return redirect()->route('pangkat-golongan.index')
                             ->with('error', 'Hanya pengajuan yang ditolak verifikasi yang dapat direvisi.');
        }
        if ($mode === 'edit' && $data->status_pengajuan !== 'menunggu') {
            return redirect()->route('pangkat-golongan.index')
                             ->with('error', 'Hanya pengajuan berstatus Menunggu yang dapat diedit.');
        }

        $jenis_pegawai   = !empty($data->nidn) ? 'dosen' : 'tendik';
        $urutan_sekarang = $this->urutanMap[$data->id_panggol ?? ''] ?? 0;
        $urutan_min      = ($jenis_pegawai === 'dosen') ? 9 : 5;
        $urutan_target   = ($urutan_sekarang === 0) ? $urutan_min : $urutan_sekarang + 1;
        $sudahPuncak     = ($urutan_sekarang >= 17);

        $berkasArr = DB::select(
            "SELECT id_berkas, jenis_berkas, nama_berkas, file_path FROM berkas WHERE id_pengajuan = ?",
            [$id]
        );
        $berkasAda = [];
        foreach ($berkasArr as $b) {
            $berkasAda[$b->jenis_berkas] = (array) $b;
        }

        $semuaPangkat = DB::select("
            SELECT id_panggol, jenis_pangkat
            FROM pangkat_golongan
            ORDER BY FIELD(id_panggol,
                'I/a','I/b','I/c','I/d',
                'II/a','II/b','II/c','II/d',
                'III/a','III/b','III/c','III/d',
                'IV/a','IV/b','IV/c','IV/d','IV/e')
        ");

        $semuaPangkat = array_map(function($p) use ($urutan_target, $urutan_sekarang, $sudahPuncak) {
            $urutan         = $this->urutanMap[$p->id_panggol] ?? 0;
            $p->urutan      = $urutan;
            $p->bisa        = ($urutan === $urutan_target) && !$sudahPuncak;
            $p->lebihRendah = ($urutan_sekarang > 0) && ($urutan <= $urutan_sekarang);
            return $p;
        }, $semuaPangkat);

        $jabfungToPangkat = [
        'Asisten Ahli'  => ['min' => 10, 'max' => 10, 'label' => 'III/b'],
        'Lektor'        => ['min' => 11, 'max' => 12, 'label' => 'III/c – III/d'],
        'Lektor Kepala' => ['min' => 13, 'max' => 14, 'label' => 'IV/a – IV/b'],
        'Guru Besar'    => ['min' => 15, 'max' => 17, 'label' => 'IV/c – IV/e'],
        ];

        $jabfungSekarang = null;
        if (!empty($data->id_jabfung)) {
            $jf = DB::selectOne(
                "SELECT jenis_jabfung FROM jabatan_fungsional WHERE id_jabfung = ? LIMIT 1",
                [$data->id_jabfung]
            );
            $jabfungSekarang = $jf->jenis_jabfung ?? null;
        }
        $jabfungInfo = $jabfungSekarang ? ($jabfungToPangkat[$jabfungSekarang] ?? null) : null;

        $identitas_label = ($jenis_pegawai === 'dosen') ? 'NIDN' : 'NIP';
        $identitas_value = ($jenis_pegawai === 'dosen') ? ($data->nidn ?? '-') : ($data->nip ?? '-');

        $berkasError = [];

        return view('dosen.pangkat.edit', compact(
            'data', 'mode', 'berkasAda', 'semuaPangkat',
            'urutan_sekarang', 'urutan_target',
            'jabfungSekarang', 'jabfungInfo',
            'identitas_label', 'identitas_value',
            'berkasError'
        ));
    }

    // ══════════════════════════════════════
    // UPDATE
    // ══════════════════════════════════════
    public function update(Request $request, $id)
    {
        $id_pegawai_login = 5;
        $mode             = $request->mode ?? 'edit';
        $nomor_usulan     = trim($request->nomor_usulan ?? '');

        $pengajuan = DB::selectOne(
            "SELECT pp.*, peg.id_panggol, peg.nidn, peg.nip
            FROM pengajuan_kenaikan pp
            JOIN pegawai peg ON pp.id_pegawai = peg.id_pegawai
            WHERE pp.id_pengajuan = ? AND pp.id_pegawai = ?",
            [$id, $id_pegawai_login]
        );

        if (!$pengajuan) return back()->with('error', 'Data tidak ditemukan.');

        $jenis_pegawai   = !empty($pengajuan->nidn) ? 'dosen' : 'tendik';
        $urutan_sekarang = $this->urutanMap[$pengajuan->id_panggol ?? ''] ?? 0;
        $urutan_min      = ($jenis_pegawai === 'dosen') ? 9 : 5;
        $urutan_target   = ($urutan_sekarang === 0) ? $urutan_min : $urutan_sekarang + 1;

        if (empty($request->target_panggol)) return back()->with('error', 'Pangkat harus dipilih.');

        $pangkatBaru = DB::selectOne(
            "SELECT id_panggol FROM pangkat_golongan WHERE id_panggol = ?",
            [$request->target_panggol]
        );
        if (!$pangkatBaru || ($this->urutanMap[$pangkatBaru->id_panggol] ?? 0) !== $urutan_target) {
            return back()->with('error', 'Pangkat tidak valid.');
        }

        // ── Validasi jabfung ──────────────────────────────────────
        $jabfungToPangkat = [
            'Asisten Ahli'  => ['min' => 10, 'max' => 10],
            'Lektor'        => ['min' => 11, 'max' => 12],
            'Lektor Kepala' => ['min' => 13, 'max' => 14],
            'Guru Besar'    => ['min' => 15, 'max' => 17],
        ];

        // Ambil jabfung pegawai
        $jabfung = DB::selectOne("
            SELECT jf.jenis_jabfung
            FROM pegawai p
            LEFT JOIN jabatan_fungsional jf ON p.id_jabfung = jf.id_jabfung
            WHERE p.id_pegawai = ?
        ", [$id_pegawai]);

        $jabfungSekarang = $jabfung->jenis_jabfung ?? null;

        if ($jabfungSekarang && isset($jabfungToPangkat[$jabfungSekarang])) {
            $maxDiizinkan = $jabfungToPangkat[$jabfungSekarang]['max'];
            if ($urutanDiajukan > $maxDiizinkan) {
                return back()->with('error', 
                    "Pangkat yang diajukan melebihi batas jabfung Anda ({$jabfungSekarang}). " .
                    "Silakan ajukan kenaikan jabfung terlebih dahulu."
                );
            }
        }
        // ─────────────────────────────────────────────────────────

        if (empty($nomor_usulan)) return back()->with('error', 'Nomor SK/Usulan wajib diisi.');

        $berkasArr = DB::select(
            "SELECT id_berkas, jenis_berkas, nama_berkas, file_path FROM berkas WHERE id_pengajuan = ?",
            [$id]
        );
        $berkasAda = [];
        foreach ($berkasArr as $b) {
            $berkasAda[$b->jenis_berkas] = (array) $b;
        }

        try {
            $uploads = [
                'sk_cpns'   => $this->uploadFile($request->file('sk_cpns'),   5),
                'sk_pns'    => $this->uploadFile($request->file('sk_pns'),    5),
                'pak'       => $this->uploadFile($request->file('pak'),       5),
                'publikasi' => $this->uploadFile($request->file('publikasi'), 10),
            ];
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        if (!($uploads['sk_cpns'] || isset($berkasAda['sk_cpns'])) ||
            !($uploads['sk_pns']  || isset($berkasAda['sk_pns']))  ||
            !($uploads['pak']     || isset($berkasAda['pak']))) {
            return back()->with('error', 'SK CPNS, SK PNS, dan PAK wajib ada.');
        }

        DB::beginTransaction();
        try {
            DB::table('pengajuan_kenaikan')->where('id_pengajuan', $id)->update([
                'target_panggol'   => $request->target_panggol,
                'status_pengajuan' => 'menunggu',
                'nomor_usulan'     => $nomor_usulan,
            ]);

            foreach ($uploads as $jenis => $fileResult) {
                if (!$fileResult) continue;
                if (isset($berkasAda[$jenis])) {
                    Storage::delete('public/uploads/pangkat/' . basename($berkasAda[$jenis]['file_path']));
                    DB::table('berkas')->where('id_berkas', $berkasAda[$jenis]['id_berkas'])->update([
                        'nama_berkas' => $fileResult['nama_berkas'],
                        'file_path'   => $fileResult['file_path'],
                    ]);
                } else {
                    DB::table('berkas')->insert([
                        'id_pengajuan' => $id,
                        'id_pegawai'   => $id_pegawai_login,
                        'jenis_berkas' => $jenis,
                        'nama_berkas'  => $fileResult['nama_berkas'],
                        'file_path'    => $fileResult['file_path'],
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->route('pangkat-golongan.index')
                         ->with('success', ($mode === 'revisi')
                             ? 'Revisi berhasil dikirim kembali ke operator.'
                             : 'Pengajuan berhasil diperbarui.');
    }

    // ══════════════════════════════════════
    // DESTROY
    // ══════════════════════════════════════
    public function destroy($id)
    {
        $id_pegawai_login = 5;

        $pengajuan = DB::selectOne(
            "SELECT id_pengajuan, status_pengajuan FROM pengajuan_kenaikan WHERE id_pengajuan = ? AND id_pegawai = ?",
            [$id, $id_pegawai_login]
        );

        if (!$pengajuan) {
            return redirect()->route('pangkat-golongan.index')->with('error', 'Data tidak ditemukan.');
        }

        if (!in_array($pengajuan->status_pengajuan, ['menunggu', 'draft'])) {
            return redirect()->route('pangkat-golongan.index')
                             ->with('error', 'Hanya pengajuan berstatus Draft atau Menunggu yang dapat dihapus.');
        }

        $berkasArr = DB::select(
            "SELECT nama_berkas, file_path FROM berkas WHERE id_pengajuan = ?",
            [$id]
        );

        DB::beginTransaction();
        try {
            DB::table('berkas')->where('id_pengajuan', $id)->delete();
            DB::table('pengajuan_kenaikan')->where('id_pengajuan', $id)->delete();
            DB::commit();

            foreach ($berkasArr as $b) {
                Storage::delete('public/uploads/pangkat/' . basename($b->file_path));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pangkat-golongan.index')
                             ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }

        return redirect()->route('pangkat-golongan.index')->with('success', 'Pengajuan berhasil dihapus.');
    }
}