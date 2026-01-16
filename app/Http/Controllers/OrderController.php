<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pembeli;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Keranjang;
use App\Models\ProdukRating;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->where('user_id', Auth::id()) // kalau kolommu id_user, ganti jadi ->where('id_user', Auth::id())
            ->with([
                'items.produk.penjual', // sesuaikan relasi penjual
            ])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pembeli.orders', compact('orders'));
    }
    // =========================
    // Helper hitung jarak (KM)
    // =========================
    private function hitungJarakKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function hitungOngkir(?float $latPenjual, ?float $lngPenjual, ?float $latPembeli, ?float $lngPembeli): float
    {
        if (!$latPenjual || !$lngPenjual || !$latPembeli || !$lngPembeli) {
            // kalau koordinat tidak lengkap, kamu bisa pilih:
            // return 0;
            // atau throw/return error di controller
            return 0;
        }

        $jarak = $this->hitungJarakKm($latPenjual, $lngPenjual, $latPembeli, $lngPembeli);

        if ($jarak <= 5) return 0;

        // default kalau >= 10 km (ubah sesuai aturanmu)
        return 8000;
    }

    /**
     * GET /checkout
     * view: resources/views/pembeli/checkout.blade.php
     */
    public function checkout()
    {
        // ambil keranjang dari DB
        $items = Keranjang::with('produk')
            ->where('id_user', Auth::id())
            ->get();

        $cart = $items->mapWithKeys(function ($k) {
            $p = $k->produk;
            if (!$p) return [];

            return [$p->id => [
                'id'     => (int) $p->id,
                'nama'   => $p->nama_barang,
                'harga'  => (float) $p->harga,  // harga decimal
                'gambar' => $p->gambar,
                'stok'   => (int) $p->stok,
                'qty'    => (int) $k->jumlah,
            ]];
        })->toArray();

        if (empty($cart)) {
            return redirect()->route('pembeli.keranjang')
                ->with('error', 'Keranjang masih kosong.');
        }

        $pembeli = Pembeli::where('idUser', Auth::id())->first();

        $subtotal = collect($cart)->sum(fn($i) => $i['harga'] * $i['qty']);

        // ambil penjual dari produk pertama (buat rekening transfer + ongkir)
        $firstProdukId = array_key_first($cart);
        $produk = Produk::with('penjual')->findOrFail($firstProdukId);
        $penjual = $produk->penjual;

        $ongkir = $this->hitungOngkir(
            $penjual->latitude ?? null,
            $penjual->longitude ?? null,
            $pembeli->latitude ?? null,
            $pembeli->longitude ?? null
        );

        $total = $subtotal + $ongkir;

        return view('pembeli.checkout', compact('cart', 'pembeli', 'subtotal', 'ongkir', 'total', 'penjual'));
    }


    /**
     * POST /order/simpan
     */
    public function simpan(Request $request)
    {
        $request->validate([
            'catatan' => ['nullable','string','max:500'],
            'metode_pembayaran' => ['required','in:cod,transfer'],
            'bukti_pembayaran' => [
                'required_if:metode_pembayaran,transfer',
                'image','mimes:jpg,jpeg,png,webp','max:2048',
            ],
        ]);

        $pembeli = Pembeli::where('idUser', Auth::id())->first();
        if (!$pembeli || !$pembeli->nama_pembeli || !$pembeli->no_telp || !$pembeli->alamat) {
            return back()->with('error', 'Lengkapi data pembeli dulu.');
        }

        $items = Keranjang::with('produk')
            ->where('id_user', Auth::id())
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('pembeli.keranjang')->with('error', 'Keranjang kosong.');
        }

        /** 🔽 INI PENTING */
        $order = DB::transaction(function () use ($items, $pembeli, $request) {

            $firstProduk = $items->first()->produk;
            $produkFirst = Produk::with('penjual')->lockForUpdate()->findOrFail($firstProduk->id);
            $penjual = $produkFirst->penjual;

            $subtotal = 0;
            foreach ($items as $k) {
                $p = Produk::lockForUpdate()->find($k->produk->id);
                if ((int)$p->stok < (int)$k->jumlah) {
                    throw new \Exception("Stok {$p->nama_barang} tidak cukup");
                }
                $subtotal += ((float)$p->harga) * (int)$k->jumlah;
            }

            $ongkir = $this->hitungOngkir(
                $penjual->latitude ?? null,
                $penjual->longitude ?? null,
                $pembeli->latitude ?? null,
                $pembeli->longitude ?? null
            );

            $total = $subtotal + $ongkir;

            $buktiPath = null;
            if ($request->metode_pembayaran === 'transfer' && $request->hasFile('bukti_pembayaran')) {
                $buktiPath = $request->file('bukti_pembayaran')
                    ->store('bukti_pembayaran', 'public');
            }

            $order = Order::create([
                'user_id'           => Auth::id(),
                'nama_penerima'     => $pembeli->nama_pembeli,
                'no_hp'             => $pembeli->no_telp,
                'alamat_pengiriman' => $pembeli->alamat,
                'subtotal'          => $subtotal,
                'ongkir'            => $ongkir,
                'total_bayar'       => $total,
                'status_pesanan'    => 'menunggu',
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => $request->metode_pembayaran === 'transfer'
                                        ? 'menunggu_verifikasi'
                                        : 'belum_bayar',
                'bukti_pembayaran'  => $buktiPath,
                'catatan'           => $request->catatan,
            ]);

            foreach ($items as $k) {
                $p = Produk::lockForUpdate()->find($k->produk->id);

                OrderItem::create([
                    'order_id'      => $order->id,
                    'produk_id'     => $p->id,
                    'nama_barang'   => $p->nama_barang,
                    'harga_satuan'  => $p->harga,
                    'jumlah'        => $k->jumlah,
                    'subtotal_item' => ((float)$p->harga) * (int)$k->jumlah,
                ]);

                $p->stok -= (int)$k->jumlah;
                $p->save();
            }

            Keranjang::where('id_user', Auth::id())->delete();

            return $order; // ⬅️ RETURN DATA, BUKAN REDIRECT
        });

        // ✅ REDIRECT DI LUAR TRANSACTION
        return redirect()
            ->route('pembeli.orders.sukses', $order->id)
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    /**
     * GET /order/{orderId}/sukses
     * (opsional)
     */
    public function sukses($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pembeli.order_sukses', compact('order'));
    }

    public function selesai(Order $order)
    {
        // Validasi kepemilikan
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Hanya boleh dari status dikirim
        if (($order->status_pesanan ?? $order->status) !== 'dikirim') {
            return back()->with('error', 'Pesanan belum dapat diselesaikan.');
        }

        $order->update([
            'status_pesanan'  => 'selesai',
            'tanggal_selesai' => now(),
        ]);

        // Kembali ke riwayat pesanan (BUKAN detail)
        return back()->with('success', 'Pesanan berhasil diselesaikan.');
    }

    public function show(Order $order)
    {
        // Validasi kepemilikan
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Halaman detail hanya untuk selesai
        $status = $order->status_pesanan ?? $order->status;

        if ($status !== 'selesai') {
            abort(404);
        }

        $order->load([
            'items.produk',
            'ratings',
        ]);

        $rated = $order->ratings
            ->whereNotNull('produk_id')
            ->keyBy('produk_id');

        return view('pembeli.order_selesai', compact('order', 'rated'));
    }

    /**
     * SIMPAN / UPDATE RATING + UPLOAD MULTI GAMBAR
     */
    public function storeRating(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        $status = $order->status_pesanan ?? $order->status ?? '';
        if ($status !== 'selesai') {
            return back()->with('error', 'Rating hanya bisa diberikan jika pesanan selesai.');
        }

        $validated = $request->validate([
            'produk_id' => 'required|integer',
            'rating'    => 'required|integer|min:1|max:5',
            'review'    => 'nullable|string|max:2000',
            'review_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $produkId = (int) $validated['produk_id'];

        // pastikan produk ada di order
        if (!$order->items()->where('produk_id', $produkId)->exists()) {
            return back()->with('error', 'Produk tidak ditemukan.');
        }

        // CEGAH DUPLIKAT REVIEW
        $already = ProdukRating::where([
            'user_id'   => Auth::id(),
            'order_id'  => $order->id,
            'produk_id' => $produkId,
        ])->exists();

        if ($already) {
            return back()->with('error', 'Review sudah ada. Silakan edit review.');
        }

        // upload gambar
        $images = [];
        if ($request->hasFile('review_images')) {
            foreach ($request->file('review_images') as $img) {
                if ($img->isValid()) {
                    $images[] = $img->store('review_images', 'public');
                }
            }
        }

        ProdukRating::create([
            'user_id'       => Auth::id(),
            'order_id'      => $order->id,
            'produk_id'     => $produkId,
            'rating'        => (int) $validated['rating'],
            'review'        => $validated['review'] ?? null,
            'review_images' => !empty($images) ? $images : null,
        ]);

        return back()->with('success', 'Review berhasil dikirim.');
    }

    /* ============================
     | UPDATE – EDIT REVIEW SAJA
     ============================ */
    public function updateRating(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        $status = $order->status_pesanan ?? $order->status ?? '';
        if ($status !== 'selesai') {
            return back()->with('error', 'Review hanya bisa diubah jika pesanan selesai.');
        }

        $validated = $request->validate([
            'produk_id' => 'required|integer',
            'rating'    => 'required|integer|min:1|max:5',
            'review'    => 'nullable|string|max:2000',
            'review_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'delete_images'   => 'nullable|array',
            'delete_images.*' => 'string',
        ]);

        $produkId = (int) $validated['produk_id'];

        $rating = ProdukRating::where([
            'user_id'   => Auth::id(),
            'order_id'  => $order->id,
            'produk_id' => $produkId,
        ])->firstOrFail();

        $images = $rating->review_images ?? [];
        if (!is_array($images)) {
            $images = json_decode($images, true) ?: [];
        }

        // hapus gambar lama
        if ($request->filled('delete_images')) {
            foreach ($request->delete_images as $img) {
                if (in_array($img, $images)) {
                    Storage::disk('public')->delete($img);
                }
            }
            $images = array_values(array_diff($images, $request->delete_images));
        }

        // upload gambar baru
        if ($request->hasFile('review_images')) {
            foreach ($request->file('review_images') as $img) {
                if ($img->isValid()) {
                    $images[] = $img->store('review_images', 'public');
                }
            }
        }

        $rating->update([
            'rating'        => (int) $validated['rating'],
            'review'        => $validated['review'] ?? null,
            'review_images' => !empty($images) ? array_values(array_unique($images)) : null,
        ]);

        return back()->with('success', 'Review berhasil diperbarui.');
    }


}
