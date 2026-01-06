<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk_rating', function (Blueprint $table) {
            $table->id();

            // relasi utama
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            // FK ke produk (sesuai OrderItem: produk_id)
            $table->unsignedBigInteger('produk_id');

            // isi rating
            $table->unsignedTinyInteger('rating'); // 1–5
            $table->text('review')->nullable();

            /**
             * gambar review
             * disimpan sebagai JSON array
             * contoh: ["reviews/img1.jpg","reviews/img2.png"]
             */
            $table->json('review_images')->nullable();

            $table->timestamps();

            // foreign key produk
            $table->foreign('produk_id')
                ->references('id')
                ->on('produk')   // 🔴 ganti ke "products" kalau tabelmu bernama products
                ->cascadeOnDelete();

            // 1 user hanya boleh 1 rating per produk per order
            $table->unique(
                ['user_id', 'order_id', 'produk_id'],
                'uniq_user_order_produk_rating'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_rating');
    }
};
