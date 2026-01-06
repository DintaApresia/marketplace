@extends('layouts.pembeli')
@section('title', 'Pencarian')

@section('content')

@php
  $qs = request()->getQueryString(); // contoh: q=kotak+makan&max_km=10&page=2
  $qs = $qs ? ('?' . $qs) : '';
@endphp

<div class="space-y-4">

  {{-- Notice kalau LBS belum aktif --}}
  @if(isset($lbs_enabled) && $lbs_enabled === false)
    <div class="rounded-lg border bg-yellow-50 p-3 text-sm text-yellow-800">
      Lokasi kamu belum diisi, jadi hasil belum diurutkan berdasarkan jarak.
      Isi lokasi di menu Profile supaya fitur “terdekat” aktif.
    </div>
  @endif

  <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
    <div>
      <h1 class="text-xl font-semibold">Hasil Pencarian</h1>
      <p class="text-sm text-gray-600">
        @if($q) Kata kunci: <span class="font-semibold">"{{ $q }}"</span> · @endif
        Radius: <span class="font-semibold">{{ $maxKm }} km</span> ·
        {{ $products->total() }} produk
      </p>
    </div>

    {{-- Dropdown radius --}}
    <form method="GET" action="{{ route('pembeli.search') }}" class="flex items-center gap-2">
      <input type="hidden" name="q" value="{{ $q }}">
      <label class="text-sm text-gray-600">Radius</label>
      <select name="max_km" onchange="this.form.submit()" class="rounded-md border px-3 py-2 text-sm">
        @foreach([3,5,10,20,50] as $r)
          <option value="{{ $r }}" @selected((int)$maxKm === $r)>{{ $r }} km</option>
        @endforeach
      </select>
    </form>
  </div>

  @if($products->count() === 0)
    <div class="rounded-xl border bg-white p-8 text-center">
      <div class="text-3xl mb-2">🛒</div>
      <h3 class="font-semibold text-lg">Produk tidak ditemukan</h3>
      <p class="text-sm text-gray-600 mt-1">Coba kata kunci lain atau radius lebih besar.</p>
    </div>
  @else
    <div class="grid gap-3 [grid-template-columns:repeat(auto-fill,minmax(200px,1fr))]">
      @foreach($products as $p)
        @if($p->stok > 0)
          <article class="group rounded-xl overflow-hidden border border-gray-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            
            {{-- Gambar --}}
            <a href="{{ route('pembeli.produk.detail', $p->id . $qs) }}" class="block">
              <div class="relative aspect-[4/3] sm:aspect-[16/11] bg-gray-100 overflow-hidden">
                @if($p->gambar)
                  <img
                    src="{{ asset('storage/'.$p->gambar) }}"
                    alt="{{ $p->nama_barang }}"
                    class="absolute inset-0 h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                    loading="lazy"
                  >
                @else
                  <div class="absolute inset-0 flex items-center justify-center text-gray-400 text-xs">
                    No Image
                  </div>
                @endif
              </div>
            </a>

            {{-- Konten --}}
            <div class="p-3">
              <a
                href="{{ route('pembeli.produk.detail', $p->id . $qs) }}"
                class="block text-sm font-semibold text-gray-900 leading-snug line-clamp-2 transition group-hover:text-green-700"
              >
                {{ $p->nama_barang }}
              </a>

              <p class="mt-1 text-xs text-gray-600 line-clamp-2">
                {{ $p->deskripsi }}
              </p>

              <div class="mt-2 flex items-end justify-between gap-2">
                <div class="text-sm font-bold text-green-700">
                  Rp {{ number_format($p->harga, 0, ',', '.') }}
                </div>

                <a
                  href="{{ route('pembeli.keranjang') }}"
                  class="inline-flex items-center justify-center rounded-lg bg-green-700 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-green-800"
                >
                  <i class="bi bi-cart-plus"></i>
                </a>
              </div>
            </div>

          </article>
        @endif
      @endforeach


        </div>

<div class="mt-6">
  {{ $products->links() }}
</div>
  @endif

</div>
@endsection
