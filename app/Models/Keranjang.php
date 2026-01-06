<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $table = 'keranjang';

    protected $fillable = [
        'id_user',
        'id_produk',
        'jumlah',
    ];

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';

    // relasi ke produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }

    // relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
