<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembeli', function (Blueprint $table) {
            $table->id('idPembeli');

            // foreign key ke users.id
            $table->unsignedBigInteger('idUser');
            $table->foreign('idUser')->references('id')->on('users')->onDelete('cascade');

            $table->string('nama_pembeli')->nullable();
            $table->text('alamat')->nullable();

            // latitude DECIMAL(10,8)
            $table->decimal('latitude', 10, 8)->nullable();

            // longitude DECIMAL(11,8)
            $table->decimal('longitude', 11, 8)->nullable();

            $table->string('no_telp')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembeli');
    }
};
