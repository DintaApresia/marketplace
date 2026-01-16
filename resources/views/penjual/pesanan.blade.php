@extends('layouts.penjual')
@section('title', 'Pesanan Masuk')

@section('content')

<div class="py-6 px-2 md:px-4 overflow-x-hidden">

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

    <div class="bg-white rounded-lg shadow">

      {{-- 🔑 WRAPPER KHUSUS TABEL --}}
      <div class="relative overflow-x-auto">

        <table class="min-w-max w-full divide-y divide-gray-200 text-sm">

          <thead class="bg-gray-50 sticky top-0 z-10">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Kode Order</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Pembeli</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Produk</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Jumlah</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Total Produk</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Total Order</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Status</th>
              <th class="px-4 py-3 whitespace-nowrap"></th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            @foreach($orders as $order)

              {{-- ⬇️ BLOK LOGIKA ASLI KAMU --}}
              @php
                $sellerId = auth()->id();

                $sellerItems = $order->items->filter(fn($it) =>
                  optional($it->produk)->user_id == $sellerId
                );

                $first = $sellerItems->first();

                $qtyTotal = (int) $sellerItems->sum(fn($it) =>
                  (int)($it->jumlah ?? $it->qty ?? $it->quantity ?? 0)
                );

                $totalProdukSeller = (int) $sellerItems->sum(function($it){
                  $produk = $it->produk;
                  $harga  = (int) ($it->harga ?? $produk?->harga ?? 0);
                  $qty    = (int) ($it->jumlah ?? $it->qty ?? $it->quantity ?? 0);
                  return (int)($it->subtotal ?? ($harga * $qty));
                });

                $kode = $order->kode_order ?? ('ORD-'.$order->id);

                $status = $order->status_pesanan ?? 'menunggu';
              @endphp

              <tr>
                <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-800">
                  {{ $kode }}
                  <div class="text-xs text-gray-500">
                    {{ optional($order->created_at)->format('d M Y H:i') }}
                  </div>
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="font-medium">{{ $order->user->name ?? 'Pembeli' }}</div>
                  <div class="text-xs text-gray-500">{{ $order->user->email ?? '' }}</div>
                </td>

                <td class="px-4 py-3">
                  {{ $first?->produk?->nama_barang ?? '-' }}
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  {{ $qtyTotal }}
                </td>

                <td class="px-4 py-3 whitespace-nowrap font-semibold">
                  Rp {{ number_format($totalProdukSeller, 0, ',', '.') }}
                </td>

                <td class="px-4 py-3 whitespace-nowrap">
                  Rp {{ number_format((int)($order->total_bayar ?? 0), 0, ',', '.') }}
                </td>

                {{-- 🔒 DROPDOWN STATUS ASLI KAMU (TIDAK DIUBAH) --}}
                <td class="px-4 py-3 whitespace-nowrap">
                  <form method="POST" action="{{ route('penjual.orders.masuk.status', $order->id) }}">
                    @csrf
                    @method('PATCH')
                    <select name="status"
                            class="text-xs border-gray-300 rounded-md focus:ring-0 focus:border-gray-400"
                            onchange="this.form.submit()">
                      <option value="menunggu" @selected($status=='menunggu')>Menunggu</option>
                      <option value="dikemas" @selected($status=='dikemas')>Dikemas</option>
                      <option value="dikirim" @selected($status=='dikirim')>Dikirim</option>
                      <option value="selesai" @selected($status=='selesai')>Selesai</option>
                      <option value="ditolak" @selected($status=='ditolak')>Ditolak</option>
                    </select>
                  </form>
                </td>

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