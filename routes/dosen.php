<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\DataDiriController;
use App\Http\Controllers\Dosen\JabatanFungsionalController;
// Pastikan path PegawaiController ini sesuai dengan letak file controllermu
use App\Http\Controllers\PegawaiController; 

// ==========================================
// MODULE: DATA DIRI & PEGAWAI
// ==========================================

// Group route untuk URL yang diawali dengan /data-diri
Route::prefix('data-diri')->group(function () {
    Route::get('/', [DataDiriController::class, 'index'])->name('datadiri.index');
    Route::get('/{id}', [PegawaiController::class, 'show'])->name('pegawai.show');
    Route::get('/{id}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
    Route::put('/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
});

// Group route khusus untuk pengaturan /pegawai (contoh: password)
Route::prefix('pegawai')->group(function () {
    Route::get('/{id}/password', [PegawaiController::class, 'passwordForm'])->name('pegawai.password.form');
    Route::put('/{id}/password', [PegawaiController::class, 'passwordUpdate'])->name('pegawai.password.update');
});

// ==========================================
// MODULE: JABATAN FUNGSIONAL
// ==========================================

// URL: /jabatanfungsional
Route::prefix('jabatanfungsional')->name('jabatanfungsional.')->group(function () {
    Route::get('/',             [JabatanFungsionalController::class, 'index'])->name('index');
    Route::get('/create',       [JabatanFungsionalController::class, 'create'])->name('create');
    Route::post('/',            [JabatanFungsionalController::class, 'store'])->name('store');
    Route::get('/{id}/edit',    [JabatanFungsionalController::class, 'edit'])->name('edit');
    Route::put('/{id}',         [JabatanFungsionalController::class, 'update'])->name('update');
    Route::delete('/{id}',      [JabatanFungsionalController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/detail',  [JabatanFungsionalController::class, 'show'])->name('detail');
    
    Route::get('/{id}/download', function ($id) {
        return redirect()->route('dosen.jabatanfungsional.index')
            ->with('error', 'Fitur download surat belum tersedia.');
    })->name('download');
});