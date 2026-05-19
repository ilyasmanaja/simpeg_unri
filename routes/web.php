<?php

// ============================================================
// Tambahkan baris-baris ini ke dalam routes/web.php
// di dalam middleware group auth (atau sesuaikan project kamu)
// ============================================================
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\JabatanFungsionalController;
Route::get('/', function () {
    return redirect('/jabatanfungsional');
});
// Resource route jabatan fungsional
Route::prefix('jabatanfungsional')->name('jabatanfungsional.')->group(function () {

    // READ  — daftar pengajuan                GET  /jabatanfungsional
    Route::get('/',         [JabatanFungsionalController::class, 'index'])->name('index');

    // FORM  — form pengajuan baru             GET  /jabatanfungsional/create
    Route::get('/create',   [JabatanFungsionalController::class, 'create'])->name('create');

    // STORE — simpan pengajuan baru           POST /jabatanfungsional
    Route::post('/',        [JabatanFungsionalController::class, 'store'])->name('store');

    // DETAIL— data untuk modal (iframe)       GET  /jabatanfungsional/{id}/detail
    Route::get('/{id}/detail', function ($id) {
        $controller = app(JabatanFungsionalController::class);
        return $controller->show($id);
    })->name('detail');

    // EDIT  — form edit pengajuan             GET  /jabatanfungsional/{id}/edit
    Route::get('/{id}/edit',   [JabatanFungsionalController::class, 'edit'])->name('edit');

    // UPDATE— simpan perubahan                PUT  /jabatanfungsional/{id}
    Route::put('/{id}',        [JabatanFungsionalController::class, 'update'])->name('update');

    // HAPUS — hapus pengajuan                 DELETE /jabatanfungsional/{id}
    Route::delete('/{id}',     [JabatanFungsionalController::class, 'destroy'])->name('destroy');

    // DOWNLOAD surat (placeholder)            GET  /jabatanfungsional/{id}/download
    Route::get('/{id}/download', function ($id) {
        // TODO: implementasi generate/download surat pengusulan
        return redirect()->route('jabatanfungsional.index')
            ->with('error', 'Fitur download surat belum tersedia.');
    })->name('download');

    Route::get('/tes', function () {
    return 'TES BERHASIL';
});
});