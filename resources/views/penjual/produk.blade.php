@extends('layouts.penjual')
@section('title','Barang')
@section('content')
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

    <div class="flex items-center justify-between mb-4 w-full">
        <div class="col-span-2">

            @if ($produks->isEmpty())
                <div class="p-4 bg-gray-100 text-gray-600 rounded">
                    Belum ada produk yang ditambahkan.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($produks as $produk)
                        @php
                            $penjual = $produk->user->penjual ?? null;
                        @endphp

                        <div class="bg-white border rounded-lg shadow p-4 flex gap-4">
                            
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
                                            @if ($produk->is_active)
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

            <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Barang</label>
                    <input name="nama_barang" class="w-full border rounded px-3 py-2 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Harga</label>
                    <input type="number" name="harga" min="0" class="w-full border rounded px-3 py-2 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Stok</label>
                    <input type="number" name="stok" min="0" class="w-full border rounded px-3 py-2 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Deskripsi</label>
                    <textarea name="deskripsi" class="w-full border rounded px-3 py-2 text-sm"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Gambar Produk</label>
                    <input type="file" name="gambar" class="w-full text-sm">
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

            <form action="{{ route('produk.update', $produk->id) }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium mb-1">Nama Barang</label>
                    <input type="text" name="nama_barang"
                        value="{{ $produk->nama_barang }}"
                        class="w-full border rounded px-3 py-2 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Harga</label>
                    <input type="number" name="harga" min="0"
                        value="{{ $produk->harga }}"
                        class="w-full border rounded px-3 py-2 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Stok</label>
                    <input type="number" name="stok" min="0"
                        value="{{ $produk->stok }}"
                        class="w-full border rounded px-3 py-2 text-sm" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Deskripsi</label>
                    <textarea name="deskripsi" class="w-full border rounded px-3 py-2 text-sm">{{ $produk->deskripsi }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Gambar Produk</label>
                    @if ($produk->gambar)
                    <img src="{{ asset('storage/' . $produk->gambar) }}" class="w-24 h-24 object-cover rounded border mb-2">
                    @endif
                    <input type="file" name="gambar" class="w-full text-sm">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti gambar.</p>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1"
                        {{ $produk->is_active ? 'checked' : '' }}>
                    <span class="text-sm">Aktif</span>
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


<script>
function openForm() {
    document.getElementById('formModal').classList.remove('hidden');
}

function closeForm() {
    document.getElementById('formModal').classList.add('hidden');
}

function openEditForm(id) {
    const modal = document.getElementById('editModal-' + id);
    if (modal) modal.classList.remove('hidden');
}

function closeEditForm(id) {
    const modal = document.getElementById('editModal-' + id);
    if (modal) modal.classList.add('hidden');
}
</script>

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

@endsection
