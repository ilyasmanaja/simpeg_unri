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
        Schema::create('PEGAWAI', function (Blueprint $table) {
            $table->id('id_pegawai'); // AUTO INCREMENT BIGINT UNSIGNED

            $table->string('nama_lengkap', 255);
            $table->string('foto', 255)->nullable();
            $table->string('nik', 16)->unique()->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('nomor_hp', 20)->nullable();
            $table->string('jurusan', 255)->nullable();
            $table->string('prodi', 255)->nullable();
            $table->string('nomor_hp_darurat', 20)->nullable();
            $table->string('nidn', 10)->nullable();
            $table->string('nip', 18)->unique()->nullable();
            $table->enum('status_pegawai', ['ASN', 'Non ASN']);

            $table->string('id_jabfung', 10)->nullable();
            $table->string('id_panggol', 10)->nullable();

            $table->foreign('id_jabfung')
                ->references('id_jabfung')
                ->on('JABATAN_FUNGSIONAL');

            $table->foreign('id_panggol')
                ->references('id_panggol')
                ->on('PANGKAT_GOLONGAN');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
