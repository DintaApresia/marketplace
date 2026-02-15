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
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('status');
            // contoh: menunggu, diproses, dikemas, dikirim, selesai, dibatalkan

            $table->enum('actor_role', ['penjual', 'pembeli', 'admin'])
                ->nullable();

            $table->unsignedBigInteger('actor_id')
                ->nullable();

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index('order_id');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
    }
};
