<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;

class DataDiriController extends Controller
{
    public function index()
    {
        // Skenario: Kita mengambil data pegawai berdasarkan user yang sedang login.
        // Karena sistem login belum utuh, kita anggap auth()->user() nantinya akan me-return id_pegawai.
        
        // Hapus tanda komentar di bawah ini jika sistem Auth sudah jalan:
        // $id_pegawai_login = auth()->user()->id_pegawai;
        
        // SEMENTARA: Kita tembak data statis (misal id_pegawai = 1) untuk testing tampilan
        $id_pegawai_login = 1; 

        // Mengambil data pegawai beserta relasi Jabatan dan Pangkatnya (Eager Loading)
        $pegawai = Pegawai::with(['jabatanFungsional', 'pangkatGolongan'])->find($id_pegawai_login);

        // Jika data tidak ditemukan, lemparkan error 404
        if (!$pegawai) {
            abort(404, 'Data Pegawai tidak ditemukan.');
        }

        // Melempar variabel $pegawai ke file View Blade
        return view('dosen.data-diri.detail', compact('pegawai'));
    }
}
