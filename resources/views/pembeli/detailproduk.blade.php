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

<div class="max-w-6xl mx-auto px-4 py-6">

    {{-- Breadcrumb --}}
    <div class="text-sm text-gray-500 mb-4">
        <a href="{{ route('pembeli.dashboard') }}" class="hover:underline">Home</a>
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
                <div class="pt-3 border-t">
                    <p class="text-sm font-medium">
                        {{ $produk->penjual->nama_toko ?? 'Toko' }}
                    </p>
                    <p class="text-xs text-gray-500">
                        📍 {{ $produk->penjual->alamat_toko }}
                    </p>
                </div>
            @endif

            {{-- Aksi --}}
            <div class="pt-4 mt-auto flex gap-3">
                <form action="{{ route('pembeli.keranjang.tambah', $produk->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button
                        class="w-full bg-green-600 hover:bg-green-700 text-white text-sm py-2 rounded-md
                            disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ $produk->stok <= 0 ? 'disabled' : '' }}>
                        Tambah ke Keranjang
                    </button>
                </form>
                <button
                    class="flex-1 border border-green-600 text-green-700 hover:bg-green-50 text-sm py-2 rounded-md">
                    Chat
                </button>
            </div>

        </div>
    </div>
</div>
@endsection
