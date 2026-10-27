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
            $table->unsignedTinyInteger('jumlah_keberatan')->default(0)->after('konfirmasi_pelapor');
        });

        // Set jumlah_keberatan = 1 untuk pengaduan yang sedang atau pernah mengajukan keberatan
        DB::table('pengajuan')
            ->where('konfirmasi_pelapor', 'keberatan')
            ->update(['jumlah_keberatan' => 1]);
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn('jumlah_keberatan');
        });
    }
};
