@extends('layouts.penjual')
@section('title','Detail Pesanan')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">

  <h1 class="text-2xl font-bold mb-6 text-gray-800">
    Detail Pesanan #{{ $order->kode_order ?? ('ORD-'.$order->id) }}
  </h1>

  {{-- INFO PEMBELI + ALAMAT --}}
  <div class="bg-white rounded-lg shadow p-5 mb-3 space-y-3">
    <div>
      <div class="text-sm text-gray-600">Pembeli</div>
      <div class="font-semibold text-gray-800">
        {{ $order->user->name ?? 'Pembeli' }}
      </div>
      <div class="text-xs text-gray-500">
        {{ $order->user->email ?? '' }}
      </div>
    </div>

    <div class="pt-3 border-t">
      <div class="text-sm text-gray-600">Alamat Pengiriman</div>
      <div class="text-sm text-gray-800 leading-relaxed">
        {{ $order->alamat_pengiriman ?? '-' }}
      </div>
    </div>
  </div>

  @php
    // helper URL gambar review (support storage path / full url)
    $imgUrl = function ($path) {
      if (!$path) return null;
      return \Illuminate\Support\Str::startsWith($path, ['http://','https://'])
        ? $path
        : asset('storage/'.$path);
    };

    // Map rating per produk_id (khusus order ini + pembeli ini)
    $ratingsMap = [];
    foreach ($itemsSeller as $it) {
      if (!$it->produk) continue;

      $row = \Illuminate\Support\Facades\DB::table('produk_rating')
        ->where('order_id', $order->id)
        ->where('user_id', $order->user_id)
        ->where('produk_id', $it->produk->id)
        ->first();

      if ($row) $ratingsMap[$it->produk->id] = $row;
    }
  @endphp

  {{-- CARD DETAIL PRODUK + RATING (JADI SATU) --}}
  <div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-5 py-4 border-b">
      <div class="font-semibold text-gray-800">Detail Produk</div>
      <div class="text-xs text-gray-500">Item yang termasuk produk kamu.</div>
    </div>

    {{-- TABEL PRODUK --}}
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produk</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Harga</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bukti Pembayaran</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Qty</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Subtotal</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Catatan</th>

          </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
          @foreach($itemsSeller as $item)
            @php
              $produk = $item->produk;
              $harga  = (int) ($item->harga ?? $produk?->harga ?? 0);
              $qty    = (int) ($item->jumlah ?? $item->qty ?? $item->quantity ?? 0);
              $sub    = (int) ($item->subtotal ?? ($harga * $qty));
            @endphp

            <tr>
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded overflow-hidden bg-gray-100 flex-shrink-0">
                    @if($produk && $produk->gambar)
                      <img src="{{ asset('storage/'.$produk->gambar) }}"
                           class="w-full h-full object-cover" alt="">
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
                    <div class="text-xs text-gray-500">ID Produk: {{ $produk->id ?? '-' }}</div>
                  </div>
                </div>
              </td>

              <td class="px-4 py-3 whitespace-nowrap">
                Rp {{ number_format($harga, 0, ',', '.') }}
              </td>

              <td class="px-4 py-3">
                @if($order->metode_pembayaran === 'transfer')
                  @if(!empty($order->bukti_pembayaran))
                    <a href="{{ asset('storage/'.$order->bukti_pembayaran) }}" target="_blank"
                      class="block w-12 h-12 rounded overflow-hidden bg-gray-100 border hover:opacity-90">
                      <img
                        src="{{ asset('storage/'.$order->bukti_pembayaran) }}"
                        class="w-full h-full object-cover"
                        alt="Bukti Pembayaran">
                    </a>
                  @else
                    <span class="text-xs text-red-500 italic">Belum diupload</span>
                  @endif
                @else
                  <span class="text-xs text-gray-400 italic">COD</span>
                @endif
              </td>


              <td class="px-4 py-3 whitespace-nowrap">
                {{ $qty }}
              </td>

              <td class="px-4 py-3 whitespace-nowrap font-semibold">
                Rp {{ number_format($sub, 0, ',', '.') }}
              </td>

              {{-- CATATAN --}}
              <td class="px-4 py-3 max-w-xs">
                @if(!empty($order->catatan))
                  <div class="text-sm text-gray-700 break-words">
                    {{ $order->catatan }}
                  </div>
                @else
                  <span class="text-xs text-gray-400 italic">
                    Tidak ada catatan
                  </span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- SECTION RATING DI BAWAH TABEL (MASIH DALAM CARD YANG SAMA) --}}
    <div class="border-t">
      <div class="px-5 py-4">
        <div class="font-semibold text-gray-800">Rating & Ulasan</div>
        <div class="text-xs text-gray-500">Penilaian pembeli untuk order ini.</div>
      </div>

      <div class="px-5 pb-5 space-y-4">
        @if(count($ratingsMap) === 0)
          <div class="text-center text-sm text-gray-500 py-6">
            Belum ada rating untuk pesanan ini.
          </div>
        @else
          @foreach($itemsSeller as $item)
            @php
              $produk = $item->produk;
              if (!$produk) continue;

              $r = $ratingsMap[$produk->id] ?? null;

              $imgs = [];
              if ($r && !empty($r->review_images)) {
                $decoded = json_decode($r->review_images, true);
                if (is_array($decoded)) $imgs = $decoded;
              }
            @endphp

            <div class="border rounded-lg p-4">
              <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded overflow-hidden bg-gray-100 flex-shrink-0">
                  @if($produk->gambar)
                    <img src="{{ asset('storage/'.$produk->gambar) }}" class="w-full h-full object-cover" alt="">
                  @else
                    <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Img</div>
                  @endif
                </div>

                <div class="min-w-0 flex-1">
                  <div class="flex items-center justify-between gap-3">
                    <div class="font-semibold text-gray-800 truncate">
                      {{ $produk->nama_barang ?? '-' }}
                    </div>

                    @if($r)
                      <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 whitespace-nowrap">
                        {{ (int)($r->rating ?? 0) }}/5
                      </span>
                    @endif
                  </div>

                  @if(!$r)
                    <div class="text-xs text-gray-400 mt-1">Produk ini belum dinilai.</div>
                  @else
                    @if(!empty($r->review))
                      <div class="text-sm text-gray-700 mt-2">
                        “{{ $r->review }}”
                      </div>
                    @else
                      <div class="text-xs text-gray-400 mt-2">Tidak ada komentar.</div>
                    @endif

                    @if(!empty($imgs))
                      <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                        @foreach($imgs as $path)
                          @php $url = $imgUrl($path); @endphp
                          @if($url)
                            <a href="{{ $url }}" target="_blank" class="block">
                              <img src="{{ $url }}" class="w-full h-16 object-cover rounded-md border" alt="">
                            </a>
                          @endif
                        @endforeach
                      </div>
                    @endif

                    <div class="text-xs text-gray-400 mt-2">
                      Diulas: {{ \Carbon\Carbon::parse($r->created_at)->format('d M Y H:i') }}
                    </div>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        @endif
      </div>
    </div>
  </div>
    {{-- KEMBALI --}}
  <div class="mb-4">
    <a href="{{ route('penjual.orders.masuk') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-800">
      ← Kembali ke Pesanan Masuk
    </a>
  </div>

</div>
@endsection
