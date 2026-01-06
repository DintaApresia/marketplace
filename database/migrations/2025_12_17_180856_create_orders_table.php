<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // relasi user
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // snapshot data pembeli (diambil dari tabel pembeli saat checkout)
            $table->string('nama_penerima', 100);
            $table->string('no_hp', 25);
            $table->text('alamat_pengiriman');

            // ringkasan harga
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('ongkir')->default(0);
            $table->unsignedBigInteger('total_bayar')->default(0);

            // pembayaran
            $table->string('metode_pembayaran', 50)->nullable(); 
            $table->string('status_pembayaran', 20)->default('belum_bayar');
            // belum_bayar | menunggu | dibayar | gagal | dibatalkan

            // status order
            $table->string('status_pesanan', 20)->default('pending');
            // pending | diproses | dikirim | selesai | dibatalkan

            // catatan tambahan
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status_pesanan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
