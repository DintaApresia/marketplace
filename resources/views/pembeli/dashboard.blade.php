@extends('layouts.pembeli')

@section('title','Dashboard — SecondLife')

@section('content')

{{-- HERO --}}
<section class="mt-4 bg-gradient-to-r from-green-700 to-green-600 text-white rounded-xl shadow-md overflow-hidden">
    <div class="px-6 py-10 sm:px-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div>
            <h1 class="text-3xl sm:text-4xl font-bold">Give Items a Second Life</h1>
            <p class="mt-2 text-white/90 text-sm sm:text-base">
                Buy & sell pre-loved items sustainably — hemat & ramah lingkungan.
            </p>
            <a href="#featured"
               class="mt-5 inline-block rounded-lg bg-white text-green-800 px-5 py-2.5 text-sm font-semibold hover:bg-gray-100">
                Start Shopping
            </a>
        </div>

        {{-- small promo cards --}}
        <div class="grid grid-cols-2 gap-2 w-full sm:w-auto">
            <div class="bg-white/10 rounded-xl p-4">
                <p class="text-xs text-white/80">Rekomendasi</p>
                <p class="text-lg font-bold">Barang Pilihan</p>
                <p class="text-xs text-white/80 mt-1">Update tiap hari</p>
            </div>
            <div class="bg-white/10 rounded-xl p-4">
                <p class="text-xs text-white/80">Sustainable</p>
                <p class="text-lg font-bold">Eco Friendly</p>
                <p class="text-xs text-white/80 mt-1">Kurangi limbah</p>
            </div>
        </div>
    </div>
</section>

{{-- PROMO STRIP --}}
<section class="mt-5">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="rounded-xl border bg-white p-4 shadow-sm">
            <div class="text-2xl">🚚</div>
            <p class="mt-2 font-semibold text-gray-800">Gratis Ongkir*</p>
            <p class="text-xs text-gray-500">S&K berlaku untuk area tertentu.</p>
        </div>
        <div class="rounded-xl border bg-white p-4 shadow-sm">
            <div class="text-2xl">💸</div>
            <p class="mt-2 font-semibold text-gray-800">Diskon Harian</p>
            <p class="text-xs text-gray-500">Promo pilihan tiap hari.</p>
        </div>
        <div class="rounded-xl border bg-white p-4 shadow-sm">
            <div class="text-2xl">🛡️</div>
            <p class="mt-2 font-semibold text-gray-800">Belanja Aman</p>
            <p class="text-xs text-gray-500">Barang tervalidasi penjual.</p>
        </div>
    </div>
</section>

{{-- PRODUK --}}
<section id="featured" class="py-6">
    <div class="flex items-end justify-between">
        <div>
            <h2 class="text-base font-semibold text-gray-800">Featured Items</h2>
            <p class="text-xs text-gray-500">Temukan barang bekas berkualitas</p>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">
        @forelse($produk as $item)
            <article class="group rounded-xl overflow-hidden border bg-white shadow-sm hover:shadow-md transition">
                {{-- Gambar --}}
                <a href="{{ route('pembeli.produk.detail', $item->id) }}?back={{ urlencode(url()->full()) }}">
                    <div class="relative h-36 sm:h-44 lg:h-40 bg-gray-100 overflow-hidden">
                        @if ($item->gambar)
                            <img
                                src="{{ asset('storage/'.$item->gambar) }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                alt="{{ $item->nama_barang }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">
                                No Image
                            </div>
                        @endif

                        {{-- Badge stok --}}
                        @if((int)$item->stok <= 0)
                            <span class="absolute top-2 left-2 text-[10px] px-2 py-1 rounded-full bg-red-600 text-white">
                                Habis
                            </span>
                        @elseif((int)$item->stok <= 3)
                            <span class="absolute top-2 left-2 text-[10px] px-2 py-1 rounded-full bg-amber-500 text-white">
                                Stok Tipis
                            </span>
                        @else
                            <span class="absolute top-2 left-2 text-[10px] px-2 py-1 rounded-full bg-green-600 text-white">
                                Ready
                            </span>
                        @endif

                    </div>
                </a>

                {{-- Detail --}}
                <div class="p-3">
                    <h3 class="text-sm font-semibold text-gray-800 line-clamp-1">
                        {{ $item->nama_barang }}
                    </h3>

                    <p class="mt-1 text-[12px] text-gray-600 leading-snug line-clamp-2">
                        {{ $item->deskripsi }}
                    </p>

                    @if ($item->penjual)
                        <p class="mt-2 text-[11px] text-gray-500 line-clamp-1">
                            📍 {{ $item->penjual->alamat_toko }}
                        </p>
                    @endif

                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-sm text-green-700 font-bold whitespace-nowrap">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-gray-500 whitespace-nowrap">
                            Stok {{ $item->stok }}
                        </span>
                    </div>

                    <!-- <a href="{{ route('pembeli.produk.detail', $item->id) }}?back={{ urlencode(url()->full()) }}"
                    class="mt-3 flex items-center justify-center gap-1.5
                            text-xs font-medium text-green-700
                            hover:text-green-900 transition">
                        👁
                        <span>Lihat detail</span>
                    </a> -->
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white border rounded-xl p-6 text-center">
                <p class="text-sm text-gray-600">Belum ada produk tersedia.</p>
            </div>
        @endforelse
    </div>
</section>

@endsection
