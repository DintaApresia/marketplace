@extends('layouts.penjual')

@section('title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

    {{-- BOX INFORMASI TOKO --}}
    <div class="p-5 bg-white shadow rounded border">
        <h3 class="text-sm font-semibold text-gray-600">Status Toko</h3>
        <p class="text-xl font-bold mt-2 text-green-700">
            Aktif
        </p>
        <p class="text-xs text-gray-500 mt-2">
            Toko Anda sudah bisa menerima pesanan.
        </p>
    </div>

    {{-- PRODUK --}}
    <div class="p-5 bg-white shadow rounded border">
        <h3 class="text-sm font-semibold text-gray-600">Total Produk</h3>

        <p class="text-3xl font-bold mt-2 text-blue-600">
            {{ $totalProduk }}
        </p>

        @if ($totalProduk == 0)
            <p class="text-xs text-gray-500 mt-2">
                Belum ada produk. Tambahkan produk pertama Anda.
            </p>
        @else
            <p class="text-xs text-gray-500 mt-2">
                Produk aktif di toko Anda
            </p>
        @endif
    </div>

    {{-- PESANAN --}}
    <div class="p-5 bg-white shadow rounded border">
        <h3 class="text-sm font-semibold text-gray-600">Pesanan Baru</h3>
        <p class="text-3xl font-bold mt-2 text-yellow-600">
            0
        </p>
        <p class="text-xs text-gray-500 mt-2">
            Belum ada pesanan masuk.
        </p>
    </div>

</div>

{{-- BAGIAN BAWAH --}}
<div class="mt-6 p-6 bg-white border rounded shadow">
    <h3 class="text-lg font-semibold text-gray-800">Informasi Toko</h3>
    <p class="text-sm text-gray-600 mt-2">
        Kelola produk, pesanan, dan informasi toko Anda melalui menu di sebelah kiri.
    </p>
</div>

@endsection
