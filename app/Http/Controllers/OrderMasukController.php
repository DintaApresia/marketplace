<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderStatusLog;

class OrderMasukController extends Controller
{
    public function index(Request $request)
    {
        $penjualId = auth()->user()->penjual->id;

        $q = Order::with(['user', 'items.produk'])
            ->whereHas('items.produk', function ($p) use ($penjualId) {
                $p->where('penjual_id', $penjualId);
            })
            ->latest();

        if ($request->filled('status')) {
            $q->where('status_pesanan', $request->status);
        }

        $orders = $q->paginate(10)->withQueryString();

        return view('penjual.pesanan', compact('orders'));
    }
    public function show(Order $order)
    {
        $penjualId = auth()->user()->penjual->id;

        abort_unless((int)$order->penjual_id === (int)$penjualId, 403);

        $order->load([
            'user',
            'items.produk',
            'items.produk.ratings',
            'statusLogs',
        ]);

        $itemsSeller = $order->items
            ->filter(fn ($it) => optional($it->produk)->penjual_id === $penjualId);

        // ✅ AMBIL ADUAN UNTUK ORDER INI
        $aduan = Aduan::where('order_id', $order->id)
            ->where('penjual_id', $penjualId)   // biar aman kalau ada edge-case
            ->latest('id')
            ->first();

        return view('penjual.order_show', compact('order', 'itemsSeller', 'aduan'));
    }
    public function balasAduan(Request $request, $orderId)
    {
        $request->validate([
            'catatan_penjual' => 'required|string|max:2000',
        ]);

        $penjualId = auth()->user()->penjual->id;

        // samakan dengan show(): ambil aduan terbaru untuk order ini + penjual ini
        $aduan = Aduan::where('order_id', $orderId)
            ->where('penjual_id', $penjualId)
            ->latest('id')
            ->firstOrFail();

        $aduan->catatan_penjual = $request->catatan_penjual;
        $aduan->tgl_catatan_penjual = now();
        $aduan->save();

        return back()->with('success', 'Balasan aduan berhasil dikirim.');
    }


    // OPSIONAL: ubah status
    public function updateStatus(Request $request, Order $order)
    {
        $penjualId = auth()->user()->penjual->id;

        // ✅ fallback: kalau penjual_id null / belum keisi, cek dari items.produk
        $ownsOrder = ((int)$order->penjual_id === (int)$penjualId)
            || $order->items()->whereHas('produk', function ($q) use ($penjualId) {
                $q->where('penjual_id', $penjualId);
            })->exists();

        abort_unless($ownsOrder, 403);

        $data = $request->validate([
            // penjual JANGAN bisa set selesai
            'status'  => 'required|in:menunggu,dikemas,dikirim,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $order->update([
            'status_pesanan' => $data['status'],
        ]);

        OrderStatusLog::create([
            'order_id'   => $order->id,
            'status'     => $data['status'],
            'actor_role' => 'penjual',
            'actor_id'   => auth()->id(),
            'catatan'    => $data['catatan'] ?? null,
        ]);

        return back()->with('success', 'Status pesanan diperbarui.');
    }


}
