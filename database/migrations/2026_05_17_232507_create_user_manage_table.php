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
        Schema::create('USER_MANAGE', function (Blueprint $table) {
            $table->id('id_user');
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->foreignId('id_pegawai')->nullable();

            $table->foreign('id_pegawai')->references('id_pegawai')->on('PEGAWAI')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_manage');
    }
};
