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
            $table->id('id_pengajuan');
            $table->foreignId('id_pegawai');
            $table->string('jenis_pengajuan', 20);
            $table->foreignId('target_panggol')->nullable();
            $table->foreignId('target_jabfung')->nullable();
            $table->string('status_pengajuan', 50)->default('PENDING');
            $table->string('keterangan_tambahan', 255)->nullable();

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
        Schema::dropIfExists('pengajuan_kenaikan');
    }
};
