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

  {{-- HEADER --}}
  <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Riwayat Pesanan Saya</h1>
      <p class="text-sm text-gray-600 mt-1">
        Daftar pesanan yang pernah kamu buat, lengkap dengan status dan aksi.
      </p>
    </div>

    <a href="{{ route('pembeli.profile') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800">
      <span class="text-lg leading-none">←</span>
      Kembali ke Profil
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-800">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="mb-4 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800">
      {{ session('error') }}
    </div>
  @endif

  @if($orders->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
      <div class="text-gray-800 font-semibold mb-1">Belum ada pesanan</div>
      <div class="text-sm text-gray-600">Kamu belum memiliki pesanan.</div>
    </div>
  @else

    {{-- CARD TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-900 sticky top-0 z-10">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Kode Order</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Penjual</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Produk</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">Total Produk</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Subtotal</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Total Order</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100">
          @foreach($orders as $order)
            @php
              $items   = $order->items;
              $penjual = optional(optional($items->first())->produk)->penjual;

              $totalProduk = $items->sum('jumlah');

              $status = $order->status_pesanan;

              $badge = match($status) {
                'menunggu','dikemas' => 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-200',
                'dikirim' => 'bg-blue-100 text-blue-800 ring-1 ring-blue-200',
                'selesai' => 'bg-green-100 text-green-800 ring-1 ring-green-200',
                'ditolak' => 'bg-red-100 text-red-800 ring-1 ring-red-200',
                default => 'bg-gray-100 text-gray-800 ring-1 ring-gray-200',
              };

              // ringkas tampilan produk: tampilkan max 2 item, sisanya jadi "+N lainnya"
              $itemsPreview = $items->take(2);
              $sisaItems = max(0, $items->count() - $itemsPreview->count());
            @endphp

            <tr class="hover:bg-gray-50/70">
              {{-- KODE ORDER --}}
              <td class="px-4 py-4 align-top">
                <div class="font-semibold text-gray-900">ORD-{{ $order->id }}</div>
                <div class="text-xs text-gray-500 mt-0.5">
                  {{ optional($order->created_at)->format('d M Y H:i') }}
                </div>
              </td>

              {{-- PENJUAL --}}
              <td class="px-4 py-4 align-top">
                <div class="font-medium text-gray-900">
                  {{ $penjual->nama_toko ?? $penjual->nama_penjual ?? '-' }}
                </div>
                @if(!empty($penjual?->no_telp))
                  <div class="text-xs text-gray-500 mt-0.5">
                    {{ $penjual->no_telp }}
                  </div>
                @endif
              </td>

              {{-- PRODUK --}}
              <td class="px-4 py-4 align-top">
                <div class="space-y-2">
                  @foreach($itemsPreview as $item)
                    @php $produk = $item->produk; @endphp

                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 ring-1 ring-gray-200">
                        @if($produk && $produk->gambar)
                          <img src="{{ asset('storage/'.$produk->gambar) }}"
                               class="w-full h-full object-cover"
                               alt="">
                        @else
                          <div class="w-full h-full flex items-center justify-center text-[10px] text-gray-400">
                            No Img
                          </div>
                        @endif
                      </div>

                      <div class="min-w-0">
                        <div class="font-medium text-gray-900 truncate">
                          {{ $produk->nama_barang ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500">
                          {{ $item->jumlah }} × Rp {{ number_format($item->harga_satuan ?? $produk->harga ?? 0, 0, ',', '.') }}
                        </div>
                      </div>
                    </div>
                  @endforeach

                  @if($sisaItems > 0)
                    <div class="text-xs text-gray-500 pl-[52px]">
                      +{{ $sisaItems }} produk lainnya
                    </div>
                  @endif
                </div>
              </td>

              {{-- TOTAL PRODUK --}}
              <td class="px-4 py-4 text-center align-top">
                <span class="inline-flex items-center justify-center min-w-[44px] px-2 py-1 rounded-lg bg-gray-100 text-gray-800 font-semibold">
                  {{ $totalProduk }}
                </span>
              </td>

              {{-- SUBTOTAL --}}
              <td class="px-4 py-4 font-semibold text-gray-900 align-top whitespace-nowrap">
                Rp {{ number_format((int)($order->subtotal ?? 0), 0, ',', '.') }}
              </td>

              {{-- TOTAL ORDER --}}
              <td class="px-4 py-4 font-bold text-gray-900 align-top whitespace-nowrap">
                Rp {{ number_format((int)($order->total_bayar ?? 0), 0, ',', '.') }}
              </td>

              {{-- STATUS --}}
              <td class="px-4 py-4 align-top">
                <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                  <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                  {{ ucwords(str_replace('_',' ', $status)) }}
                </span>
              </td>

              {{-- AKSI --}}
              <td class="px-4 py-4 align-top whitespace-nowrap">
                <div class="flex flex-col sm:flex-row gap-2">

                  {{-- Rincian/Detail Pesanan (dinamis) --}}
                  @if($status === 'selesai')
                    {{-- halaman khusus selesai (rating dll) --}}
                    <a href="{{ route('pembeli.orders.show', $order->id) }}"
                      class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                      Detail Pesanan
                    </a>
                  @else
                    {{-- halaman detail sesuai keadaan (menunggu/dikemas/dikirim) --}}
                    <a href="{{ route('pembeli.orders.show', $order->id) }}"
                      class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700">
                      Rincian Pesanan
                    </a>
                  @endif

                  {{-- Tombol Terima Barang / Status --}}
                  @if($status === 'dikirim')
                    <form action="{{ route('pembeli.orders.selesai', $order->id) }}"
                          method="POST"
                          onsubmit="return confirm('Yakin sudah menerima barang?')">
                      @csrf
                      @method('PATCH')
                      <button class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-semibold hover:bg-green-700">
                        Terima Barang
                      </button>
                    </form>

                  @elseif($status === 'selesai')
                    <button type="button"
                            disabled
                            class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-gray-100 text-gray-500 text-xs font-semibold border border-gray-200 cursor-not-allowed">
                      Pesanan Selesai
                    </button>

                  @else
                    <button type="button"
                            disabled
                            class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-gray-100 text-gray-400 text-xs font-semibold border border-gray-200 cursor-not-allowed">
                      Menunggu dikirim
                    </button>
                  @endif

                </div>
              </td>
            </tr>

          @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">
      {{ $orders->links() }}
    </div>
  @endif

</div>

</body>
</html>
