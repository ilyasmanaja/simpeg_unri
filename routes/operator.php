<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Operator\VerifikasiController;
use App\Http\Controllers\Operator\BerkasController;
use App\Http\Controllers\Operator\DataDiriController; 
use App\Http\Controllers\Operator\PegawaiController;

// TIDAK PERLU Route::prefix('operator') lagi karena sudah diwakili oleh web.php

Route::prefix('data-diri')->group(function () {
    // Halaman Index Operator
    Route::get('/', [PegawaiController::class, 'index'])->name('datadiri.index');

    // Halaman Tambah & Proses Simpan Operator
    Route::get('/create', [PegawaiController::class, 'create'])->name('pegawai.create');
    Route::post('/', [PegawaiController::class, 'store'])->name('pegawai.store');

    // Halaman Edit & Proses Update Operator
    Route::get('/{id}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
    Route::put('/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
});

// ---- Halaman verifikasi ----------------------------------------
Route::get('/verifikasi/surat-tugas', [VerifikasiController::class, 'suratTugas'])->name('verifikasi.surat-tugas');
Route::get('/verifikasi/jabfung', [VerifikasiController::class, 'jabfung'])->name('verifikasi.jabfung');
Route::get('/verifikasi/panggol', [VerifikasiController::class, 'panggol'])->name('verifikasi.panggol');

// ---- Aksi verifikasi (POST dari JS via form hidden) -------------
Route::post('/verifikasi/{id}/terima', [VerifikasiController::class, 'terima'])->name('terima');
Route::post('/verifikasi/{id}/verifikasi', [VerifikasiController::class, 'verifikasi'])->name('verifikasi');
Route::post('/verifikasi/{id}/tolak', [VerifikasiController::class, 'tolak'])->name('tolak');

// ---- Berkas -----------------------------------------------------
Route::get('/berkas', [BerkasController::class, 'index'])->name('berkas.index');
Route::get('/berkas/{id_berkas}', [BerkasController::class, 'show'])->name('berkas.show');
Route::get('/berkas/{id_berkas}/download', [BerkasController::class, 'download'])->name('berkas.download');


// ---- Manajemen Akun ----------------------------------------------

Route::resource('/manajemen_akun', PegawaiController::class);