<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('PENGAJUAN_KENAIKAN', function (Blueprint $table) {
            $table->string('nomor_usulan', 100)->nullable()->after('jenis_pengajuan');
        });
    }

    public function down(): void
    {
        Schema::table('PENGAJUAN_KENAIKAN', function (Blueprint $table) {
            $table->dropColumn('nomor_usulan');
        });
    }
};