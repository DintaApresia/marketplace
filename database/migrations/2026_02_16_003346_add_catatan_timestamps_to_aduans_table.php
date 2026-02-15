<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aduans', function (Blueprint $table) {
            $table->timestamp('tgl_catatan_penjual')->nullable()->after('catatan_penjual');
            $table->timestamp('tgl_catatan_admin')->nullable()->after('catatan_admin');
        });
    }

    public function down(): void
    {
        Schema::table('aduans', function (Blueprint $table) {
            $table->dropColumn(['tgl_catatan_penjual', 'tgl_catatan_admin']);
        });
    }
};
