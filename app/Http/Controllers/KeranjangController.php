<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $total = collect($cart)->sum(fn($i) => $i['harga'] * $i['qty']);

        return view('pembeli.keranjang', compact('cart', 'total'));
    }

    public function tambah(Request $request, Produk $produk)
    {
        $request->validate([
            'qty' => ['nullable','integer','min:1'],
        ]);

        if ($produk->stok <= 0) {
            return back()->with('error', 'Stok habis.');
        }

        $qtyTambah = (int) ($request->qty ?? 1);

        $cart = session('cart', []);
        $id = $produk->id;

        $qtySekarang = isset($cart[$id]) ? (int)$cart[$id]['qty'] : 0;
        $qtyBaru = min($qtySekarang + $qtyTambah, $produk->stok);

        $cart[$id] = [
            'id'     => $produk->id,
            'nama'   => $produk->nama_barang,
            'harga'  => (int) $produk->harga,
            'gambar' => $produk->gambar,
            'qty'    => $qtyBaru,
            // ❌ JANGAN simpan stok di session
        ];

        session(['cart' => $cart]);

        return back()->with('success', 'Berhasil dimasukkan ke keranjang!');
    }

    public function ubahKeranjangAjax(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:plus,minus',
        ]);

        $cart = session('cart', []);

        if (!isset($cart[$id])) {
            return response()->json([
                'ok' => false,
                'message' => 'Item tidak ditemukan di keranjang.',
            ], 404);
        }

        // 🔥 STOK SELALU DARI DB
        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json([
                'ok' => false,
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $stokServer = (int) $produk->stok;
        $qty = (int) $cart[$id]['qty'];

        if ($request->action === 'plus') {
            if ($qty >= $stokServer) {
                return response()->json([
                    'ok' => false,
                    'message' => "Stok tidak cukup. Maksimal {$stokServer} pcs.",
                    'qty' => $qty,
                    'stok' => $stokServer,
                ], 422);
            }
            $qty++;
        } else {
            $qty = max(1, $qty - 1);
        }

        // ✅ UPDATE SESSION DENGAN QTY BARU
        $cart[$id]['qty'] = $qty;
        session(['cart' => $cart]);

        return response()->json([
            'ok' => true,
            'message' => 'Qty diperbarui.',
            'id' => (string) $id,
            'qty' => $qty,
            'stok' => $stokServer, // ⬅️ KIRIM STOK REAL KE UI
            'itemSubtotal' => $cart[$id]['harga'] * $qty,
        ]);
    }

    public function hapus(Produk $produk)
    {
        $cart = session('cart', []);
        unset($cart[$produk->id]);

        session(['cart' => $cart]);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
