<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\SuratTugasController;
use App\Http\Controllers\Dosen\DataDiriController;
use App\Http\Controllers\Dosen\JabatanFungsionalController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Pengajuan Surat Tugas
|--------------------------------------------------------------------------
*/
Route::get('/read-pengajuan-surat-tugas',
    [SuratTugasController::class, 'index'])
    ->name('surat.index');

Route::get('/pengajuan-surat-tugas',
    [SuratTugasController::class, 'create'])
    ->name('surat.create');

Route::post('/surat/store',
    [SuratTugasController::class, 'store'])
    ->name('surat.store');

Route::get('/update/{id}',
    [SuratTugasController::class, 'edit'])
    ->name('surat.edit');

Route::post('/update/{id}',
    [SuratTugasController::class, 'update'])
    ->name('surat.update');

Route::get('/ajukan-kembali/{id}',
    [SuratTugasController::class, 'ajukanKembali'])
    ->name('surat.ajukanKembali');

Route::post('/ajukan-kembali/{id}',
    [SuratTugasController::class, 'prosesAjukanKembali'])
    ->name('surat.prosesAjukanKembali');

Route::get('/hapus/{id}',
    [SuratTugasController::class, 'destroy'])
    ->name('surat.destroy');

Route::get('/berkas/{filename}',
    [SuratTugasController::class, 'viewBerkas'])
    ->name('berkas.view');

Route::get('/download-surat-tugas/{id}',
    [SuratTugasController::class, 'downloadPdf'])
    ->name('surat.downloadPdf');

