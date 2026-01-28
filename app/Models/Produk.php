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
        'penjual_id',
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
        parent::boot();

        static::saving(function ($product) {
            if ($product->stok <= 0) {
                $product->is_active = 0;
            } else {
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
        return $this->belongsTo(Penjual::class, 'penjual_id');
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
