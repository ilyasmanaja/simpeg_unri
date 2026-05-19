<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\DataDiriController;
use App\Http\Controllers\Dosen\JabatanFungsionalController;

// URL: /dosen/data-diri
Route::get('/data-diri', [DataDiriController::class, 'index'])->name('datadiri.index');

// URL: /dosen/jabatanfungsional
Route::prefix('jabatanfungsional')->name('jabatanfungsional.')->group(function () {

    // Baris ini yang menghandle URL /dosen/jabatanfungsional
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