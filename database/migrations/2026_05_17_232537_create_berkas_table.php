<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

      public function up(): void
      {
            Schema::create('berkas', function (Blueprint $table) {

                  $table->id('id_berkas');

                  $table->string('nama_berkas', 255);

                  $table->string('jenis_berkas', 255)->nullable();

                  $table->string('nomor_berkas', 255)->nullable();

                  $table->string('file_path', 255)->nullable();

                  $table->unsignedBigInteger('id_pegawai');

                  $table->unsignedBigInteger('id_pengajuan')->nullable();

                  $table->unsignedBigInteger('id_jabfung')->nullable();
                  $table->unsignedBigInteger('id_panggol')->nullable();

                  // INI YANG DIPERBAIKI
                  $table->unsignedBigInteger('id_surat_tugas')->nullable();

                  $table->foreign('id_pegawai')
                        ->references('id_pegawai')
                        ->on('pegawai')
                        ->onDelete('cascade');

                  $table->foreign('id_pengajuan')
                        ->references('id_pengajuan')
                        ->on('pengajuan_kenaikan')
                        ->onDelete('cascade');

                  $table->foreign('id_jabfung')
                        ->references('id_jabfung')
                        ->on('jabatan_fungsional')
                        ->nullOnDelete();

                  $table->foreign('id_panggol')
                        ->references('id_panggol')
                        ->on('pangkat_golongan')
                        ->nullOnDelete();

                  // INI JUGA DIPERBAIKI
                  $table->foreign('id_surat_tugas')
                        ->references('id_surat_tugas')
                        ->on('surat_tugas')
                        ->nullOnDelete();

            });
      }

      public function down(): void
      {
            Schema::dropIfExists('berkas');
      }
};