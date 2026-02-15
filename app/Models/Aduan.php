<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aduan extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'penjual_id',
        'judul',
        'deskripsi',
        'bukti',
        'status_pesanan_saat_aduan',
        'status_aduan',
        'catatan_penjual',
        'catatan_admin',
        'last_actor_role',
        'last_actor_id',
    ];

    protected $casts = [
        'bukti' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
