@extends('layouts.admin')

@section('title', 'Daftar Toko')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    <h1 class="text-2xl font-bold mb-2">Daftar Toko</h1>
    <p class="text-gray-500 mb-6">
        Menampilkan semua toko (penjual) yang terdaftar.
    </p>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left border">No</th>
                    <th class="px-4 py-3 text-left border">Nama Toko</th>
                    <th class="px-4 py-3 text-left border">Nama Penjual</th>
                    <th class="px-4 py-3 text-left border">No HP</th>
                    <th class="px-4 py-3 text-center border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penjuals as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">
                            {{ $index + 1 }}
                        </td>

                        <td class="px-4 py-2 border font-medium">
                            {{ $item->penjual->nama_toko ?? '-' }}
                        </td>

                        <td class="px-4 py-2 border">
                            {{ $item->name }}
                        </td>

                        <td class="px-4 py-2 border">
                            {{ $item->penjual->no_telp ?? '-' }}
                        </td>

                        <td class="px-4 py-2 border text-center space-x-2">
                            <a href="{{ route('admin.toko.barang', $item->id) }}"
                            class="inline-block px-3 py-1.5 rounded-md bg-indigo-600 text-white text-xs font-medium hover:bg-indigo-700 transition">
                                Lihat Barang
                            </a>

                            <!-- <a href="{{ route('admin.toko.barang', $item->id) }}"
                            class="inline-block px-3 py-1.5 rounded-md bg-yellow-500 text-white text-xs font-medium hover:bg-yellow-600 transition">
                                Lihat Hasil Penjualan
                            </a> -->
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            Belum ada toko yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
