<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        DB::statement("
            UPDATE produk
            INNER JOIN penjuals
                ON produk.user_id = penjuals.user_id
            SET produk.penjual_id = penjuals.id
            WHERE produk.penjual_id IS NULL
        ");
    }

    public function down(): void
    {
        DB::statement("UPDATE produk SET penjual_id = NULL");
    }
};
