<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\DataDiriController;
use App\Http\Controllers\Dosen\JabatanFungsionalController;
use App\Http\Controllers\Dosen\PangkatController;
use App\Http\Controllers\PegawaiController; 

// URL Otomatis: /dosen/data-diri
// Nama Rute Otomatis: dosen.datadiri.index
Route::prefix('data-diri')->group(function () {
    Route::get('/', [DataDiriController::class, 'index'])->name('datadiri.index');
    Route::get('/{id}', [PegawaiController::class, 'detail'])->name('pegawai.detail');
    Route::get('/{id}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
    Route::put('/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
});

// URL Otomatis: /dosen/pegawai/*
// Nama Rute Otomatis: dosen.pegawai.password.form
Route::prefix('pegawai')->group(function () {
    Route::get('/{id}/password', [PegawaiController::class, 'passwordForm'])->name('pegawai.password.form');
    Route::put('/{id}/password', [PegawaiController::class, 'passwordUpdate'])->name('pegawai.password.update');
});


// ==========================================
// MODULE: PANGKAT GOLONGAN
// ==========================================
// URL Otomatis: /dosen/pangkat-golongan
// Nama Rute Otomatis: dosen.pangkat-golongan.index, dosen.pangkat-golongan.create, dll.
// Cukup lepas array kustomnya, biarkan Laravel yang mengurus penamaannya secara otomatis.
Route::resource('pangkat-golongan', PangkatController::class);


// ==========================================
// MODULE: JABATAN FUNGSIONAL
// ==========================================
// URL Otomatis: /dosen/jabatanfungsional
// Nama Rute Otomatis: dosen.jabatanfungsional.index, dosen.jabatanfungsional.create, dll.
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