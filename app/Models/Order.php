<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'nama_penerima',
        'no_hp',
        'alamat_pengiriman',
        'subtotal',
        'ongkir',
        'total_bayar',
        'metode_pembayaran',
        'status_pembayaran',
        'status_pesanan',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function ratings()
    {
        return $this->hasMany(ProdukRating::class);
    }
}
