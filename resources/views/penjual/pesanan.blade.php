@extends('layouts.penjual')

@section('content')

<div class="py-6 px-2 md:px-4 overflow-x-hidden">

  <h1 class="text-2xl font-bold mb-6 text-green-800">Pesanan Masuk</h1>
  <p class="text-sm text-gray-600 mb-4">
    Halaman ini menampilkan daftar pesanan yang masuk dari pembeli dan digunakan untuk
    memproses pesanan, memeriksa detail transaksi, serta memperbarui status pengiriman.
  </p>

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
      <div class="relative overflow-x-auto">
        <table class="min-w-max w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-slate-800">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Kode Order</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Pembeli</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Produk</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Jumlah</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Total Produk</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Total Order</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Status</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
          @foreach($orders as $order)

            @php
              // ===== LOGIKA ASLI KAMU (TIDAK DIUBAH) =====
              $sellerId = auth()->id();

              $sellerItems = $order->items->filter(fn($it) =>
                optional($it->produk)->user_id == $sellerId
              );

              $qtyTotal = (int) $sellerItems->sum(fn($it) =>
                (int)($it->jumlah ?? $it->qty ?? $it->quantity ?? 0)
              );

              $totalProdukSeller = (int) $sellerItems->sum(function($it){
                $produk = $it->produk;
                $harga  = (int) ($it->harga ?? $produk?->harga ?? 0);
                $qty    = (int) ($it->jumlah ?? $it->qty ?? $it->quantity ?? 0);
                return (int)($it->subtotal ?? ($harga * $qty));
              });

              $kode   = $order->kode_order ?? ('ORD-'.$order->id);
              $status = $order->status_pesanan ?? 'menunggu';

              // badge style (UI only)
              $badgeClass = match($status) {
                'menunggu' => 'bg-gray-200 text-gray-800 border border-gray-300',
                'dikemas'  => 'bg-yellow-200 text-yellow-900 border border-yellow-300',
                'dikirim'  => 'bg-blue-200 text-blue-900 border border-blue-300',
                'selesai'  => 'bg-green-200 text-green-900 border border-green-300',
                'ditolak'  => 'bg-red-200 text-red-900 border border-red-300',
                default    => 'bg-gray-100 text-gray-700 border border-gray-200',
              };

              $statusLabel = match($status) {
                'menunggu' => 'Menunggu',
                'dikemas'  => 'Dikemas',
                'dikirim'  => 'Dikirim',
                'selesai'  => 'Selesai',
                'ditolak'  => 'Ditolak',
                default    => ucfirst($status),
              };
            @endphp

            <tr>
              {{-- KODE ORDER --}}
              <td class="px-4 py-3 font-medium text-gray-800">
                {{ $kode }}
                <div class="text-xs text-gray-500">
                  {{ optional($order->created_at)->format('d M Y H:i') }}
                </div>
              </td>

              {{-- PEMBELI --}}
              <td class="px-4 py-3">
                <div class="font-medium">{{ $order->user->name ?? 'Pembeli' }}</div>
                <div class="text-xs text-gray-500">{{ $order->user->email ?? '' }}</div>
              </td>

              {{-- PRODUK (BISA BANYAK) --}}
              <td class="px-4 py-3">
                <div class="space-y-2">
                  @foreach($sellerItems as $it)
                    @php $p = $it->produk; @endphp
                    <div class="flex items-center gap-2">
                      <div class="w-8 h-8 rounded overflow-hidden bg-gray-100 flex-shrink-0">
                        @if($p && $p->gambar)
                          <img src="{{ asset('storage/'.$p->gambar) }}"
                               class="w-full h-full object-cover">
                        @else
                          <div class="w-full h-full flex items-center justify-center text-[10px] text-gray-400">
                            No Img
                          </div>
                        @endif
                      </div>
                      <div class="text-sm text-gray-800 truncate">
                        {{ $p->nama_barang ?? '-' }}
                      </div>
                    </div>
                  @endforeach
                </div>
              </td>

              {{-- JUMLAH --}}
              <td class="px-4 py-3 whitespace-nowrap">
                {{ $qtyTotal }}
              </td>

              {{-- TOTAL PRODUK --}}
              <td class="px-4 py-3 whitespace-nowrap font-semibold">
                Rp {{ number_format($totalProdukSeller, 0, ',', '.') }}
              </td>

              {{-- TOTAL ORDER --}}
              <td class="px-4 py-3 whitespace-nowrap">
                Rp {{ number_format((int)($order->total_bayar ?? 0), 0, ',', '.') }}
              </td>

              {{-- STATUS (DIUBAH JADI BADGE SAJA) --}}
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                  <span class="w-2 h-2 rounded-full
                    @if($status=='menunggu') bg-gray-500
                    @elseif($status=='dikemas') bg-yellow-600
                    @elseif($status=='dikirim') bg-blue-600
                    @elseif($status=='selesai') bg-green-600
                    @elseif($status=='ditolak') bg-red-600
                    @else bg-gray-400
                    @endif
                  "></span>
                  {{ $statusLabel }}
                </span>
              </td>

              {{-- DETAIL --}}
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
