<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Verifikasi;
use App\Models\Berkas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VerifikasiController extends Controller
{
    // ----------------------------------------------------------------
    // SURAT TUGAS
    // ----------------------------------------------------------------
    public function suratTugas()
    {
        $antrian = Verifikasi::with([
                        'berkas.suratTugas.anggota.pegawai',
                        'berkas.pegawai.jabatanFungsional',
                        'berkas.pegawai.pangkatGolongan',
                    ])
                    ->where('jenis_verifikasi', Verifikasi::JENIS_SURAT_TUGAS)
                    ->whereIn('status_verifikasi', ['Menunggu Diproses', 'Sedang Diverifikasi'])
                    ->orderByDesc('tanggal_pengajuan')
                    ->paginate(15);

        $riwayat = Verifikasi::with(['berkas.suratTugas', 'berkas.pegawai'])
                    ->where('jenis_verifikasi', Verifikasi::JENIS_SURAT_TUGAS)
                    ->whereNotIn('status_verifikasi', ['Menunggu Diproses', 'Sedang Diverifikasi'])
                    ->orderByDesc('tanggal_proses')
                    ->paginate(15);

        return view('Operator.verif-surat-tugas', compact('antrian', 'riwayat'));
    }

    // ----------------------------------------------------------------
    // JABATAN FUNGSIONAL
    // ----------------------------------------------------------------
    public function jabfung()
    {
        $antrian = Verifikasi::with([
                        'berkas.pegawai.jabatanFungsional',
                        'berkas.jabatanFungsional',
                        'berkas.pengajuan',
                    ])
                    ->where('jenis_verifikasi', Verifikasi::JENIS_JABFUNG)
                    ->whereIn('status_verifikasi', ['Menunggu Diproses', 'Sedang Diverifikasi'])
                    ->orderByDesc('tanggal_pengajuan')
                    ->paginate(15);

        $riwayat = Verifikasi::with(['berkas.pegawai', 'berkas.jabatanFungsional'])
                    ->where('jenis_verifikasi', Verifikasi::JENIS_JABFUNG)
                    ->whereNotIn('status_verifikasi', ['Menunggu Diproses', 'Sedang Diverifikasi'])
                    ->orderByDesc('tanggal_proses')
                    ->paginate(15);

        return view('Operator.verif-jabfung', compact('antrian', 'riwayat'));
    }

    // ----------------------------------------------------------------
    // PANGKAT GOLONGAN
    // ----------------------------------------------------------------
    public function panggol()
    {
        $antrian = Verifikasi::with([
                        'berkas.pegawai.pangkatGolongan',
                        'berkas.pangkatGolongan',
                        'berkas.pengajuan',
                    ])
                    ->where('jenis_verifikasi', Verifikasi::JENIS_PANGKAT)
                    ->whereIn('status_verifikasi', ['Menunggu Diproses', 'Sedang Diverifikasi'])
                    ->orderByDesc('tanggal_pengajuan')
                    ->paginate(15);

        $riwayat = Verifikasi::with(['berkas.pegawai', 'berkas.pangkatGolongan'])
                    ->where('jenis_verifikasi', Verifikasi::JENIS_PANGKAT)
                    ->whereNotIn('status_verifikasi', ['Menunggu Diproses', 'Sedang Diverifikasi'])
                    ->orderByDesc('tanggal_proses')
                    ->paginate(15);

        return view('Operator.verif-panggol', compact('antrian', 'riwayat'));
    }

    // ----------------------------------------------------------------
    // TERIMA — ubah status ke Sedang Diverifikasi
    // POST /operator/verifikasi/{id}/terima
    // ----------------------------------------------------------------
    public function terima($id_verifikasi)
    {
        $verifikasi = Verifikasi::findOrFail($id_verifikasi);

        if ($verifikasi->status_verifikasi !== 'Menunggu Diproses') {
            return back()->with('error', 'Status tidak valid untuk diterima.');
        }

        $verifikasi->update([
            'status_verifikasi' => 'Sedang Diverifikasi',
            'tanggal_proses'    => Carbon::today(),
        ]);

        return back()->with('success', 'Pengajuan berhasil diterima, silakan periksa berkas.');
    }

    // ----------------------------------------------------------------
    // VERIFIKASI (Diteruskan) — POST /operator/verifikasi/{id}/verifikasi
    // ----------------------------------------------------------------
    public function verifikasi(Request $request, $id_verifikasi)
    {
        $verifikasi = Verifikasi::findOrFail($id_verifikasi);

        if ($verifikasi->status_verifikasi !== 'Sedang Diverifikasi') {
            return back()->with('error', 'Status tidak valid untuk diverifikasi.');
        }

        // Tentukan status sesuai jenis
        $statusOk = $verifikasi->jenis_verifikasi === Verifikasi::JENIS_SURAT_TUGAS
            ? Verifikasi::STATUS_TERVERIFIKASI
            : Verifikasi::STATUS_DITERUSKAN;

        $verifikasi->update([
            'status_verifikasi' => $statusOk,
            'tanggal_proses'    => Carbon::today(),
            'keterangan'        => '-',
        ]);

        return back()->with('success', 'Pengajuan berhasil diteruskan ke pimpinan.');
    }

    // ----------------------------------------------------------------
    // TOLAK — POST /operator/verifikasi/{id}/tolak
    // ----------------------------------------------------------------
    public function tolak(Request $request, $id_verifikasi)
    {
        $request->validate([
            'keterangan'        => 'required|string|max:500',
            'berkas_bermasalah' => 'nullable|array',
        ]);

        $verifikasi = Verifikasi::findOrFail($id_verifikasi);

        if ($verifikasi->status_verifikasi !== 'Sedang Diverifikasi') {
            return back()->with('error', 'Status tidak valid untuk ditolak.');
        }

        $berkasBermasalah = $request->input('berkas_bermasalah', []);
        $keteranganFinal  = $request->input('keterangan');
        if (!empty($berkasBermasalah)) {
            $keteranganFinal .= ' [Berkas bermasalah: ' . implode(', ', $berkasBermasalah) . ']';
        }

        $verifikasi->update([
            'status_verifikasi' => Verifikasi::STATUS_DITOLAK,
            'tanggal_proses'    => Carbon::today(),
            'keterangan'        => $keteranganFinal,
        ]);

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }
}