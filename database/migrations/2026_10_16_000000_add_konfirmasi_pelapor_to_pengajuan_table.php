<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            // null = belum direspons, dikonfirmasi | keberatan | otomatis
            $table->string('konfirmasi_pelapor', 20)->nullable();
            $table->timestamp('dikonfirmasi_pada')->nullable();
        });

        // Laporan yang sudah selesai sebelum fitur ini ada jangan tiba-tiba
        // muncul sebagai "menunggu konfirmasi".
        DB::table('pengajuan')
            ->where('status', 'selesai')
            ->update(['konfirmasi_pelapor' => 'otomatis']);
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn(['konfirmasi_pelapor', 'dikonfirmasi_pada']);
        });
    }
};
