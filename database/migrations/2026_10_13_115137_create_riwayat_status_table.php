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
        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('status_sebelumnya', ['menunggu', 'diproses', 'selesai'])->nullable();
            $table->enum('status_baru', ['menunggu', 'diproses', 'selesai']);
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['pengajuan_id', 'created_at'], 'idx_riwayat_pengajuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_status');
    }
};
