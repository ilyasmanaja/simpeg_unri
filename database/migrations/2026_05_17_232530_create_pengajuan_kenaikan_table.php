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
        Schema::create('PENGAJUAN_KENAIKAN', function (Blueprint $table) {
            $table->integer('id_pengajuan')->primary();
            $table->integer('id_pegawai');

            // 'jabfung' untuk pengajuan jabatan fungsional, 'pangkat' untuk pangkat/golongan
            $table->string('jenis_pengajuan', 20);

            $table->string('target_panggol', 10)->nullable();
            $table->string('target_jabfung', 10)->nullable();

            // Status alur jabfung:
            // menunggu          → pegawai baru ajukan, belum diproses operator
            // verifikasi        → operator sedang memverifikasi
            // persetujuan       → menunggu persetujuan pimpinan
            // disetujui         → disetujui pimpinan
            // tolak_verifikasi  → ditolak oleh operator (pegawai bisa revisi)
            // tolak_persetujuan → ditolak oleh pimpinan
            $table->string('status_pengajuan', 50)->default('menunggu');

            $table->string('nomor_usulan', 100)->nullable();     // nomor SK / usulan dari pegawai
            $table->string('keterangan_tambahan', 255)->nullable(); // catatan tolak dari operator/pimpinan
            $table->date('tanggal_pengajuan')->nullable();

            $table->foreign('id_pegawai')->references('id_pegawai')->on('PEGAWAI')->onDelete('cascade');
            $table->foreign('target_panggol')->references('id_panggol')->on('PANGKAT_GOLONGAN')->onDelete('set null');
            $table->foreign('target_jabfung')->references('id_jabfung')->on('JABATAN_FUNGSIONAL')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PENGAJUAN_KENAIKAN');
    }
};