<?php

namespace App\Http\Controllers;

use App\Models\Produk;

class PublicDashboardController extends Controller
{
    public function index()
    {
        $produk = Produk::with('penjual')
            ->where('is_active', true)
            ->where('stok', '>', 0)
            ->latest()
            ->get();

        return view('dashboard', compact('produk'));
    }
}
