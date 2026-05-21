<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\JabatanFungsionalController;
use App\Http\Controllers\Dosen\PangkatController;
use App\Http\Controllers\Dosen\SuratTugasController; // Pastikan Controller ini ada di folder Dosen
use App\Http\Controllers\PegawaiController; 

// ==========================================
// MODULE: DATA DIRI
// ==========================================
Route::prefix('data-diri')->group(function () {
    Route::get('/', [PegawaiController::class, 'index'])->name('datadiri.index');
    Route::get('/create', [PegawaiController::class, 'create'])->name('pegawai.create');
    Route::post('/', [PegawaiController::class, 'store'])->name('pegawai.store'); 
    Route::get('/{id}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
    Route::put('/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
});

Route::prefix('pegawai')->group(function () {
    Route::get('/{id}/password', [PegawaiController::class, 'passwordForm'])->name('pegawai.password.form');
    Route::put('/{id}/password', [PegawaiController::class, 'passwordUpdate'])->name('pegawai.password.update');
});

// ==========================================
// MODULE: SURAT TUGAS
// ==========================================
Route::prefix('surat')->name('surat.')->group(function () {
    Route::get('/', [SuratTugasController::class, 'index'])->name('index');
    Route::get('/create', [SuratTugasController::class, 'create'])->name('create');
    Route::post('/store', [SuratTugasController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [SuratTugasController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [SuratTugasController::class, 'update'])->name('update');
    Route::get('/ajukan-kembali/{id}', [SuratTugasController::class, 'ajukanKembali'])->name('ajukanKembali');
    Route::post('/ajukan-kembali/{id}', [SuratTugasController::class, 'prosesAjukanKembali'])->name('prosesAjukanKembali');
    Route::get('/hapus/{id}', [SuratTugasController::class, 'destroy'])->name('destroy');
    Route::get('/download/{id}', [SuratTugasController::class, 'downloadPdf'])->name('downloadPdf');
});

// Berkas tetap di luar atau prefix khusus jika perlu
Route::get('/berkas/{filename}', [SuratTugasController::class, 'viewBerkas'])->name('berkas.view');

// ==========================================
// MODULE: PANGKAT GOLONGAN
// ==========================================
Route::resource('pangkat-golongan', PangkatController::class);

// ==========================================
// MODULE: JABATAN FUNGSIONAL
// ==========================================
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