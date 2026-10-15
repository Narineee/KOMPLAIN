<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuan', 'nomor_tiket')) {
                $table->dropColumn('nomor_tiket');
            }

            if (Schema::hasColumn('pengajuan', 'judul')) {
                $table->dropColumn('judul');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            if (! Schema::hasColumn('pengajuan', 'nomor_tiket')) {
                $table->string('nomor_tiket', 40)->nullable()->unique();
            }

            if (! Schema::hasColumn('pengajuan', 'judul')) {
                $table->string('judul', 200)->nullable();
            }
        });
    }
};
