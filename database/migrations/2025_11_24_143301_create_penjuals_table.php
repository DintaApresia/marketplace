<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjuals', function (Blueprint $table) {
            $table->id();

            // relasi ke users (akun yang sama dengan pembeli)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // SALINAN DATA DARI PEMBELI (opsional tapi boleh)
            $table->string('nama_penjual')->nullable();
            $table->string('no_telp', 30)->nullable();
            $table->text('alamat')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // DATA KHUSUS PENJUAL
            $table->string('nama_toko', 100)->nullable();
            $table->text('alamat_toko')->nullable();
            $table->string('rekening', 100)->nullable();
            $table->string('nama_rekening', 100)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjuals');
    }
};
