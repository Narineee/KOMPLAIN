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
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 100);
            $table->foreignId('parent_id')->nullable()->constrained('kategori')->nullOnDelete()->cascadeOnUpdate();
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->unique(['parent_id', 'nama_kategori'], 'uq_kategori_parent_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
