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
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pengajuan MODIFY COLUMN status ENUM('belum_verifikasi', 'verifikasi', 'menunggu', 'diproses', 'selesai', 'ditolak') NOT NULL DEFAULT 'belum_verifikasi'");
            DB::statement("ALTER TABLE riwayat_status MODIFY COLUMN status_sebelumnya ENUM('belum_verifikasi', 'verifikasi', 'menunggu', 'diproses', 'selesai', 'ditolak') NULL");
            DB::statement("ALTER TABLE riwayat_status MODIFY COLUMN status_baru ENUM('belum_verifikasi', 'verifikasi', 'menunggu', 'diproses', 'selesai', 'ditolak') NOT NULL DEFAULT 'belum_verifikasi'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE riwayat_status MODIFY COLUMN status_baru ENUM('menunggu', 'diproses', 'selesai', 'ditolak') NOT NULL");
            DB::statement("ALTER TABLE riwayat_status MODIFY COLUMN status_sebelumnya ENUM('menunggu', 'diproses', 'selesai', 'ditolak') NULL");
            DB::statement("ALTER TABLE pengajuan MODIFY COLUMN status ENUM('menunggu', 'diproses', 'selesai', 'ditolak') NOT NULL DEFAULT 'menunggu'");
        }
    }
};
