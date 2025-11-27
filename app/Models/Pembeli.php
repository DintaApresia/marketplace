<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembeli extends Model
{
    use HasFactory;

    protected $table = 'pembeli';
    protected $primaryKey = 'idPembeli';   // sesuai struktur DB
    public $timestamps = true;

    protected $fillable = [
        'idUser',
        'nama_pembeli',
        'alamat',
        'latitude',
        'longitude',
        'no_telp',     // SAMA persis dengan nama kolom di DB
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }
}