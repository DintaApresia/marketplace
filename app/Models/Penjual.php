<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjual extends Model
{
    use HasFactory;

    protected $table = 'penjuals'; // nama tabel sesuai migration

    protected $fillable = [
        'user_id',
        'nama_penjual',
        'no_telp',
        'alamat',
        'latitude',
        'longitude',
        'nama_toko',
        'alamat_toko',
        'rekening',
        'nama_rekening',
        'kartu_identitas',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    // Relasi ke user (akun yang sama dengan pembeli)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');

    }

}
