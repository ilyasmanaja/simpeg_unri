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
            // 'berkas.jabatanFungsional', <--- HAPUS BARIS INI
            'berkas.pengajuan',
        ])
            ->where('jenis_verifikasi', Verifikasi::JENIS_JABFUNG)
            ->whereIn('status_verifikasi', ['Menunggu Diproses', 'Sedang Diverifikasi'])
            ->orderByDesc('tanggal_pengajuan')
            ->paginate(15);

        $riwayat = Verifikasi::with([
            'berkas.pegawai',
            // 'berkas.jabatanFungsional' <--- HAPUS JUGA DI SINI (jika ada)
            'berkas.pengajuan' // Ganti dengan ini jika perlu data pengajuannya
        ])
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
            // 'berkas.pegawai.pangkatGolongan'
            'berkas.pegawai',
            // // 'berkas.pangkatGolongan', <--- HAPUS BARIS INI
            // 'berkas.pengajuan',
            'berkas.pengajuan.pangkatGolongan'
        ])
            ->where('jenis_verifikasi', Verifikasi::JENIS_PANGKAT)
            ->whereIn('status_verifikasi', ['Menunggu Diproses', 'Sedang Diverifikasi'])
            ->orderByDesc('tanggal_pengajuan')
            ->paginate(15);

        $riwayat = Verifikasi::with([
            'berkas.pegawai',
            // 'berkas.pangkatGolongan' <--- HAPUS JUGA DI SINI
            'berkas.pengajuan.pangkatGolongan'
        ])
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
            'tanggal_proses' => Carbon::today(),
        ]);

        if ($verifikasi->jenis_verifikasi === Verifikasi::JENIS_SURAT_TUGAS) {
            $suratTugas = $verifikasi->berkas->suratTugas ?? null;
            if ($suratTugas) {
                $suratTugas->update(['status' => 'sedang diverifikasi']);
            }
        } elseif (in_array($verifikasi->jenis_verifikasi, [Verifikasi::JENIS_JABFUNG, Verifikasi::JENIS_PANGKAT])) {
            $pengajuan = $verifikasi->berkas->pengajuan ?? null;
            if ($pengajuan) {
                // Di dosen, status "Sedang Diverifikasi" = "verifikasi"
                $pengajuan->update(['status_pengajuan' => 'verifikasi']);
            }
        }

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

        // Tentukan status untuk tabel VERIFIKASI
        $statusOk = $verifikasi->jenis_verifikasi === Verifikasi::JENIS_SURAT_TUGAS
            ? Verifikasi::STATUS_TERVERIFIKASI
            : Verifikasi::STATUS_DITERUSKAN;

        // 1. Update tabel VERIFIKASI
        $verifikasi->update([
            'status_verifikasi' => $statusOk,
            'tanggal_proses' => Carbon::today(),
            'keterangan' => '-',
        ]);

        // 2. SINKRONISASI KE TABEL SURAT TUGAS
        // Ambil data surat tugas terkait melalui relasi (pastikan relasinya ada di model Verifikasi)
        if ($verifikasi->jenis_verifikasi === Verifikasi::JENIS_SURAT_TUGAS) {
            $suratTugas = $verifikasi->berkas->suratTugas ?? null;
            if ($suratTugas) {
                $suratTugas->update([
                    'status' => 'menunggu persetujuan' // Teks ini harus sama dengan yang dicek di Blade Dosen
                ]);
            }
        } elseif (in_array($verifikasi->jenis_verifikasi, [Verifikasi::JENIS_JABFUNG, Verifikasi::JENIS_PANGKAT])) {
            $pengajuan = $verifikasi->berkas->pengajuan ?? null;
            if ($pengajuan) {
                // Di dosen, status setelah diverifikasi = "persetujuan" (Menunggu Persetujuan)
                $pengajuan->update(['status_pengajuan' => 'persetujuan']);
            }
        }

        return back()->with('success', 'Pengajuan berhasil diteruskan ke pimpinan.');
    }

    // ----------------------------------------------------------------
    // TOLAK — POST /operator/verifikasi/{id}/tolak
    // ----------------------------------------------------------------
    public function tolak(Request $request, $id_verifikasi)
    {
        $request->validate([
            'keterangan' => 'required|string|max:500',
            'berkas_bermasalah' => 'nullable|array',
        ]);

        $verifikasi = Verifikasi::findOrFail($id_verifikasi);

        if ($verifikasi->status_verifikasi !== 'Sedang Diverifikasi') {
            return back()->with('error', 'Status tidak valid untuk ditolak.');
        }

        $berkasBermasalah = $request->input('berkas_bermasalah', []);
        $keteranganFinal = $request->input('keterangan');
        if (!empty($berkasBermasalah)) {
            $keteranganFinal .= ' [Berkas bermasalah: ' . implode(', ', $berkasBermasalah) . ']';
        }

        $verifikasi->update([
            'status_verifikasi' => Verifikasi::STATUS_DITOLAK,
            'tanggal_proses' => Carbon::today(),
            'keterangan' => $keteranganFinal,
        ]);

        if ($verifikasi->jenis_verifikasi === Verifikasi::JENIS_SURAT_TUGAS) {
            $suratTugas = $verifikasi->berkas->suratTugas ?? null;
            if ($suratTugas) {
                $suratTugas->update([
                    'status' => 'ditolak (verifikasi)', // Teks ini yang dikenali oleh Blade Dosen
                    'alasan_penolakan' => $keteranganFinal
                ]);
            }
        } elseif (in_array($verifikasi->jenis_verifikasi, [Verifikasi::JENIS_JABFUNG, Verifikasi::JENIS_PANGKAT])) {
            $pengajuan = $verifikasi->berkas->pengajuan ?? null;
            if ($pengajuan) {
                $pengajuan->update(['status_pengajuan' => 'tolak_verifikasi']);
            }

            // Simpan daftar berkas bermasalah di tabel verifikasi sebagai JSON agar bisa dibaca Dosen
            $verifikasi->update([
                'berkas_bermasalah' => json_encode($berkasBermasalah)
            ]);
        }

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }
}