@extends('layouts.pembeli')

@section('title', 'Hasil Pencarian')

@section('content')
  <h2 class="text-xl font-semibold mb-4">Hasil Pencarian Produk</h2>

  @if (!empty($query))
    <p class="text-sm text-gray-500 mb-3">
      Kata kunci: <span class="font-semibold">"{{ $query }}"</span>
    </p>
  @endif

  @php
      $products = $products ?? collect();
  @endphp

  @if ($products->isEmpty())
      <p class="text-gray-600">Tidak ada produk ditemukan.</p>
  @else
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach ($products as $p)
              <div class="border rounded-lg p-4 shadow-sm bg-white">

                  @if ($p->gambar)
                      <img src="{{ asset('storage/'.$p->gambar) }}"
                           class="w-full h-40 object-cover rounded-md mb-2">
                  @endif

                  <h3 class="font-semibold text-green-700 text-lg">
                      {{ $p->nama_barang }}
                  </h3>

                  <p class="text-sm text-gray-600 mb-2">
                      {{ $p->deskripsi }}
                  </p>

                  <p class="text-sm font-bold text-green-700">
                      Rp {{ number_format($p->harga, 0, ',', '.') }}
                  </p>
              </div>
          @endforeach
      </div>
  @endif
@endsection
