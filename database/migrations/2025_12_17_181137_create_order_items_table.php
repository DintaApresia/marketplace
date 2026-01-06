<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('produk_id')
                ->constrained('produk') // ✅ sesuai tabel kamu
                ->restrictOnDelete();

            // Snapshot barang (diambil dari tabel produk saat checkout)
            $table->string('nama_barang', 255);
            $table->decimal('harga_satuan', 12, 2);
            $table->unsignedInteger('jumlah');
            $table->decimal('subtotal_item', 12, 2);

            $table->timestamps();

            $table->index(['order_id']);
            $table->index(['produk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
