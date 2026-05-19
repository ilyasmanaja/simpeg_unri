<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('VERIFIKASI', function (Blueprint $table) {
            $table->id('id_verifikasi');
            $table->string('status_verifikasi', 50)->nullable();
            $table->string('jenis_verifikasi', 50)->nullable();
            $table->date('tanggal_pengajuan')->nullable();
            $table->date('tanggal_proses')->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->json('berkas_bermasalah')->nullable(); // tambah ini
            $table->foreignId('id_berkas');

            $table->foreign('id_berkas')->references('id_berkas')->on('BERKAS')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi');
    }
};
