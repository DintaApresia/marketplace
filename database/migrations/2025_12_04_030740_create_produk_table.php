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
        Schema::create('produk', function (Blueprint $table) {
            $table->id();

            // kalau produk milik user/penjual tertentu
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('nama_barang');              // nama barang bekas
            $table->text('deskripsi')->nullable(); // deskripsi
            $table->decimal('harga', 12, 2);     // harga
            $table->integer('stok')->default(1); // stok (barang bekas biasanya 1)
            $table->string('gambar')->nullable();  // path foto
            $table->boolean('is_active')->default(true); // status tampil / tidak

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
