<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pembeli;

class KeranjangController extends Controller
{
    public function index()
    {
        $items = Keranjang::with('produk.penjual')
            ->where('id_user', Auth::id())
            ->get();

        $cart = $items->map(function ($k) {
            $p = $k->produk;

            return [
                'id'         => $k->id_produk,              // ⬅️ ID PRODUK
                'nama'       => $p->nama_barang,
                'harga'      => (float) $p->harga,
                'qty'        => (int) $k->jumlah,
                'stok'       => (int) $p->stok,
                'gambar'     => $p->gambar,
                'penjual_id' => $p->penjual->id,
                'nama_penjual' => $p->penjual->nama_toko ?? $p->penjual->nama_penjual,
            ];
        })->toArray();

        // cek profil pembeli
        $pembeli = Pembeli::where('idUser', Auth::id())->first();

        $profilLengkap = $pembeli &&
            $pembeli->nama_pembeli &&
            $pembeli->no_telp &&
            $pembeli->alamat;

        return view('pembeli.keranjang', compact(
            'cart',
            'profilLengkap'
        ));
    }


    public function tambah(Request $request, Produk $produk)
    {
        $request->validate([
            'qty' => ['nullable', 'integer', 'min:1'],
        ]);

        $qtyTambah = (int) ($request->qty ?? 1);

        // Stok harus dari DB
        $stokServer = (int) $produk->stok;
        if ($stokServer <= 0) {
            return back()->with('error', 'Stok habis.');
        }

        $userId = Auth::id();

        DB::transaction(function () use ($userId, $produk, $qtyTambah, $stokServer) {
            $row = Keranjang::firstOrNew([
                'id_user'   => $userId,
                'id_produk' => $produk->id,
            ]);

            $qtySekarang = (int) ($row->jumlah ?? 0);
            $qtyBaru = min($qtySekarang + $qtyTambah, $stokServer);

            $row->jumlah = $qtyBaru;
            $row->save();
        });

        return back()->with('success', 'Berhasil dimasukkan ke keranjang!');
    }

    public function ubahKeranjangAjax(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:plus,minus',
        ]);

        $userId = Auth::id();

        // Ambil produk & stok terbaru
        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json([
                'ok' => false,
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        $stokServer = (int) $produk->stok;
        if ($stokServer <= 0) {
            return response()->json([
                'ok' => false,
                'message' => 'Stok habis.',
                'stok' => 0,
            ], 422);
        }

        $row = Keranjang::where('id_user', $userId)
            ->where('id_produk', $produk->id)
            ->first();

        if (!$row) {
            return response()->json([
                'ok' => false,
                'message' => 'Item tidak ditemukan di keranjang.',
            ], 404);
        }

        $qty = (int) $row->jumlah;

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
        } else { // minus
            $qty = max(1, $qty - 1);
        }

        $row->update(['jumlah' => $qty]);

        return response()->json([
            'ok' => true,
            'message' => 'Qty diperbarui.',
            'id' => (string) $produk->id,
            'qty' => $qty,
            'stok' => $stokServer,
            'itemSubtotal' => ((int) $produk->harga) * $qty,
        ]);
    }

    public function hapus(Produk $produk)
    {
        Keranjang::where('id_user', Auth::id())
            ->where('id_produk', $produk->id)
            ->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
