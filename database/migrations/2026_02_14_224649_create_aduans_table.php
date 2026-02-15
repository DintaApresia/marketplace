<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aduans', function (Blueprint $table) {
            $table->id();

            // Relasi ke order
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->onDelete('cascade');

            // Pembeli yang mengajukan
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Penjual pemilik order (biar query cepat)
            $table->unsignedBigInteger('penjual_id')->index();

            // Data aduan
            $table->string('judul', 150);
            $table->text('deskripsi');

            // Bukti foto (opsional, bisa lebih dari 1)
            $table->json('bukti')->nullable();

            // Snapshot status pesanan saat aduan dibuat
            $table->enum('status_pesanan_saat_aduan', [
                'menunggu',
                'dikemas',
                'dikirim',
                'selesai',
                'ditolak'
            ])->nullable();

            // Status aduan
            $table->enum('status_aduan', [
                'baru',
                'diproses',
                'selesai',
                'ditolak'
            ])->default('baru')->index();

            // Catatan dari penjual
            $table->text('catatan_penjual')->nullable();

            // Catatan dari admin
            $table->text('catatan_admin')->nullable();

            // Siapa terakhir update
            $table->enum('last_actor_role', [
                'pembeli',
                'penjual',
                'admin'
            ])->nullable();

            $table->unsignedBigInteger('last_actor_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aduans');
    }
};
