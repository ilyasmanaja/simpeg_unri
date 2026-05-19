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
        Schema::create('JABATAN_FUNGSIONAL', function (Blueprint $table) {
            $table->string('id_jabfung', 10)->primary();
            $table->string('jenis_jabfung', 255); // nilai: 'dosen' atau 'tendik'
            $table->string('nama_jabfung', 255);
            $table->integer('urutan')->nullable(); // urutan jenjang, hanya untuk dosen (1=Asisten Ahli, 2=Lektor, 3=Lektor Kepala, 4=Guru Besar), tendik = null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('JABATAN_FUNGSIONAL');
    }
};