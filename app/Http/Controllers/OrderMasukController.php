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
        $sellerId = Auth::id(); // atau Auth::user()->penjual->id

        // Query utama daftar pesanan
        $q = Order::query()
            ->with([
                'user',
                'items.produk',
            ])
            ->whereHas('items.produk', function ($p) use ($sellerId) {
                $p->where('user_id', $sellerId);
            })
            ->latest();

        // Filter status (opsional)
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        $orders = $q->paginate(10)->withQueryString();

        return view('penjual.pesanan', compact('orders'));
    }

    public function show(Order $order)
    {
        $sellerId = auth()->id();

        abort_unless(
            $order->items()->whereHas('produk', fn($p) => $p->where('user_id', $sellerId))->exists(),
            403
        );

        $order->load(['user', 'items.produk', 'items.produk.ratings']);

        $itemsSeller = $order->items->filter(fn($it) => optional($it->produk)->user_id == $sellerId);

        return view('penjual.order_show', compact('order', 'itemsSeller'));
    }


    // OPSIONAL: ubah status
    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:menunggu,dikemas,dikirim,selesai,ditolak',
        ]);

        $order->status_pesanan = $data['status'];
        $order->save();

        return back()->with('success', 'Status pesanan diperbarui.');
    }

}
