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
        Schema::create('konfirmasi_penyelesaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->enum('jenis_konfirmasi', ['setuju_selesai', 'keberatan', 'minta_tindak_lanjut']);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['pengajuan_id', 'created_at'], 'idx_konfirmasi_pengajuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfirmasi_penyelesaian');
    }
};
