<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukRating extends Model
{
    protected $table = 'produk_rating';

    protected $fillable = [
        'user_id',
        'order_id',
        'produk_id',
        'rating',
        'review',
        'review_images',
    ];

    protected $casts = [
        'review_images' => 'array',
    ];

    

}

