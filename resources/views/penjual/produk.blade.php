@extends('layouts.penjual')
@section('title','Barang')
@section('content')

@php
    // kalau ada error edit, auto buka modal edit yang sesuai
    $openEditId = null;
    foreach ($produks as $p) {
        if ($errors->getBag('edit_'.$p->id)->any()) {
            $openEditId = $p->id;
            break;
        }
    }
@endphp

<div class="max-w-5xl mx-auto px-4 py-6">

    {{-- ALERT SUCCESS --}}
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- HEADER + BUTTON --}}
    <div class="flex items-center justify-between mb-4 w-full">
        <h2 class="text-lg font-semibold">Daftar Produk Saya</h2>

        <button onclick="openForm()"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm shadow">
            + Tambah Produk
        </button>
    </div>

    <div class="w-full">
        <div class="col-span-2 w-full">

            @if ($produks->isEmpty())
                <div class="p-4 bg-gray-100 text-gray-600 rounded">
                    Belum ada produk yang ditambahkan.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($produks as $produk)
                        <div class="bg-white border rounded-lg shadow p-4 flex gap-4 relative">

                            {{-- Badge stok habis --}}
                            @if ((int)$produk->stok === 0)
                                <span class="absolute top-2 right-2 text-xs bg-red-100 text-red-700 px-2 py-1 rounded">
                                    Stok habis
                                </span>
                            @endif

                            {{-- Gambar --}}
                            <div class="w-24 h-24 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                                @if ($produk->gambar)
                                    <img src="{{ asset('storage/' . $produk->gambar) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                                        Tidak ada gambar
                                    </div>
                                @endif
                            </div>

                            {{-- Info Produk --}}
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="font-semibold text-base">{{ $produk->nama_barang }}</h3>

                                    <p class="text-sm text-gray-700 mt-2 line-clamp-2">
                                        {{ $produk->deskripsi ?? '-' }}
                                    </p>

                                    <div class="text-sm text-gray-600 mt-1 space-y-0.5">
                                        <p>Harga: <span class="font-semibold">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span></p>
                                        <p>Stok: {{ $produk->stok }}</p>
                                        <p>Status:
                                            @if ((int)$produk->stok > 0)
                                                <span class="text-green-600 font-semibold">Aktif</span>
                                            @else
                                                <span class="text-red-600 font-semibold">Nonaktif</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- Aksi --}}
                                <div class="flex items-center justify-between mt-3 text-sm">
                                    <button type="button"
                                            onclick="openEditForm({{ $produk->id }})"
                                            class="text-blue-600 hover:underline">
                                        Edit
                                    </button>

                                    <form action="{{ route('produk.destroy', $produk->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                {{-- PAGINATION --}}
                <div class="mt-4">
                    {{ $produks->links() }}
                </div>
            @endif

        </div>
    </div>

</div>

{{-- ========================= --}}
{{-- MODAL TAMBAH PRODUK --}}
{{-- ========================= --}}
<div id="formModal" class="fixed inset-0 bg-black bg-opacity-40 hidden backdrop-blur-sm z-50 overflow-y-auto">
    <div class="min-h-screen flex justify-center items-start pt-10 pb-10">
        <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 mx-4">

            <h2 class="text-lg font-semibold mb-4">Tambah Produk</h2>

            {{-- ERROR BAG CREATE --}}
            @if ($errors->getBag('create')->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->getBag('create')->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1">Nama Barang</label>
                    <input name="nama_barang"
                           value="{{ old('nama_barang') }}"
                           class="w-full border rounded px-3 py-2 text-sm"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Harga</label>
                    <input type="number" name="harga" min="0"
                           value="{{ old('harga') }}"
                           class="w-full border rounded px-3 py-2 text-sm"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Stok</label>
                    <input type="number" name="stok" min="0"
                           value="{{ old('stok') }}"
                           class="w-full border rounded px-3 py-2 text-sm"
                           required>
                    <p class="text-xs text-gray-500 mt-1">Jika stok 0, produk otomatis nonaktif.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Deskripsi</label>
                    <textarea name="deskripsi"
                              class="w-full border rounded px-3 py-2 text-sm"
                              required>{{ old('deskripsi') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Gambar Produk</label>
                    {{-- kalau kamu mau gambar wajib, biarkan required --}}
                    <input type="file" name="gambar" class="w-full text-sm" required>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeForm()" class="px-4 py-2 bg-gray-300 rounded text-sm">
                        Batal
                    </button>

                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded text-sm">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ========================= --}}
{{-- MODAL EDIT PRODUK --}}
{{-- ========================= --}}
@foreach ($produks as $produk)
<div id="editModal-{{ $produk->id }}"
    class="fixed inset-0 bg-black bg-opacity-40 hidden backdrop-blur-sm z-50 overflow-y-auto">

    <div class="min-h-screen flex justify-center items-start pt-10 pb-10">
        <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 mx-4">
            <h2 class="text-lg font-semibold mb-4">Edit Produk</h2>

            {{-- ERROR BAG EDIT PER PRODUK --}}
            @if ($errors->getBag('edit_'.$produk->id)->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->getBag('edit_'.$produk->id)->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('produk.update', $produk->id) }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium mb-1">Nama Barang</label>
                    <input type="text" name="nama_barang"
                        value="{{ old('nama_barang', $produk->nama_barang) }}"
                        class="w-full border rounded px-3 py-2 text-sm"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Harga</label>
                    <input type="number" name="harga" min="0"
                        value="{{ old('harga', $produk->harga) }}"
                        class="w-full border rounded px-3 py-2 text-sm"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Stok</label>
                    <input type="number" name="stok" min="0"
                        value="{{ old('stok', $produk->stok) }}"
                        class="w-full border rounded px-3 py-2 text-sm"
                        required>
                    <p class="text-xs text-gray-500 mt-1">Jika stok 0, produk otomatis nonaktif.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Deskripsi</label>
                    <textarea name="deskripsi"
                              class="w-full border rounded px-3 py-2 text-sm"
                              required>{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Gambar Produk</label>
                    @if ($produk->gambar)
                        <img src="{{ asset('storage/' . $produk->gambar) }}" class="w-24 h-24 object-cover rounded border mb-2">
                    @endif
                    {{-- biasanya edit: gambar opsional --}}
                    <input type="file" name="gambar" class="w-full text-sm">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti gambar.</p>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                        onclick="closeEditForm({{ $produk->id }})"
                        class="px-4 py-2 bg-gray-300 rounded text-sm">
                        Batal
                    </button>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach


{{-- ========================= --}}
{{-- SCRIPT MODAL --}}
{{-- ========================= --}}
<script>
function openForm() {
    document.getElementById('formModal')?.classList.remove('hidden');
}

function closeForm() {
    document.getElementById('formModal')?.classList.add('hidden');
}

function openEditForm(id) {
    document.getElementById('editModal-' + id)?.classList.remove('hidden');
}

function closeEditForm(id) {
    document.getElementById('editModal-' + id)?.classList.add('hidden');
}
</script>

{{-- ========================= --}}
{{-- SCRIPT ANTI MINUS --}}
{{-- ========================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const noMinusInputs = document.querySelectorAll('input[type="number"]');

    noMinusInputs.forEach(input => {
        input.addEventListener('keydown', function (e) {
            if (e.key === '-' || e.key === 'Minus') {
                e.preventDefault();
            }
        });

        input.addEventListener('input', function () {
            if (this.value < 0) this.value = 0;
        });
    });
});
</script>

{{-- ========================= --}}
{{-- AUTO OPEN MODAL KETIKA ERROR --}}
{{-- ========================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // kalau error tambah produk
    @if ($errors->getBag('create')->any())
        openForm();
    @endif

    // kalau error edit produk tertentu
    @if ($openEditId)
        openEditForm({{ $openEditId }});
    @endif
});
</script>

@endsection