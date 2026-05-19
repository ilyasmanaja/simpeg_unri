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
        Schema::create('BERKAS', function (Blueprint $table) {
            $table->id('id_berkas');
            $table->string('nama_berkas', 255);
            $table->string('jenis_berkas', 255)->nullable();
            $table->string('nomor_berkas', 255)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->foreignId('id_pegawai');
            $table->foreignId('id_pengajuan')->nullable();
            $table->foreignId('id_jabfung')->nullable();
            $table->foreignId('id_panggol')->nullable();
            $table->foreignId('id_surat_tugas')->nullable();

            $table->foreign('id_pegawai')->references('id_pegawai')->on('PEGAWAI')->onDelete('cascade');
            $table->foreign('id_pengajuan')->references('id_pengajuan')->on('PENGAJUAN_KENAIKAN')->onDelete('cascade');
            $table->foreign('id_jabfung')->references('id_jabfung')->on('JABATAN_FUNGSIONAL')->onDelete('set null');
            $table->foreign('id_panggol')->references('id_panggol')->on('PANGKAT_GOLONGAN')->onDelete('set null');
            $table->foreign('id_surat_tugas')->references('id_surat_tugas')->on('SURAT_TUGAS')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berkas');
    }
};
