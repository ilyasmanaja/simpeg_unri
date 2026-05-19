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
    $table->id('id_user_role');

    $table->foreignId('id_user')
        ->constrained('USER_MANAGE', 'id_user')
        ->onDelete('cascade');

    $table->foreignId('id_role')
        ->constrained('ROLE', 'id_role')
        ->onDelete('cascade');
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
