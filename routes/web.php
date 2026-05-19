<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\DataDiriController;
use App\Http\Controllers\Operator\PegawaiController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('dosen')->group(function () {
    Route::get('/data-diri', [DataDiriController::class, 'index']);
});

require __DIR__.'/operator.php';