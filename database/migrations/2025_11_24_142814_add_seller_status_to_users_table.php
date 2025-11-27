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
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom role kalau belum ada
            $table->string('role')
                  ->default('pembeli')
                  ->after('password');

            // Tambah kolom seller_status setelah role
            $table->enum('seller_status', ['none', 'pending', 'verified', 'rejected'])
                  ->default('none')
                  ->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('seller_status');
            $table->dropColumn('role');
        });
    }
};
