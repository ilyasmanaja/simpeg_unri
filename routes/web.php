<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\DataDiriController;
use App\Http\Controllers\PegawaiController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/data-diri/{id}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
Route::put('/data-diri/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
Route::get('/data-diri/{id}', [PegawaiController::class, 'show'])->name('pegawai.show');
Route::get('/pegawai/{id}/password', [PegawaiController::class, 'passwordForm'])->name('pegawai.password.form');
Route::put('/pegawai/{id}/password', [PegawaiController::class, 'passwordUpdate'])->name('pegawai.password.update');

