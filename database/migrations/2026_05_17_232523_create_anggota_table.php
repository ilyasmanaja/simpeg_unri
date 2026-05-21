<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {

            $table->id('id_anggota');

            $table->string('nama_anggota')->nullable();

            $table->unsignedBigInteger('id_surat_tugas');

            $table->integer('id_pegawai')->nullable();

            $table->foreign('id_surat_tugas')
                  ->references('id_surat_tugas')
                  ->on('surat_tugas')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};