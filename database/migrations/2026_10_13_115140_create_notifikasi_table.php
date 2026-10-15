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
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('pengajuan_id')->nullable()->constrained('pengajuan')->nullOnDelete()->cascadeOnUpdate();
            $table->string('judul', 150);
            $table->text('pesan');
            $table->string('tipe', 50)->nullable();
            $table->dateTime('dibaca_pada')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'dibaca_pada'], 'idx_notifikasi_user_dibaca');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
