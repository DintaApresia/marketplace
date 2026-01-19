@extends('layouts.pembeli')
@section('title', $produk->nama_barang)

@section('content')
{{-- TOAST SUCCESS --}}
@if (session('success'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 2200)"
        x-show="show"
        x-transition
        class="fixed top-5 right-5 z-50"
    >
        <div class="bg-green-600 text-white text-sm px-4 py-3 rounded-lg shadow-lg flex items-center gap-2">
            <span>✅</span>
            <span>{{ session('success') }}</span>
            <button
                @click="show = false"
                class="ml-3 text-white/90 hover:text-white font-bold leading-none"
                aria-label="Close"
            >
                ×
            </button>
        </div>
    </div>
@endif

@php
    // URL asal (home / search / dll) dibawa dari ?back=
    $back = request('back');

    // keamanan: hanya izinkan URL internal (biar ga bisa redirect ke luar)
    if (!$back || !str_starts_with($back, url('/'))) {
        $back = route('pembeli.dashboard'); // fallback default
    }
@endphp

<div class="max-w-6xl mx-auto px-4 py-6">

    {{-- Breadcrumb --}}
    <div class="text-sm text-gray-500 mb-4">
        <a href="{{ $back }}" class="hover:underline">Kembali</a>
        <span class="mx-1">/</span>
        <span>{{ $produk->nama_barang }}</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- GAMBAR --}}
        <div class="bg-white border rounded-lg p-4">
            <div class="aspect-square max-h-[420px] mx-auto bg-gray-100 rounded overflow-hidden">
                @if ($produk->gambar)
                    <img
                        src="{{ asset('storage/'.$produk->gambar) }}"
                        class="w-full h-full object-cover"
                        alt="{{ $produk->nama_barang }}">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        No Image
                    </div>
                @endif
            </div>
        </div>

        {{-- INFO --}}
        <div class="bg-white border rounded-lg p-4 flex flex-col gap-3">

            <h1 class="text-base font-semibold leading-snug">
                {{ $produk->nama_barang }}
            </h1>

            <div class="text-lg font-bold text-green-700">
                Rp {{ number_format($produk->harga, 0, ',', '.') }}
            </div>

            <div class="text-sm text-gray-600">
                Stok: {{ $produk->stok }}
            </div>

            {{-- Deskripsi --}}
            <div class="pt-3 border-t">
                <h2 class="text-sm font-medium mb-1">Deskripsi</h2>
                <p class="text-sm text-gray-700 leading-relaxed">
                    {{ $produk->deskripsi }}
                </p>
            </div>

            {{-- Toko --}}
            @if ($produk->penjual)
                <div class="pt-4 mt-4 border-t space-y-2">
            <h3 class="text-sm font-semibold text-gray-800">
                Penjual
            </h3>

            <div class="flex items-start gap-2 text-sm text-gray-700">
                {{-- Icon toko --}}
                <svg class="w-4 h-4 mt-0.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 7h18M5 7l1 12h12l1-12M9 7V5a3 3 0 016 0v2"/>
                </svg>
                <span class="font-medium">
                    {{ $produk->penjual->nama_toko ?? 'Toko' }}
                </span>
            </div>

            <div class="flex items-start gap-2 text-xs text-gray-500">
                {{-- Icon lokasi --}}
                <svg class="w-4 h-4 mt-0.5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 11c1.1 0 2-.9 2-2a2 2 0 10-4 0c0 1.1.9 2 2 2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 22s8-4.5 8-11a8 8 0 10-16 0c0 6.5 8 11 8 11z"/>
                </svg>
                <span>{{ $produk->penjual->alamat_toko }}</span>
            </div>

            <div class="flex items-center gap-2 text-xs text-gray-500">
                {{-- Icon telepon --}}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 5a2 2 0 012-2h2l2 5-2 1a11 11 0 005 5l1-2 5 2v2a2 2 0 01-2 2A16 16 0 013 5z"/>
                </svg>
                <span>{{ $produk->penjual->no_telp }}</span>
            </div>
        </div>
            @endif

            {{-- Aksi --}}
            <div class="pt-4 mt-auto flex gap-3">
                {{-- Tambah ke Keranjang --}}
                <form action="{{ route('pembeli.keranjang.tambah', $produk->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button
                        type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white text-sm py-2 rounded-md
                            disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ $produk->stok <= 0 ? 'disabled' : '' }}>
                        Tambah ke Keranjang
                    </button>
                </form>

                {{-- Checkout --}}
                <a
                    href="{{ $produk->stok > 0 ? route('pembeli.checkout') : '#' }}"
                    class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 rounded-md
                        {{ $produk->stok <= 0 ? 'opacity-50 pointer-events-none' : '' }}">
                    Checkout
                </a>
            </div>


            {{-- Back --}}
            <a href="{{ $back }}" class="text-xs sm:text-sm text-gray-500 hover:text-gray-700">
                ← Kembali
            </a>
        </div>
    </div>
</div>
@endsection
