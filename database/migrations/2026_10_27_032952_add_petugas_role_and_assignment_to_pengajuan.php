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
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pegawai', 'admin', 'petugas') NOT NULL DEFAULT 'pegawai'");
        }

        Schema::table('pengajuan', function (Blueprint $table) {
            $table->foreignId('assigned_unit_id')->nullable()->after('kategori_id')->constrained('unit')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('petugas_id')->nullable()->after('assigned_unit_id')->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->text('catatan_penugasan')->nullable()->after('petugas_id');

            $table->index('assigned_unit_id', 'idx_pengajuan_assigned_unit_id');
            $table->index('petugas_id', 'idx_pengajuan_petugas_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropForeign(['assigned_unit_id']);
            $table->dropForeign(['petugas_id']);
            $table->dropIndex('idx_pengajuan_assigned_unit_id');
            $table->dropIndex('idx_pengajuan_petugas_id');
            $table->dropColumn(['assigned_unit_id', 'petugas_id', 'catatan_penugasan']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pegawai', 'admin') NOT NULL DEFAULT 'pegawai'");
        }
    }
};
