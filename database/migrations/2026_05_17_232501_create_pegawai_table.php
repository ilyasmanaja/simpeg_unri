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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('foto')->nullable();
            $table->string('nik')->unique();           
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('nomor_hp');
            $table->string('jurusan');
            $table->string('prodi');
            $table->string('no_hp_darurat')->nullable();
            $table->string('nidn')->nullable()->unique();
            $table->string('nip')->nullable()->unique();
            $table->enum('status_pegawai', ['ASN', 'Non ASN']);
            $table->foreignId('jabatan_fungsional_id')
                ->nullable()
                ->constrained('jabatan_fungsionals')
                ->onDelete('set null');

            $table->foreignId('pangkat_golongan_id')
                ->nullable()
                ->constrained('pangkat_golongans')
                ->onDelete('set null');

            $table->timestamps();
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
