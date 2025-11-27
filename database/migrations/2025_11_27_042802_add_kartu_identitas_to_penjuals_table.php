<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjuals', function (Blueprint $table) {
            $table->string('kartu_identitas')
                  ->nullable()
                  ->after('nama_toko'); // letakkan setelah kolom longitude
        });
    }

    public function down(): void
    {
        Schema::table('penjuals', function (Blueprint $table) {
            $table->dropColumn('kartu_identitas');
        });
    }
};
