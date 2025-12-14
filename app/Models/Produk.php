<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Penjual;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'user_id',
        'nama_barang',
        'deskripsi',
        'harga',
        'stok',
        'gambar',
        'is_active',
    ];

    protected $casts = [
        'harga'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penjual()
    {
        return $this->hasOne(Penjual::class, 'user_id', 'user_id');
    }
}
