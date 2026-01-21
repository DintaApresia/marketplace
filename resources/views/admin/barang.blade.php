@extends('layouts.admin')
@section('title', 'Produk Penjual')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">

        {{-- JUDUL --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Produk Penjual</h1>

            {{-- TOMBOL KEMBALI --}}
            <a href="{{ route('admin.toko.show') }}"
               class="inline-block mt-2 text-xs sm:text-sm text-gray-500 hover:text-gray-700">
                ← Kembali ke halaman toko
            </a>
        </div>

        {{-- SEARCH --}}
        <form method="GET" action="{{ route('admin.toko.barang', $penjualId) }}" class="flex gap-2">

            <input
                type="text"
                name="q"
                value="{{ $q ?? '' }}"
                placeholder="Cari nama / deskripsi..."
                class="w-64 rounded-md border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-green-600 focus:outline-none"
            />

            <button class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                Cari
            </button>
        </form>

    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow border border-gray-100 overflow-hidden">

        <div class="px-5 py-4 border-b flex items-center justify-between">
            <p class="text-sm text-gray-600">
                Total data: <span class="font-semibold">{{ $barangs->total() }}</span>
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Pengguna</th>
                        <th class="px-4 py-3 text-left">Gambar</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Harga</th>
                        <th class="px-4 py-3 text-left">Stok</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Dibuat</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($barangs as $i => $b)
                        <tr class="hover:bg-gray-50">

                            {{-- # --}}
                            <td class="px-4 py-3">
                                {{ $barangs->firstItem() + $i }}
                            </td>

                            {{-- Pengguna --}}
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">
                                    {{ $b->penjual->user->name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Toko: {{ $b->penjual->nama_toko ?? '-' }}
                                </div>
                            </td>

                            {{-- Gambar --}}
                            <td class="px-4 py-3">
                                @if (!empty($b->gambar))
                                    <img
                                        src="{{ asset('storage/' . $b->gambar) }}"
                                        class="w-12 h-12 object-cover rounded border"
                                        alt="gambar"
                                    >
                                @else
                                    <div class="w-12 h-12 rounded border bg-gray-100 flex items-center justify-center text-xs text-gray-400">
                                        N/A
                                    </div>
                                @endif
                            </td>

                            {{-- Nama --}}
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-800">
                                    {{ $b->nama_barang }}
                                </div>
                                <div class="text-xs text-gray-500 line-clamp-2">
                                    {{ $b->deskripsi }}
                                </div>
                            </td>

                            {{-- Harga --}}
                            <td class="px-4 py-3">
                                Rp {{ number_format((float) $b->harga, 0, ',', '.') }}
                            </td>

                            {{-- Stok --}}
                            <td class="px-4 py-3">
                                {{ (int) $b->stok }}
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3">
                                @if ($b->stok <= 0)
                                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                                        Nonaktif
                                    </span>
                                @elseif ($b->is_active)
                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                                        Aktif
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- Dibuat --}}
                            <td class="px-4 py-3 text-gray-600">
                                {{ optional($b->created_at)->format('d M Y') }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-500">
                                Data barang belum ada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-5 py-4 border-t">
            {{ $barangs->appends(request()->query())->links() }}
        </div>

    </div>
</div>
@endsection
