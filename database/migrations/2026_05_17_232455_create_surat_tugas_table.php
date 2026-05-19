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
        Schema::create('SURAT_TUGAS', function (Blueprint $table) {
            $table->id('id_surat_tugas');
            $table->date('waktu_pelaksanaan')->nullable();
            $table->date('lama_pelaksanaan')->nullable();
            $table->string('perihal', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_tugas');
    }
};
