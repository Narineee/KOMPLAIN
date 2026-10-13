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
        Schema::create('tanggapan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->text('isi_tanggapan');
            $table->timestamps();

            $table->index('pengajuan_id', 'idx_tanggapan_pengajuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanggapan');
    }
};
