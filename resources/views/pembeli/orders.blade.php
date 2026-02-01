<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Pesanan</title>
  @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

<div class="max-w-6xl mx-auto py-8 px-4">
  <h1 class="text-2xl font-bold mb-6 text-gray-800">Riwayat Pesanan Saya</h1>

  <a href="{{ route('pembeli.profile') }}"
     class="inline-block mb-4 text-sm text-gray-500 hover:text-gray-700">
    ← Kembali ke Profil
  </a>

  @if($orders->isEmpty())
    <div class="bg-white rounded-lg shadow p-6 text-center text-gray-600">
      Kamu belum memiliki pesanan.
    </div>
  @else
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-slate-800">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Kode Order</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Penjual</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Produk</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase">Total Produk</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Subtotal</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Total Order</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Status</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Aksi</th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
          @foreach($orders as $order)
            @php
              $items   = $order->items;
              $penjual = optional(optional($items->first())->produk)->penjual;

              // TOTAL PRODUK (qty semua item)
              $totalProduk = $items->sum('jumlah');

              // STATUS
              $status = $order->status_pesanan;
              $badge = match($status) {
                'menunggu','dikemas' => 'bg-yellow-100 text-yellow-800',
                'dikirim' => 'bg-blue-100 text-blue-800',
                'selesai' => 'bg-green-100 text-green-800',
                'ditolak' => 'bg-red-100 text-red-800',
                default => 'bg-gray-100 text-gray-800',
              };
            @endphp

            <tr>
              {{-- KODE ORDER --}}
              <td class="px-4 py-3 font-medium text-gray-800 align-top">
                ORD-{{ $order->id }}
                <div class="text-xs text-gray-500">
                  {{ $order->created_at->format('d M Y H:i') }}
                </div>
              </td>

              {{-- PENJUAL --}}
              <td class="px-4 py-3 align-top">
                <div class="font-medium text-gray-800">
                  {{ $penjual->nama_toko ?? $penjual->nama_penjual ?? '-' }}
                </div>
                <div class="text-xs text-gray-500">
                  {{ $penjual->no_telp ?? '' }}
                </div>
              </td>

              {{-- PRODUK (GAMBAR + NAMA, TIDAK DIUBAH KONSEPNYA) --}}
              <td class="px-4 py-3">
                <div class="space-y-3">
                  @foreach($items as $item)
                    @php
                      $produk = $item->produk;
                    @endphp

                    <div class="flex items-center gap-3">
                      <div class="w-12 h-12 rounded overflow-hidden bg-gray-100 flex-shrink-0">
                        @if($produk && $produk->gambar)
                          <img src="{{ asset('storage/'.$produk->gambar) }}"
                               class="w-full h-full object-cover"
                               alt="">
                        @else
                          <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">
                            No Img
                          </div>
                        @endif
                      </div>

                      <div class="min-w-0">
                        <div class="font-medium text-gray-800 truncate">
                          {{ $produk->nama_barang ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500">
                          {{ $item->jumlah }} × Rp {{ number_format($item->harga_satuan ?? $produk->harga ?? 0, 0, ',', '.') }}
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </td>

              {{-- TOTAL PRODUK --}}
              <td class="px-4 py-3 text-center align-top">
                {{ $totalProduk }}
              </td>

              {{-- SUBTOTAL --}}
              <td class="px-4 py-3 font-semibold align-top">
                Rp {{ number_format($order->subtotal, 0, ',', '.') }}
              </td>

              {{-- TOTAL ORDER --}}
              <td class="px-4 py-3 font-semibold align-top">
                Rp {{ number_format($order->total_bayar, 0, ',', '.') }}
              </td>

              {{-- STATUS --}}
              <td class="px-4 py-3 align-top">
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                  {{ ucwords(str_replace('_',' ', $status)) }}
                </span>
              </td>

              {{-- AKSI --}}
              <td class="px-4 py-3 align-top whitespace-nowrap">
                @if($status === 'dikirim')
                  <form action="{{ route('pembeli.orders.selesai', $order->id) }}"
                        method="POST"
                        onsubmit="return confirm('Yakin sudah menerima barang?')">
                    @csrf
                    @method('PATCH')
                    <button class="text-xs font-semibold text-green-600 hover:text-green-800">
                      Terima Barang
                    </button>
                  </form>

                @elseif($status === 'selesai')
                  <a href="{{ route('pembeli.orders.show', $order->id) }}"
                     class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    Lihat Detail
                  </a>
                @else
                  <span class="text-xs text-gray-400">—</span>
                @endif
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

</body>
</html>
