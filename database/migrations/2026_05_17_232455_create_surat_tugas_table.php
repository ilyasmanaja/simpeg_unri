<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_tugas', function (Blueprint $table) {

            $table->id('id_surat_tugas');

            $table->date('waktu_pelaksanaan');
            $table->integer('lama_pelaksanaan');

            $table->string('perihal');

            $table->string('berkas')->nullable();

            $table->string('status')->nullable();

            $table->text('alasan_penolakan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_tugas');
    }
};