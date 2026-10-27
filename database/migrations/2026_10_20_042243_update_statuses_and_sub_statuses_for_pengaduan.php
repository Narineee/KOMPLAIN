<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Perbarui tahapan sub-status 'identifikasi' dan 'klarifikasi' menjadi 'identifikasi_klarifikasi'
        DB::table('pengajuan')
            ->whereIn('sub_status', ['identifikasi', 'klarifikasi'])
            ->update(['sub_status' => 'identifikasi_klarifikasi']);

        // 2. Hapus status 'ditolak' dan sub-status 'selesai_ditangani' / 'selesai_ditolak', semuanya menjadi status 'selesai'
        DB::table('pengajuan')
            ->whereIn('sub_status', ['selesai_ditolak', 'selesai_ditangani'])
            ->orWhere('status', 'ditolak')
            ->update([
                'status' => 'selesai',
                'sub_status' => null,
            ]);

        // 3. Selaraskan data riwayat status
        DB::table('riwayat_status')
            ->whereIn('sub_status_sebelumnya', ['identifikasi', 'klarifikasi'])
            ->update(['sub_status_sebelumnya' => 'identifikasi_klarifikasi']);

        DB::table('riwayat_status')
            ->whereIn('sub_status_baru', ['identifikasi', 'klarifikasi'])
            ->update(['sub_status_baru' => 'identifikasi_klarifikasi']);

        DB::table('riwayat_status')
            ->whereIn('sub_status_sebelumnya', ['selesai_ditolak', 'selesai_ditangani'])
            ->update(['sub_status_sebelumnya' => null]);

        DB::table('riwayat_status')
            ->whereIn('sub_status_baru', ['selesai_ditolak', 'selesai_ditangani'])
            ->update(['sub_status_baru' => null]);

        DB::table('riwayat_status')
            ->where('status_sebelumnya', 'ditolak')
            ->update(['status_sebelumnya' => 'selesai']);

        DB::table('riwayat_status')
            ->where('status_baru', 'ditolak')
            ->update(['status_baru' => 'selesai']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak diperlukan rollback khusus untuk normalisasi data teks
    }
};
