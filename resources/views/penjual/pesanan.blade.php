@extends('layouts.penjual')
@section('title', 'Pesanan Masuk')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">
  <h1 class="text-2xl font-bold mb-6 text-gray-800">Pesanan Masuk</h1>

  @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-100 text-green-800 rounded-lg p-3 text-sm">
      {{ session('success') }}
    </div>
  @endif

  @if($orders->count() === 0)
    <div class="bg-white rounded-lg shadow p-6 text-center text-gray-600">
      Belum ada pesanan masuk.
    </div>
  @else
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kode Order</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pembeli</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produk</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total Produk</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total Order</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            @foreach($orders as $order)
              @php
                $sellerId = auth()->id();

                // item yg milik penjual ini saja
                $sellerItems = $order->items->filter(fn($it) => optional($it->produk)->user_id == $sellerId);
                $first = $sellerItems->first();

                $qtyTotal = (int) $sellerItems->sum(fn($it) => (int)($it->jumlah ?? $it->qty ?? $it->quantity ?? 0));

                $totalProdukSeller = (int) $sellerItems->sum(function($it){
                  $produk = $it->produk;
                  $harga  = (int) ($it->harga ?? $produk?->harga ?? 0);
                  $qty    = (int) ($it->jumlah ?? $it->qty ?? $it->quantity ?? 0);
                  return (int)($it->subtotal ?? ($harga * $qty));
                });

                $kode = $order->kode_order ?? ('ORD-'.$order->id);

                // pembayaran (sesuaikan kalau kolom beda)
                $metode = $order->metode_pembayaran ?? $order->payment_method ?? null;
                $bukti  = $order->bukti_pembayaran ?? $order->payment_proof ?? null;

                $isTransfer = in_array($metode, ['transfer_bank','bank_transfer']);
                $hasBukti   = $isTransfer && !empty($bukti);

                $status = $order->status_pesanan ?? 'menunggu';

                // badge status (kamu bisa edit sesuai istilahmu)
                $badge = match($status) {
                  'selesai' => 'bg-green-100 text-green-800',
                  'menunggu_pembayaran', 'menunggu' => 'bg-yellow-100 text-yellow-800',
                  'dibayar' => 'bg-indigo-100 text-indigo-800',
                  'dikemas' => 'bg-blue-100 text-blue-800',
                  'dikirim' => 'bg-purple-100 text-purple-800',
                  'ditolak', 'dibatalkan' => 'bg-red-100 text-red-800',
                  default => 'bg-gray-100 text-gray-800',
                };

                $firstProduk = $first?->produk;
                $firstHarga  = (int) ($first?->harga ?? $firstProduk?->harga ?? 0);
              @endphp

              <tr>
                {{-- KODE ORDER --}}
                <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-800">
                  {{ $kode }}
                  <div class="text-xs text-gray-500">
                    {{ optional($order->created_at)->format('d M Y H:i') }}
                  </div>

                  {{-- bukti transfer kecil di bawah kode order --}}
                  @if($isTransfer)
                    <div class="text-xs mt-1">
                      @if($hasBukti)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($bukti) }}"
                           target="_blank"
                           class="font-semibold text-emerald-600 hover:text-emerald-800">
                          Lihat bukti transfer
                        </a>
                      @else
                        <span class="text-gray-400">Bukti belum ada</span>
                      @endif
                    </div>
                  @endif
                </td>

                {{-- PEMBELI --}}
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="font-medium text-gray-800">
                    {{ $order->user->name ?? 'Pembeli' }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ $order->user->email ?? '' }}
                  </div>
                </td>

                {{-- PRODUK (gambar + nama) --}}
                <td class="px-4 py-3">
                  @if($firstProduk)
                    <div class="flex items-center gap-3">
                      <div class="w-12 h-12 rounded overflow-hidden bg-gray-100 flex-shrink-0">
                        @if($firstProduk->gambar)
                          <img src="{{ asset('storage/'.$firstProduk->gambar) }}" class="w-full h-full object-cover" alt="">
                        @else
                          <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Img</div>
                        @endif
                      </div>

                      <div class="min-w-0">
                        <div class="font-medium text-gray-800 truncate">
                          {{ $firstProduk->nama_barang ?? $firstProduk->name ?? '-' }}
                          @if($sellerItems->count() > 1)
                            <span class="text-xs text-gray-500">(+{{ $sellerItems->count() - 1 }} lainnya)</span>
                          @endif
                        </div>
                        <div class="text-xs text-gray-500">
                          Rp {{ number_format($firstHarga, 0, ',', '.') }}
                        </div>
                      </div>
                    </div>
                  @else
                    <span class="text-gray-400">-</span>
                  @endif
                </td>

                {{-- JUMLAH (TOTAL QTY MILIK PENJUAL) --}}
                <td class="px-4 py-3 whitespace-nowrap">
                  {{ $qtyTotal }}
                </td>

                {{-- TOTAL PRODUK (punya penjual ini) --}}
                <td class="px-4 py-3 whitespace-nowrap font-semibold">
                  Rp {{ number_format($totalProdukSeller, 0, ',', '.') }}
                </td>

                {{-- TOTAL ORDER --}}
                <td class="px-4 py-3 whitespace-nowrap">
                  Rp {{ number_format((int)($order->total_bayar ?? 0), 0, ',', '.') }}
                </td>

                {{-- STATUS + dropdown (dikemas/dikirim) --}}
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="flex items-center gap-2">

                    <form method="POST" action="{{ route('penjual.orders.masuk.status', $order->id) }}">
                      @csrf
                      @method('PATCH')
                      <select name="status"
                              class="text-xs border-gray-300 rounded-md focus:ring-0 focus:border-gray-400"
                              onchange="this.form.submit()">
                        <option value="dikemas" @selected($status=='dikemas')>Dikemas</option>
                        <option value="dikirim" @selected($status=='dikirim')>Dikirim</option>
                        <option value="selesai" @selected($status=='selesai')>Selesai</option>
                        <option value="ditolak" @selected($status=='ditolak')>Ditolak</option>
                      </select>
                    </form>
                  </div>
                </td>

                {{-- AKSI --}}
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <a href="{{ route('penjual.orders.masuk.show', $order->id) }}"
                     class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    Lihat Detail
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4">
      {{ $orders->links() }}
    </div>
  @endif
</div>
@endsection
