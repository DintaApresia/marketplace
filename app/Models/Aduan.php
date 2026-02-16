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
        'status_aduan',
        'catatan_admin',
        'tgl_catatan_admin',
        'catatan_penjual',
        'tgl_catatan_penjual',
    ];

    protected $casts = [
        'bukti' => 'array',
        'tgl_catatan_admin' => 'datetime',
        'tgl_catatan_penjual' => 'datetime',
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
