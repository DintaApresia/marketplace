<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'penjual_id',
        'nama_penerima',
        'no_hp',
        'alamat_pengiriman',
        'subtotal',
        'ongkir',
        'total_bayar',
        'metode_pembayaran',
        'status_pembayaran',
        'bukti_pembayaran',
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

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class)->orderBy('created_at');
    }

    public function latestStatusLog()
    {
        return $this->hasOne(OrderStatusLog::class)->latestOfMany();
    }

    public function aduans()
    {
        return $this->hasMany(Aduan::class);
    }

    public function aduan()
    {
        return $this->hasOne(Aduan::class, 'order_id');
    }

}
