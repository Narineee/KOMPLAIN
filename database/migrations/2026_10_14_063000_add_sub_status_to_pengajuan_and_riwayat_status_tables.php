<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->string('sub_status', 50)->nullable()->after('status');
            $table->index('sub_status', 'idx_pengajuan_sub_status');
        });

        Schema::table('riwayat_status', function (Blueprint $table) {
            $table->string('sub_status_sebelumnya', 50)->nullable()->after('status_sebelumnya');
            $table->string('sub_status_baru', 50)->nullable()->after('status_baru');
        });

        // Migrasikan status 'ditolak' lama menjadi status 'selesai' dengan sub_status 'selesai_ditolak'
        DB::table('pengajuan')
            ->where('status', 'ditolak')
            ->update([
                'status' => 'selesai',
                'sub_status' => 'selesai_ditolak',
            ]);

        // Berikan sub_status default untuk status diproses dan selesai yang belum memiliki sub_status
        DB::table('pengajuan')
            ->where('status', 'diproses')
            ->whereNull('sub_status')
            ->update([
                'sub_status' => 'identifikasi',
            ]);

        DB::table('pengajuan')
            ->where('status', 'selesai')
            ->whereNull('sub_status')
            ->update([
                'sub_status' => 'selesai_ditangani',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_status', function (Blueprint $table) {
            $table->dropColumn(['sub_status_sebelumnya', 'sub_status_baru']);
        });

        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropIndex('idx_pengajuan_sub_status');
            $table->dropColumn('sub_status');
        });
    }
};
