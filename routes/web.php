<?php

use Illuminate\Support\Facades\Route;

// Halaman utama / Welcome
Route::get('/', function () {
    return view('welcome');
});

// ==========================================
// GROUP UTAMA: DOSEN
// ==========================================
Route::prefix('dosen')->name('dosen.')->group(function () {
    
    // Semua route di dalam file ini otomatis diawali: /dosen/
    require __DIR__.'/dosen.php';
    
});

// ==========================================
// GROUP UTAMA: OPERATOR
// ==========================================
Route::prefix('operator')->name('operator.')->group(function () {
    
    // Semua route di dalam file ini otomatis diawali: /operator/
    // require __DIR__.'/operator.php';
    
});