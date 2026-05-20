<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai ?? new Pegawai();

        // 1. DATA UNTUK 4 KOTAK STATISTIK ATAS
        $totalPegawai = Pegawai::count();
        // Asumsi: Dosen memiliki NIDN, Tendik tidak memiliki NIDN
        $totalDosen = Pegawai::whereNotNull('nidn')->count(); 
        $totalTendik = Pegawai::whereNull('nidn')->count();
        $pengajuanBaru = 0; // Ganti dengan Model SuratTugas jika sudah ada. Misal: SuratTugas::where('status', 'menunggu')->count();

        // 2. DATA UNTUK CHART JABATAN FUNGSIONAL
        // Sesuai id_jabfung yang kita buat di database (1=Asisten Ahli, 2=Lektor, 3=Lektor Kepala, 4=Guru Besar)
        $dataJabfung = [
            Pegawai::where('id_jabfung', 1)->count(),
            Pegawai::where('id_jabfung', 2)->count(),
            Pegawai::where('id_jabfung', 3)->count(),
            Pegawai::where('id_jabfung', 4)->count(),
        ];

        // 3. DATA UNTUK CHART GOLONGAN
        // Menghitung berapa banyak pegawai di tiap ID Golongan (asumsi ID 9-12 adalah Golongan III, dst)
        $dataGolongan = [
            Pegawai::whereIn('id_panggol', [9, 10, 11, 12])->count(), // Golongan III
            Pegawai::whereIn('id_panggol', [13, 14, 15, 16, 17])->count(), // Golongan IV
            Pegawai::whereIn('id_panggol', [5, 6, 7, 8])->count(), // Golongan II
        ];

        // 4. DATA UNTUK CHART USIA (Dihitung dari tanggal_lahir)
        // Kita tarik semua tanggal lahir pegawai, lalu hitung umurnya pakai Carbon
        $semuaPegawai = Pegawai::whereNotNull('tanggal_lahir')->get();
        $usia20_30 = 0; $usia31_40 = 0; $usia41_50 = 0; $usia51_60 = 0;

        foreach ($semuaPegawai as $p) {
            $umur = Carbon::parse($p->tanggal_lahir)->age;
            if ($umur >= 20 && $umur <= 30) $usia20_30++;
            elseif ($umur >= 31 && $umur <= 40) $usia31_40++;
            elseif ($umur >= 41 && $umur <= 50) $usia41_50++;
            elseif ($umur >= 51 && $umur <= 60) $usia51_60++;
        }
        $dataUsia = [$usia20_30, $usia31_40, $usia41_50, $usia51_60];

        $lakiLaki = Pegawai::where('jenis_kelamin', 'L')->count();
        $perempuan = Pegawai::where('jenis_kelamin', 'P')->count();
        
        $dataGender = [$lakiLaki, $perempuan];

        // Mengirimkan semua data di atas ke halaman View
        return view('pimpinan.dashboard', compact(
            'pegawai', 'totalPegawai', 'totalDosen', 'totalTendik', 'pengajuanBaru',
            'dataJabfung', 'dataGolongan', 'dataUsia', 'dataGender'
        ));
    }
}