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
        // 1. Tambah is_internal pada tabel tanggapan
        Schema::table('tanggapan', function (Blueprint $table) {
            if (! Schema::hasColumn('tanggapan', 'is_internal')) {
                $table->boolean('is_internal')->default(false)->after('isi_tanggapan');
            }
        });

        // 2. Tambah tanggal_selesai pada tabel pengajuan
        Schema::table('pengajuan', function (Blueprint $table) {
            if (! Schema::hasColumn('pengajuan', 'tanggal_selesai')) {
                $table->dateTime('tanggal_selesai')->nullable()->after('waktu_persetujuan');
            }
        });

        // 3. Tambah kolom tipe pada tabel notifikasi
        Schema::table('notifikasi', function (Blueprint $table) {
            if (! Schema::hasColumn('notifikasi', 'tipe')) {
                $table->string('tipe', 50)->nullable()->after('pesan');
            }
        });

        // 4. Perbarui ENUM status di MySQL untuk mendukung status ditolak
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pengajuan MODIFY COLUMN status ENUM('menunggu', 'diproses', 'selesai', 'ditolak') NOT NULL DEFAULT 'menunggu'");
            DB::statement("ALTER TABLE riwayat_status MODIFY COLUMN status_sebelumnya ENUM('menunggu', 'diproses', 'selesai', 'ditolak') NULL");
            DB::statement("ALTER TABLE riwayat_status MODIFY COLUMN status_baru ENUM('menunggu', 'diproses', 'selesai', 'ditolak') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE riwayat_status MODIFY COLUMN status_baru ENUM('menunggu', 'diproses', 'selesai') NOT NULL");
            DB::statement("ALTER TABLE riwayat_status MODIFY COLUMN status_sebelumnya ENUM('menunggu', 'diproses', 'selesai') NULL");
            DB::statement("ALTER TABLE pengajuan MODIFY COLUMN status ENUM('menunggu', 'diproses', 'selesai') NOT NULL DEFAULT 'menunggu'");
        }

        Schema::table('notifikasi', function (Blueprint $table) {
            if (Schema::hasColumn('notifikasi', 'tipe')) {
                $table->dropColumn('tipe');
            }
        });

        Schema::table('pengajuan', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuan', 'tanggal_selesai')) {
                $table->dropColumn('tanggal_selesai');
            }
        });

        Schema::table('tanggapan', function (Blueprint $table) {
            if (Schema::hasColumn('tanggapan', 'is_internal')) {
                $table->dropColumn('is_internal');
            }
        });
    }
};
