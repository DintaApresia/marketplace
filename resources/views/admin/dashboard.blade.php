@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Judul --}}
    <h1 class="text-2xl font-bold text-gray-800 mb-2">
        Dashboard Admin
    </h1>
    <p class="text-sm text-gray-500 mb-6">
        Halaman ini khusus untuk admin. Nanti dari sini bisa mengelola user, penjual, dan barang.
    </p>

        {{-- Info singkat --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="p-4 bg-white rounded shadow">
            <p class="text-gray-500 text-sm">Total User</p>
            <p class="text-3xl font-bold">
                {{ $totalUsers ?? 0 }}
            </p>
        </div>

        <div class="p-4 bg-white rounded shadow">
            <p class="text-gray-500 text-sm">Total Penjual</p>
            <p class="text-3xl font-bold">
                {{ $totalPenjual ?? 0 }}
            </p>
        </div>

        <div class="p-4 bg-white rounded shadow">
            <p class="text-gray-500 text-sm">Total Pembeli</p>
            <p class="text-3xl font-bold">
                {{ $totalPembeli ?? 0 }}
            </p>
        </div>
    </div>

    {{-- Menu sederhana --}}
    <div class="bg-white rounded-lg shadow border border-gray-100 p-4">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">
            Menu Admin
        </h2>

        <div class="flex flex-col gap-2 text-sm">
            <a href="#" class="px-4 py-2 rounded border border-gray-200 hover:bg-gray-50">
                Kelola User
            </a>
            <a href="{{ route('admin.penjual.index') }}" class="px-4 py-2 rounded border border-gray-200 hover:bg-gray-50">
                Verifikasi Penjual
            </a>
            <a href="#" class="px-4 py-2 rounded border border-gray-200 hover:bg-gray-50">
                Kelola Barang
            </a>
        </div>
    </div>

</div>
@endsection
