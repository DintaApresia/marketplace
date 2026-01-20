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

    public function checkoutLangsung(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'qty'       => 'nullable|integer|min:1',
        ]);

        $qty = $request->qty ?? 1;

        $produk = Produk::with('penjual')->findOrFail($request->produk_id);

        if ($produk->stok < $qty) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $pembeli = Pembeli::where('idUser', Auth::id())->first();
        if (
            !$pembeli ||
            !$pembeli->nama_pembeli ||
            !$pembeli->no_telp ||
            !$pembeli->alamat
        ) {
            return redirect()
                ->route('pembeli.profile')
                ->with('error', 'Lengkapi data profil terlebih dahulu.');
        }

        // 🔥 SIMPAN KE SESSION
        session([
            'checkout_langsung' => [
                'produk_id' => $produk->id,
                'qty'       => $qty,
            ]
        ]);

        // WAJIB redirect ke checkout
        return redirect()->route('pembeli.checkout');
    }

    /*
    |--------------------------------------------------------------------------
    | HALAMAN CHECKOUT (KERANJANG & BELI SEKARANG)
    |--------------------------------------------------------------------------
    */
    public function checkout(Request $request)
    {
        $pembeli = Pembeli::where('idUser', Auth::id())->first();
        if (!$pembeli) {
            return redirect()->route('pembeli.profile');
        }

        $cart = [];
        $subtotal = 0;

        /*
        ==========================
        MODE 1: CHECKOUT LANGSUNG
        ==========================
        */
        if (session()->has('checkout_langsung')) {

            $data = session('checkout_langsung');
            session()->forget('checkout_langsung'); // bersihkan

            $produk = Produk::with('penjual')->findOrFail($data['produk_id']);
            $qty    = $data['qty'];

            $cart[$produk->id] = [
                'id'           => $produk->id,
                'nama'         => $produk->nama_barang,
                'harga'        => (float) $produk->harga,
                'qty'          => $qty,
                'stok'         => (int) $produk->stok,
                'gambar'       => $produk->gambar,
                'nama_penjual' => $produk->penjual->nama_toko
                                  ?? $produk->penjual->nama_penjual
                                  ?? 'Penjual',
            ];

            $subtotal = $produk->harga * $qty;

            $penjual = $produk->penjual;
        }
        /*
        ==========================
        MODE 2: CHECKOUT DARI KERANJANG
        ==========================
        */
        else {

            $selectedItems = $request->input('items');

            if (!$selectedItems || !is_array($selectedItems)) {
                return redirect()
                    ->route('pembeli.keranjang')
                    ->with('error', 'Pilih minimal satu produk untuk checkout.');
            }

            $items = Keranjang::with('produk.penjual')
                ->where('id_user', Auth::id())
                ->whereIn('id_produk', $selectedItems)
                ->get();

            if ($items->isEmpty()) {
                return redirect()->route('pembeli.keranjang');
            }

            // VALIDASI 1 PENJUAL
            if ($items->pluck('produk.penjual_id')->unique()->count() > 1) {
                return redirect()
                    ->route('pembeli.keranjang')
                    ->with('error', 'Checkout hanya dapat dilakukan dari satu penjual.');
            }

            foreach ($items as $k) {
                $p = $k->produk;
                $cart[$p->id] = [
                    'id'           => $p->id,
                    'nama'         => $p->nama_barang,
                    'harga'        => (float) $p->harga,
                    'qty'          => (int) $k->jumlah,
                    'stok'         => (int) $p->stok,
                    'gambar'       => $p->gambar,
                    'nama_penjual' => $p->penjual->nama_toko
                                      ?? $p->penjual->nama_penjual
                                      ?? 'Penjual',
                ];
                $subtotal += $p->harga * $k->jumlah;
            }

            $penjual = $items->first()->produk->penjual;
        }

        // =====================
        // ONGKIR & TOTAL
        // =====================
        $ongkir = $this->hitungOngkir(
            $penjual->latitude ?? null,
            $penjual->longitude ?? null,
            $pembeli->latitude ?? null,
            $pembeli->longitude ?? null
        );

        $total = $subtotal + $ongkir;

        return view('pembeli.checkout', compact(
            'cart',
            'pembeli',
            'subtotal',
            'ongkir',
            'total',
            'penjual'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN ORDER (FINAL)
    |--------------------------------------------------------------------------
    */
    public function simpan(Request $request)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:cod,transfer',
            'catatan' => 'nullable|string|max:500',
            'bukti_pembayaran' => [
                'required_if:metode_pembayaran,transfer',
                'image','mimes:jpg,jpeg,png,webp','max:2048'
            ],
        ]);

        $pembeli = Pembeli::where('idUser', Auth::id())->first();
        if (!$pembeli) {
            return redirect()->route('pembeli.profile');
        }

        $order = DB::transaction(function () use ($request, $pembeli) {

            // AMBIL ITEM DARI KERANJANG (SETELAH CHECKOUT)
            $items = Keranjang::with('produk')
                ->where('id_user', Auth::id())
                ->get();

            if ($items->isEmpty()) {
                throw new \Exception('Keranjang kosong.');
            }

            $subtotal = 0;

            foreach ($items as $k) {
                if ($k->produk->stok < $k->jumlah) {
                    throw new \Exception('Stok tidak mencukupi.');
                }
                $subtotal += $k->produk->harga * $k->jumlah;
            }

            $penjual = $items->first()->produk->penjual;

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
                OrderItem::create([
                    'order_id'     => $order->id,
                    'produk_id'    => $k->produk->id,
                    'nama_barang'  => $k->produk->nama_barang,
                    'harga_satuan' => $k->produk->harga,
                    'jumlah'       => $k->jumlah,
                    'subtotal_item'=> $k->produk->harga * $k->jumlah,
                ]);

                $k->produk->decrement('stok', $k->jumlah);
            }

            Keranjang::where('id_user', Auth::id())->delete();

            return $order;
        });

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
