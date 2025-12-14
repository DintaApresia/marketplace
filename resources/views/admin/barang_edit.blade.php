@extends('layouts.admin')
@section('title', 'Edit Barang')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">

    <div class="mb-5">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Barang</h1>
        <p class="text-sm text-gray-500">Ubah data barang/produk.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.barang.update', $produk->id) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                <input type="text" name="nama_barang"
                       value="{{ old('nama_barang', $produk->nama_barang) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-green-600 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="4"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-green-600 focus:outline-none">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                    <input type="number" name="harga" min="0"
                           value="{{ old('harga', $produk->harga) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-green-600 focus:outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                    <input type="number" name="stok" min="0"
                           value="{{ old('stok', $produk->stok) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-green-600 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="radio" name="is_active" value="1"
                               @checked((int) old('is_active', $produk->is_active) === 1)>
                        <span>Aktif</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="radio" name="is_active" value="0"
                               @checked((int) old('is_active', $produk->is_active) === 0)>
                        <span>Nonaktif</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar (opsional)</label>

                @if (!empty($produk->gambar))
                    <img src="{{ asset('storage/' . $produk->gambar) }}"
                         class="w-28 h-28 object-cover rounded-lg border mb-3"
                         alt="gambar">
                @endif

                <input type="file" name="gambar"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white">
                <p class="text-xs text-gray-500 mt-1">jpg/jpeg/png/webp, maks 2MB.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('admin.barang') }}"
                   class="rounded-lg border px-4 py-2 text-sm hover:bg-gray-50">
                    Kembali
                </a>
                <button type="submit"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
