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
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('kategori_id')->constrained('kategori')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('kategori_lainnya', 150)->nullable();
            $table->text('deskripsi');
            $table->text('saran_penyelesaian')->nullable();
            $table->enum('metode_identitas', ['identitas', 'anonim'])->default('identitas');
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'ditolak'])->default('menunggu');
            $table->boolean('pernyataan_disetujui')->default(false);
            $table->dateTime('waktu_persetujuan')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->timestamps();

            $table->index('status', 'idx_pengajuan_status');
            $table->index('metode_identitas', 'idx_pengajuan_metode_identitas');
            $table->index('created_at', 'idx_pengajuan_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
