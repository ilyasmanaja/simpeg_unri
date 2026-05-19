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
        Schema::create('ANGGOTA', function (Blueprint $table) {
    $table->id('id_anggota');

    $table->string('jenis_anggota', 255)->nullable();
    $table->string('nama_anggota', 255)->nullable();
    $table->string('id_surat_tugas', 10)->nullable();

    // FIX RELASI SAJA (JANGAN foreignId biar tidak ubah struktur)
    $table->unsignedBigInteger('id_pegawai')->nullable();

    $table->foreign('id_pegawai')
        ->references('id_pegawai')
        ->on('PEGAWAI')
        ->onDelete('cascade');

    $table->foreign('id_surat_tugas')
        ->references('id_surat_tugas')
        ->on('SURAT_TUGAS')
        ->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
