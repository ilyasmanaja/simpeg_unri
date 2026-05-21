<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Verifikasi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PersetujuanController extends Controller
{
    // ----------------------------------------------------------------
    // SURAT TUGAS
    // ----------------------------------------------------------------
    public function suratTugas()
    {
        // Antrean Pimpinan: Hanya yang sudah 'Terverifikasi' oleh Operator
        $antrian = Verifikasi::with([
            'berkas.suratTugas.anggota.pegawai',
            'berkas.pegawai.jabatanFungsional',
            'berkas.pegawai.pangkatGolongan',
        ])
            ->where('jenis_verifikasi', Verifikasi::JENIS_SURAT_TUGAS)
            ->whereIn('status_verifikasi', [Verifikasi::STATUS_TERVERIFIKASI, 'Terverifikasi'])
            // FIX: tabel VERIFIKASI tidak punya updated_at
            ->orderByDesc('tanggal_proses')
            ->orderByDesc('tanggal_pengajuan')
            ->paginate(15);

        // Riwayat Pimpinan: Yang sudah disetujui atau ditolak pimpinan
        $riwayat = Verifikasi::with(['berkas.suratTugas', 'berkas.pegawai'])
            ->where('jenis_verifikasi', Verifikasi::JENIS_SURAT_TUGAS)
            ->whereIn('status_verifikasi', ['Disetujui', 'Ditolak (Pimpinan)'])
            ->orderByDesc('tanggal_proses')
            ->paginate(15);

        return view('pimpinan.persetujuan-surat-tugas', compact('antrian', 'riwayat'));
    }

    // ----------------------------------------------------------------
    // JABATAN FUNGSIONAL
    // ----------------------------------------------------------------
    public function jabfung()
    {
        // Antrean Pimpinan: Hanya yang sudah 'Diteruskan' oleh Operator
        $antrian = Verifikasi::with([
            'berkas.pegawai.jabatanFungsional',
            'berkas.pengajuan',
        ])
            ->where('jenis_verifikasi', Verifikasi::JENIS_JABFUNG)
            ->whereIn('status_verifikasi', [Verifikasi::STATUS_DITERUSKAN, 'Diteruskan'])
            // FIX: tabel VERIFIKASI tidak punya updated_at
            ->orderByDesc('tanggal_proses')
            ->orderByDesc('tanggal_pengajuan')
            ->paginate(15);

        $riwayat = Verifikasi::with([
            'berkas.pegawai',
            'berkas.pengajuan'
        ])
            ->where('jenis_verifikasi', Verifikasi::JENIS_JABFUNG)
            ->whereIn('status_verifikasi', ['Disetujui', 'Ditolak (Pimpinan)'])
            ->orderByDesc('tanggal_proses')
            ->paginate(15);

        return view('pimpinan.persetujuan-jabfung', compact('antrian', 'riwayat'));
    }

    // ----------------------------------------------------------------
    // PANGKAT GOLONGAN
    // ----------------------------------------------------------------
    public function panggol()
    {
        // Antrean Pimpinan: Hanya yang sudah 'Diteruskan' oleh Operator
        $antrian = Verifikasi::with([
            'berkas.pegawai',
            'berkas.pengajuan.pangkatGolongan', // <-- tambahkan ini
        ])
            ->where('jenis_verifikasi', Verifikasi::JENIS_PANGKAT)
            ->whereIn('status_verifikasi', [Verifikasi::STATUS_DITERUSKAN, 'Diteruskan'])
            // FIX: tabel VERIFIKASI tidak punya updated_at
            ->orderByDesc('tanggal_proses')
            ->orderByDesc('tanggal_pengajuan')
            ->paginate(15);

        $riwayat = Verifikasi::with([
            'berkas.pegawai',
            'berkas.pengajuan.pangkatGolongan', // <-- tambahkan ini
        ])
            ->where('jenis_verifikasi', Verifikasi::JENIS_PANGKAT)
            ->whereIn('status_verifikasi', ['Disetujui', 'Ditolak (Pimpinan)'])
            ->orderByDesc('tanggal_proses')
            ->paginate(15);

        return view('pimpinan.persetujuan-panggol', compact('antrian', 'riwayat'));
    }

    // ----------------------------------------------------------------
    // SETUJUI — POST /pimpinan/persetujuan/{id}/setuju
    // ----------------------------------------------------------------
    public function setujui(Request $request, $id_verifikasi)
    {
        $verifikasi = Verifikasi::findOrFail($id_verifikasi);

        // Pastikan statusnya memang valid untuk disetujui pimpinan
        if (
            !in_array($verifikasi->status_verifikasi, [
                Verifikasi::STATUS_TERVERIFIKASI,
                'Terverifikasi',
                Verifikasi::STATUS_DITERUSKAN,
                'Diteruskan'
            ])
        ) {
            return back()->with('error', 'Status tidak valid untuk disetujui.');
        }

        $verifikasi->update([
            'status_verifikasi' => 'Disetujui',
            'tanggal_proses' => Carbon::today(),
            'keterangan' => 'Disetujui oleh Pimpinan',
        ]);

        if ($verifikasi->jenis_verifikasi === Verifikasi::JENIS_SURAT_TUGAS) {
            $suratTugas = $verifikasi->berkas->suratTugas ?? null;
            if ($suratTugas) {
                $suratTugas->update([
                    'status' => 'disetujui'
                ]);
            }
        } elseif (in_array($verifikasi->jenis_verifikasi, [Verifikasi::JENIS_JABFUNG, Verifikasi::JENIS_PANGKAT])) {
            $pengajuan = $verifikasi->berkas->pengajuan ?? null;
            if ($pengajuan) {
                $pengajuan->update(['status_pengajuan' => 'disetujui']);
            }
        }

        return back()->with('success', 'Pengajuan berhasil disetujui.');
    }

    // ----------------------------------------------------------------
    // TOLAK PIMPINAN — POST /pimpinan/persetujuan/{id}/tolak
    // ----------------------------------------------------------------
    public function tolak(Request $request, $id_verifikasi)
    {
        $request->validate([
            'keterangan' => 'required|string|max:500',
        ]);

        $verifikasi = Verifikasi::findOrFail($id_verifikasi);

        if (
            !in_array($verifikasi->status_verifikasi, [
                Verifikasi::STATUS_TERVERIFIKASI,
                'Terverifikasi',
                Verifikasi::STATUS_DITERUSKAN,
                'Diteruskan'
            ])
        ) {
            return back()->with('error', 'Status tidak valid untuk ditolak.');
        }

        $verifikasi->update([
            'status_verifikasi' => 'Ditolak (Pimpinan)',
            'tanggal_proses' => Carbon::today(),
            'keterangan' => $request->input('keterangan'),
        ]);

        if ($verifikasi->jenis_verifikasi === Verifikasi::JENIS_SURAT_TUGAS) {
            $suratTugas = $verifikasi->berkas->suratTugas ?? null;
            if ($suratTugas) {
                $suratTugas->update([
                    'status' => 'ditolak (persetujuan)',
                    'alasan_penolakan' => $request->input('keterangan')
                ]);
            }
        } elseif (in_array($verifikasi->jenis_verifikasi, [Verifikasi::JENIS_JABFUNG, Verifikasi::JENIS_PANGKAT])) {
            $pengajuan = $verifikasi->berkas->pengajuan ?? null;
            if ($pengajuan) {
                $pengajuan->update(['status_pengajuan' => 'tolak_persetujuan']);
            }
        }

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }
}