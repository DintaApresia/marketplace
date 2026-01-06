@extends('layouts.pembeli')
@section('title', 'Pesanan Selesai #' . $order->id)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6 space-y-4">

  {{-- Header --}}
  <div class="bg-white border rounded-2xl p-4">
    <div class="flex items-start justify-between gap-3">
      <div>
        <h1 class="text-xl font-semibold">Pesanan #{{ $order->id }}</h1>
        <p class="text-sm text-gray-600">
          Status:
          <span class="font-medium">
            {{ ucwords(str_replace('_',' ', $order->status_pesanan ?? $order->status ?? '')) }}
          </span>
        </p>
      </div>

      <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
        Selesai
      </span>
    </div>

    {{-- Info ringkas --}}
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div class="border rounded-xl p-3">
        <p class="text-xs text-gray-500">Total Bayar</p>
        <p class="font-semibold">
          Rp {{ number_format($order->total_bayar ?? $order->total ?? 0, 0, ',', '.') }}
        </p>
      </div>

      <div class="border rounded-xl p-3">
        <p class="text-xs text-gray-500">Tanggal</p>
        <p class="font-semibold">
          {{ optional($order->created_at)->format('d M Y, H:i') }}
        </p>
      </div>

      <div class="border rounded-xl p-3">
        <p class="text-xs text-gray-500">Metode Bayar</p>
        <p class="font-semibold">
          {{ $order->metode_pembayaran ?? '-' }}
        </p>
      </div>
    </div>

    <div class="mt-4 text-sm text-gray-600">
      Silakan beri rating untuk setiap produk di pesanan ini. Kamu bisa upload beberapa foto review (opsional).
    </div>
  </div>

  {{-- Flash --}}
  @if(session('success'))
    <div class="p-3 rounded-xl bg-green-50 text-green-800 text-sm border border-green-100">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="p-3 rounded-xl bg-red-50 text-red-800 text-sm border border-red-100">
      {{ session('error') }}
    </div>
  @endif

  {{-- List items --}}
  <div class="bg-white border rounded-2xl overflow-hidden">
    <div class="p-4 border-b">
      <h2 class="font-semibold">Produk di Pesanan</h2>
      <p class="text-sm text-gray-600">Beri rating 1–5, ulasan, dan foto (opsional).</p>
    </div>

    <div class="divide-y">
      @foreach($order->items as $item)
        @php
          $p = $item->produk; // relasi dari OrderItem
          // rated disarankan: keyBy('produk_id') di controller
          $already = $rated[$item->produk_id] ?? null;
        @endphp

        <div class="p-4 flex gap-3">
          {{-- Gambar produk --}}
          <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
            @if($p && !empty($p->gambar))
              <img src="{{ asset('storage/'.$p->gambar) }}" class="w-full h-full object-cover" alt="{{ $p->nama_barang ?? 'Produk' }}">
            @else
              <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Image</div>
            @endif
          </div>

          <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <p class="font-semibold truncate">{{ $item->nama_barang ?? ($p->nama_barang ?? 'Produk') }}</p>
                <p class="text-sm text-gray-600">
                  Qty: {{ $item->jumlah }}
                  · Harga: Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                  · Subtotal: <span class="font-medium">Rp {{ number_format($item->subtotal_item, 0, ',', '.') }}</span>
                </p>
              </div>

              @if($already)
                <div class="text-right">
                  <div class="text-xs text-gray-500">Rating kamu</div>
                  <div class="text-yellow-600 text-sm">
                    {{ str_repeat('★', (int)$already->rating) }}{{ str_repeat('☆', 5-(int)$already->rating) }}
                  </div>
                </div>
              @endif
            </div>

            {{-- FORM RATING --}}
            <form method="POST"
                  action="{{ route('pembeli.orders.rating.store', $order->id) }}"
                  enctype="multipart/form-data"
                  class="mt-3 space-y-2">
              @csrf

              <input type="hidden" name="produk_id" value="{{ $item->produk_id }}">

              <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <label class="text-sm text-gray-700 w-20">Rating</label>
                <select name="rating" class="border rounded-xl px-3 py-2 text-sm w-full sm:w-56">
                  @for($i=5; $i>=1; $i--)
                    <option value="{{ $i }}" @selected(old('rating', $already->rating ?? 5) == $i)>
                      {{ $i }} - {{ $i==5?'Sangat puas':($i==4?'Puas':($i==3?'Cukup':($i==2?'Kurang':'Buruk'))) }}
                    </option>
                  @endfor
                </select>
              </div>

              <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                <label class="text-sm text-gray-700 w-20 pt-2">Ulasan</label>
                <textarea name="review" rows="2"
                          class="border rounded-xl px-3 py-2 text-sm w-full"
                          placeholder="Tulis ulasan (opsional)">{{ old('review', $already->review ?? '') }}</textarea>
              </div>

              {{-- Upload gambar review (multiple) --}}
              <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                <label class="text-sm text-gray-700 w-20 pt-2">Foto</label>
                <div class="w-full space-y-2">
                  <input type="file"
                         name="review_images[]"
                         multiple
                         accept="image/*"
                         class="block w-full text-sm file:mr-3 file:px-4 file:py-2 file:rounded-xl file:border-0 file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">

                  <p class="text-xs text-gray-500">Bisa pilih beberapa foto. (jpg/png/webp)</p>

                  {{-- Tampilkan foto yang sudah pernah diupload --}}
                  @if($already && !empty($already->review_images))
                    @php
                      $imgs = is_array($already->review_images) ? $already->review_images : json_decode($already->review_images, true);
                    @endphp

                    @if(!empty($imgs))
                      <div class="flex flex-wrap gap-2">
                        @foreach($imgs as $img)
                          <a href="{{ asset('storage/'.$img) }}" target="_blank" class="block">
                            <img src="{{ asset('storage/'.$img) }}"
                                 class="w-16 h-16 rounded-xl object-cover border"
                                 alt="Review Image">
                          </a>
                        @endforeach
                      </div>
                    @endif
                  @endif
                </div>
              </div>

              <div class="flex justify-end">
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm hover:bg-blue-700">
                  {{ $already ? 'Update Review' : 'Kirim Review' }}
                </button>
              </div>

              {{-- Errors --}}
              @error('rating')
                <p class="text-sm text-red-600">{{ $message }}</p>
              @enderror
              @error('review')
                <p class="text-sm text-red-600">{{ $message }}</p>
              @enderror
              @error('review_images.*')
                <p class="text-sm text-red-600">{{ $message }}</p>
              @enderror
            </form>

          </div>
        </div>
      @endforeach
    </div>
  </div>

  <div class="flex justify-end gap-2">
    <a href="{{ url('/pembeli/dashboard') }}"
       class="px-4 py-2 rounded-xl border text-sm hover:bg-gray-50">
      Kembali
    </a>
  </div>

</div>
@endsection
