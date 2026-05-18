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
        Schema::create('USER_ROLE', function (Blueprint $table) {
            $table->integer('id_role');
            $table->integer('id_user');
            $table->primary(['id_role', 'id_user']);

            $table->foreign('id_role')->references('id_role')->on('ROLE')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('USER_MANAGE')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_role');
    }
};
