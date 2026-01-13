@extends('layouts.pembeli')
@section('title', 'Pencarian')

@section('content')

@php
  $qs = request()->getQueryString();
  $qs = $qs ? ('?' . $qs) : '';
@endphp

<div class="space-y-4">

  {{-- Notice LBS --}}
  @if(isset($lbs_enabled) && $lbs_enabled === false)
    <div class="rounded-lg border bg-yellow-50 p-3 text-sm text-yellow-800">
      Lokasi kamu belum diisi, jadi hasil belum diurutkan berdasarkan jarak.
      Isi lokasi di menu Profile supaya fitur “terdekat” aktif.
    </div>
  @endif

  {{-- Header --}}
  <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
    <div>
      <h1 class="text-xl font-semibold">Hasil Pencarian</h1>
      <p class="text-sm text-gray-600">
        @if($q) Kata kunci: <span class="font-semibold">"{{ $q }}"</span> · @endif
        Radius: <span class="font-semibold">{{ $maxKm }} km</span> ·
        {{ $products->total() }} produk
      </p>
    </div>

    <!-- {{-- Filter Radius --}}
    <form method="GET" action="{{ route('pembeli.search') }}" class="flex items-center gap-2">
      <input type="hidden" name="q" value="{{ $q }}">
      <label class="text-sm text-gray-600">Radius</label>
      <select name="max_km" onchange="this.form.submit()" class="rounded-md border px-3 py-2 text-sm">
        @foreach([3,5,10,20,50] as $r)
          <option value="{{ $r }}" @selected((int)$maxKm === $r)>{{ $r }} km</option>
        @endforeach
      </select>
    </form> -->
  </div>

  {{-- TIDAK ADA DATA SAMA SEKALI --}}
  @if($products->count() === 0)
    <div class="rounded-xl border bg-white p-8 text-center">
      <div class="text-3xl mb-2">🛒</div>
      <h3 class="font-semibold text-lg">Produk tidak ditemukan</h3>
      <p class="text-sm text-gray-600 mt-1">
        Coba kata kunci lain atau perbesar radius pencarian.
      </p>
    </div>

  @else
    @php $adaProduk = false; @endphp

    {{-- GRID PRODUK --}}
    <div class="grid gap-3 [grid-template-columns:repeat(auto-fill,minmax(200px,1fr))]">
      @foreach($products as $p)
        @if($p->stok > 0)
          @php $adaProduk = true; @endphp

          <article class="group rounded-xl overflow-hidden border border-gray-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

            {{-- Gambar --}}
            <a href="{{ route('pembeli.produk.detail', $p->id . $qs) }}">
              <div class="relative aspect-[4/3] sm:aspect-[16/11] bg-gray-100 overflow-hidden">
                @if($p->gambar)
                  <img
                    src="{{ asset('storage/'.$p->gambar) }}"
                    alt="{{ $p->nama_barang }}"
                    class="absolute inset-0 h-full w-full object-cover transition group-hover:scale-[1.03]"
                    loading="lazy"
                  >
                @else
                  <div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400">
                    No Image
                  </div>
                @endif

                {{-- Badge jarak --}}
                @if(isset($lbs_enabled) && $lbs_enabled === true)
                  <span class="absolute left-2 top-2 rounded-full bg-white/95 px-2 py-0.5 text-[11px] font-semibold shadow">
                    {{ number_format($p->distance, 2) }} km
                  </span>
                @endif
              </div>
            </a>

            {{-- Konten --}}
            <div class="p-3">
              <a
                href="{{ route('pembeli.produk.detail', $p->id . $qs) }}"
                class="block text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-green-700"
              >
                {{ $p->nama_barang }}
              </a>

              <p class="mt-1 text-xs text-gray-600 line-clamp-2">
                {{ $p->deskripsi }}
              </p>

              <div class="mt-2 flex items-center justify-between">
                <div class="text-sm font-bold text-green-700">
                  Rp {{ number_format($p->harga, 0, ',', '.') }}
                </div>
                <div
                  class="text-[11px] text-gray-500 whitespace-nowrap">
                  Stok {{ $p->stok }}
                </div>
              </div>
            </div>

          </article>
        @endif
      @endforeach
    </div>

    {{-- SEMUA PRODUK HABIS --}}
    @if(!$adaProduk)
      <div class="rounded-xl border bg-white p-8 text-center mt-4">
        <div class="text-3xl mb-2">📦</div>
        <h3 class="font-semibold text-lg">Tidak ada produk</h3>
        <p class="text-sm text-gray-600 mt-1">
          Semua produk pada pencarian ini sedang habis stok.
        </p>
      </div>
    @endif

    {{-- PAGINATION --}}
    <div class="mt-6">
      {{ $products->links() }}
    </div>
  @endif

</div>
@endsection
