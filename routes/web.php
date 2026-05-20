<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ==========================================
// MODULE: AUTHENTICATION (PUBLIC)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Rute Logout (Hanya bisa diakses jika sudah login)
Route::get('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Halaman Utama: Jika belum login otomatis dilempar ke login
Route::get('/', function () {
    return redirect()->route('login');
});


// ==========================================
// GROUP UTAMA: DOSEN & TENDIK (TERKUNCI / PROTECTED)
// ==========================================
// Kita tambahkan ->middleware('auth') agar rute di dalam dosen.php aman dari penyusup
Route::prefix('dosen')->name('dosen.')->middleware('auth')->group(function () {

    // Semua rute di dalam file ini otomatis diawali: /dosen/
    require __DIR__ . '/dosen.php';

});


// ==========================================
// GROUP UTAMA: OPERATOR (TERKUNCI / PROTECTED)
// ==========================================
Route::prefix('operator')->name('operator.')->middleware('auth')->group(function () {

    // Semua rute di dalam file ini otomatis diawali: /operator/
    require __DIR__ . '/operator.php';

});

// ==========================================
// GROUP UTAMA: PIMPINAN (TERKUNCI / PROTECTED)
// ==========================================
Route::prefix('pimpinan')->name('pimpinan.')->middleware('auth')->group(function () {
    // Semua rute di dalam file pimpinan.php otomatis diawali: /pimpinan/ dan nama rute pimpinan.
    require __DIR__ . '/pimpinan.php';
});