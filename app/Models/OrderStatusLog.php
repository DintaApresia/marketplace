<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'actor_role',
        'actor_id',
        'catatan',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

