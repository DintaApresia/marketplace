<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        abort_unless(
            $order->items()
                ->whereHas('produk', fn ($p) => $p->where('penjual_id', $penjualId))
                ->exists(),
            403
        );

        $order->load(['user', 'items.produk', 'items.produk.ratings']);

        $itemsSeller = $order->items
            ->filter(fn ($it) => optional($it->produk)->penjual_id === $penjualId);

        return view('penjual.order_show', compact('order', 'itemsSeller'));
    }

    // OPSIONAL: ubah status
    public function updateStatus(Request $request, Order $order)
    {
        $penjualId = auth()->user()->penjual->id;

        abort_unless(
            $order->items()
                ->whereHas('produk', fn ($p) => $p->where('penjual_id', $penjualId))
                ->exists(),
            403
        );

        $data = $request->validate([
            'status' => 'required|in:menunggu,dikemas,dikirim,selesai,ditolak',
        ]);

        $order->status_pesanan = $data['status'];
        $order->save();

        return back()->with('success', 'Status pesanan diperbarui.');
    }


}
