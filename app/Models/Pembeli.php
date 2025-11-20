<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembeli extends Model
{
    use HasFactory;

    protected $table = 'pembeli';
    protected $primaryKey = 'idPembeli';   // <- SESUAI TABEL

    protected $fillable = [
        'idUser',
        'nama_pembeli',
        'alamat',
        'latitude',
        'longitude',
        'no_telp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }
}
