@extends('layouts.pembeli')

@section('title','Dashboard — SecondLife')

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-b from-green-700 to-green-600 text-white rounded-xl shadow-md">
    <div class="px-6 py-10 text-center">
        <h1 class="text-3xl sm:text-4xl font-bold">Give Items a Second Life</h1>
        <p class="mt-2 text-white/90">Buy & sell pre-loved items sustainably.</p>
        <a href="#featured"
           class="mt-5 inline-block rounded-md bg-white text-green-800 px-4 py-2 font-medium hover:bg-gray-100">
            Start Shopping
        </a>
    </div>
</section>

{{-- PRODUK --}}
<section id="featured" class="py-5">
    <div class="flex items-center justify-between">
        <h2 class="text-base font-semibold">Featured Items</h2>
    </div>

    <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">

        @forelse($produk as $item)
            <article class="rounded-md overflow-hidden border bg-white shadow-sm hover:shadow transition">
                {{-- Gambar --}}
                <a href="{{ route('pembeli.produk.detail', $item->id) }}" class="block">
                    <div class="h-28 sm:h-32 lg:h-28 bg-gray-100">
                        @if ($item->gambar)
                            <img
                                src="{{ asset('storage/'.$item->gambar) }}"
                                class="w-full h-full object-cover"
                                alt="{{ $item->nama_barang }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-[10px] text-gray-400">
                                No Image
                            </div>
                        @endif
                    </div>
                </a>

                {{-- Detail --}}
                <div class="p-2">
                    <h3 class="text-xs font-medium leading-snug line-clamp-1">
                        {{ $item->nama_barang }}
                    </h3>

                    <p class="mt-0.5 text-[11px] text-gray-600 leading-snug line-clamp-2">
                        {{ $item->deskripsi }}
                    </p>

                    @if ($item->penjual)
                        <p class="mt-1 text-[10px] text-gray-500 line-clamp-1">
                            📍 {{ $item->penjual->alamat_toko }}
                        </p>
                    @endif

                    <div class="mt-1.5 flex items-center justify-between gap-2">
                        <span class="text-xs text-green-700 font-semibold whitespace-nowrap">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-gray-500 whitespace-nowrap">
                            Stok {{ $item->stok }}
                        </span>
                    </div>
                </div>
            </article>
        @empty
            <p class="text-sm text-gray-500 col-span-full">Belum ada produk tersedia.</p>
        @endforelse

    </div>
</section>

@endsection
