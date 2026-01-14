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

    protected static function boot()
    {
        parent :: boot();
        
        static::saving(function ($product) {

            // 🔴 Jika stok 0 → otomatis nonaktif
            if ($product->stok <= 0) {
                $product->is_active = 0;
            }

            // 🟢 (opsional) Jika stok > 0 → aktif kembali
            if ($product->stok > 0 && $product->is_active === null) {
                $product->is_active = 1;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penjual()
    {
        return $this->hasOne(Penjual::class, 'user_id', 'user_id');
    }

    public function ratings()
    {
        return $this->hasMany(ProdukRating::class, 'produk_id');
    }

     public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'produk_id');
    }
}
