<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Pesanan</title>

  {{-- Tailwind --}}
  @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

<div class="max-w-6xl mx-auto py-8 px-4">
  <h1 class="text-2xl font-bold mb-6 text-gray-800">Riwayat Pesanan Saya</h1>

  <a href="{{ route('pembeli.profile') }}"
     class="inline-block mt-4 text-xs sm:text-sm text-gray-500 hover:text-gray-700">
    ← Kembali ke halaman profil
  </a>

  @if($orders->count() === 0)
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
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Jumlah</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Total Produk</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Total Order</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Status</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase">Pesanan Selesai</th>
              <!-- <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th> -->
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            @foreach($orders as $order)
              @foreach($order->items as $item)
                @php
                  $produk  = $item->produk;
                  $penjual = $produk?->penjual; // sesuaikan kalau relasi beda

                  // harga & total produk per item
                  $harga = (int) ($item->harga ?? $produk?->harga ?? 0);
                  $qty   = (int) ($item->jumlah ?? $item->qty ?? $item->quantity ?? 0);

                  $totalProduk = (int) ($item->subtotal ?? ($harga * $qty));

                  // badge status
                  $status = $order->status_pesanan ?? 'menunggu';
                  $badge = match($status) {
                    'selesai' => 'bg-green-100 text-green-800',
                    'dikemas', 'menunggu' => 'bg-yellow-100 text-yellow-800',
                    'dikirim' => 'bg-red-100 text-red-800',
                    'ditolak' => 'bg-blue-100 text-blue-800',
                    default => 'bg-gray-100 text-gray-800',
                  };
                @endphp

                <tr>
                  {{-- KODE ORDER --}}
                  <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-800">
                    {{ $order->kode_order ?? ('ORD-'.$order->id) }}
                    <div class="text-xs text-gray-500">
                      {{ optional($order->created_at)->format('d M Y H:i') }}
                    </div>
                  </td>

                  {{-- PENJUAL --}}
                  <td class="px-4 py-3 whitespace-nowrap">
                    <div class="font-medium text-gray-800">
                      {{ $penjual->name ?? $penjual->nama ?? $penjual->nama_toko ?? '-' }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ $penjual->no_telp ?? '' }}
                    </div>
                  </td>

                  {{-- PRODUK (gambar + nama) --}}
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                      <div class="w-12 h-12 rounded overflow-hidden bg-gray-100 flex-shrink-0">
                        @if($produk && $produk->gambar)
                          <img src="{{ asset('storage/'.$produk->gambar) }}" class="w-full h-full object-cover" alt="">
                        @else
                          <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Img</div>
                        @endif
                      </div>

                      <div class="min-w-0">
                        <div class="font-medium text-gray-800 truncate">
                          {{ $produk->nama_barang ?? $produk->name ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500">
                          Rp {{ number_format($harga, 0, ',', '.') }}
                        </div>
                      </div>
                    </div>
                  </td>

                  {{-- JUMLAH --}}
                  <td class="px-4 py-3 whitespace-nowrap">
                    {{ $qty }}
                  </td>

                  {{-- TOTAL PRODUK (line total) --}}
                  <td class="px-4 py-3 whitespace-nowrap font-semibold">
                    Rp {{ number_format($totalProduk, 0, ',', '.') }}
                  </td>

                  {{-- TOTAL ORDER --}}
                  <td class="px-4 py-3 whitespace-nowrap">
                    Rp {{ number_format((int)($order->total_bayar ?? 0), 0, ',', '.') }}
                  </td>

                  {{-- STATUS --}}
                  <td class="px-4 py-3 whitespace-nowrap">
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                      {{ ucwords(str_replace('_',' ', $status)) }}
                    </span>
                  </td>

                  {{-- PESANAN (TERIMA BARANG / LIHAT DETAIL) --}}
                  <td class="px-4 py-3 whitespace-nowrap">
                    @if($order->status_pesanan === 'dikirim')
                      <form action="{{ route('pembeli.orders.selesai', $order->id) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin sudah menerima barang?')">
                        @csrf
                        @method('PATCH')

                        <button
                          type="submit"
                          class="inline-flex items-center px-3 py-1.5 rounded-md
                                text-xs font-semibold bg-green-600 text-white hover:bg-green-700">
                          Terima Barang
                        </button>
                      </form>

                    @elseif($order->status_pesanan === 'selesai')
                      <a href="{{ route('pembeli.orders.show', $order->id) }}"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        Lihat Detail
                      </a>

                    @else
                      <span class="text-xs text-gray-400">—</span>
                    @endif
                  </td>

                  <!-- {{-- AKSI --}}
                  <td class="px-4 py-3 text-right whitespace-nowrap">
                    @if(($order->status_pesanan ?? $order->status) === 'selesai')
                      <a href="{{ route('pembeli.orders.show', $order->id) }}"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        Lihat Detail
                      </a>
                    @else
                      <span class="text-xs text-gray-400 cursor-not-allowed">
                        Lihat Detail
                      </span>
                    @endif
                  </td> -->

                </tr>
              @endforeach
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