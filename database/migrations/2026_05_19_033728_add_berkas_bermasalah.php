<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('PENGAJUAN_KENAIKAN', function (Blueprint $table) {
            $table->json('berkas_bermasalah')->nullable()->after('keterangan_tambahan');
            // Menyimpan array berkas yang ditandai operator, contoh: ["sk_cpns","pak"]
            // Alasan revisi menggunakan kolom keterangan_tambahan yang sudah ada
        });
    }

    public function down(): void
    {
        Schema::table('PENGAJUAN_KENAIKAN', function (Blueprint $table) {
            $table->dropColumn('berkas_bermasalah');
        });
    }
};