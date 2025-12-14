<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

class PencarianController extends Controller
{
    public function produk(Request $request)
    {
        $q = trim($request->input('q', ''));

        if ($q === '') {
            return view('pembeli.hasilpencarian', [
                'products' => collect(),
                'query'    => $q,
            ]);
        }

        $products = Produk::query()
            ->where('nama_barang', 'like', "%{$q}%")
            ->orWhere('deskripsi', 'like', "%{$q}%")
            ->limit(50)
            ->get();

        return view('pembeli.hasilpencarian', [
            'products' => $products,
            'query'    => $q,
        ]);
    }
}
