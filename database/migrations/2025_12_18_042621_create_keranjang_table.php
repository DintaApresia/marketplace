<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id();

            // relasi ke pengguna
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // relasi ke produk
            $table->foreignId('id_produk')
                  ->constrained('produk')
                  ->cascadeOnDelete();

            // jumlah produk dalam keranjang
            $table->unsignedInteger('jumlah');

            // timestamp versi bahasa Indonesia
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diubah_pada')->useCurrent()->useCurrentOnUpdate();

            // satu produk hanya boleh satu baris per user
            $table->unique(['id_user', 'id_produk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keranjang');
    }
};
