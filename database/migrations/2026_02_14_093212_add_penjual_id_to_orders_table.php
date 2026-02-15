<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('penjual_id')
                ->nullable()
                ->after('user_id')
                ->constrained('penjuals')
                ->cascadeOnDelete();

            $table->index(['penjual_id', 'status_pesanan']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['penjual_id']);
            $table->dropIndex(['penjual_id', 'status_pesanan']);
            $table->dropColumn('penjual_id');
        });
    }
};
