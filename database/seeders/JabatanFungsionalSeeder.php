<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanFungsionalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('JABATAN_FUNGSIONAL')->insert([
            // DOSEN (WAJIB URUT)
            ['id_jabfung' => 'JF001', 'jenis_jabfung' => 'dosen', 'nama_jabfung' => 'Asisten Ahli', 'urutan' => 1],
            ['id_jabfung' => 'JF002', 'jenis_jabfung' => 'dosen', 'nama_jabfung' => 'Lektor', 'urutan' => 2],
            ['id_jabfung' => 'JF003', 'jenis_jabfung' => 'dosen', 'nama_jabfung' => 'Lektor Kepala', 'urutan' => 3],
            ['id_jabfung' => 'JF004', 'jenis_jabfung' => 'dosen', 'nama_jabfung' => 'Guru Besar', 'urutan' => 4],

            // TENDIK (TIDAK PAKAI URUTAN)
            ['id_jabfung' => 'JT001', 'jenis_jabfung' => 'tendik', 'nama_jabfung' => 'Pengadministrasi Akademik', 'urutan' => null],
            ['id_jabfung' => 'JT002', 'jenis_jabfung' => 'tendik', 'nama_jabfung' => 'Pengadministrasi Keuangan', 'urutan' => null],
            ['id_jabfung' => 'JT003', 'jenis_jabfung' => 'tendik', 'nama_jabfung' => 'Pengadministrasi Umum', 'urutan' => null],
            ['id_jabfung' => 'JT004', 'jenis_jabfung' => 'tendik', 'nama_jabfung' => 'Pranata Komputer', 'urutan' => null],
            ['id_jabfung' => 'JT005', 'jenis_jabfung' => 'tendik', 'nama_jabfung' => 'Pranata Laboratorium Pendidikan', 'urutan' => null],
            ['id_jabfung' => 'JT006', 'jenis_jabfung' => 'tendik', 'nama_jabfung' => 'Pustakawan', 'urutan' => null],
            ['id_jabfung' => 'JT007', 'jenis_jabfung' => 'tendik', 'nama_jabfung' => 'Teknisi/Laboran', 'urutan' => null],
        ]);
    }
}