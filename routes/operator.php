<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Operator\VerifikasiController;
use App\Http\Controllers\Operator\BerkasController;

// TIDAK PERLU Route::prefix('operator') lagi karena sudah diwakili oleh web.php

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