<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pimpinan\DashboardController;
use App\Http\Controllers\Pimpinan\PersetujuanController;
use App\Http\Controllers\Pimpinan\LaporanController;
use App\Http\Controllers\Pimpinan\PegawaiController;

// TIDAK PERLU Route::prefix('pimpinan') lagi karena sudah diwakili oleh web.php

// ---- Dashboard & Laporan Pimpinan ---------------------------------
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');

// ---- Kelompok Rute Data Diri Pimpinan -----------------------------
Route::prefix('data-diri')->group(function () {
    // Halaman Index Data Diri Pimpinan
    Route::get('/', [PegawaiController::class, 'index'])->name('datadiri.index');

    // Halaman Tambah & Proses Simpan Data Diri Pimpinan
    Route::get('/create', [PegawaiController::class, 'create'])->name('pegawai.create');
    Route::post('/', [PegawaiController::class, 'store'])->name('pegawai.store');

    // Halaman Edit & Proses Update Data Diri Pimpinan
    Route::get('/{id}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
    Route::put('/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
});

// ---- Halaman Persetujuan (Sesuai Sidebar) -------------------------
Route::get('/persetujuan/surat-tugas', [PersetujuanController::class, 'suratTugas'])->name('persetujuan.surat-tugas');
Route::get('/persetujuan/jabfung', [PersetujuanController::class, 'jabfung'])->name('persetujuan.jabfung');
Route::get('/persetujuan/panggol', [PersetujuanController::class, 'panggol'])->name('persetujuan.panggol');

// ---- Aksi Persetujuan (POST untuk update status) ------------------
Route::post('/persetujuan/{id}/setuju', [PersetujuanController::class, 'setujui'])->name('persetujuan.setuju');
Route::post('/persetujuan/{id}/tolak', [PersetujuanController::class, 'tolak'])->name('persetujuan.tolak');