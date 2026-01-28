<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->foreign('penjual_id')
                  ->references('id')
                  ->on('penjuals')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropForeign(['penjual_id']);
        });
    }
};
