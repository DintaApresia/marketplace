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

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form id="formBarang"
              method="POST"
              action="{{ route('admin.barang.update', $produk->id) }}"
              enctype="multipart/form-data"
              class="space-y-5">
            @csrf
            @method('PATCH')

            {{-- Nama Barang --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                <input type="text"
                       name="nama_barang"
                       value="{{ old('nama_barang', $produk->nama_barang) }}"
                       data-label="Nama barang"
                       required
                       class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:outline-none {{ $errors->has('nama_barang') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-600' }}">
                @error('nama_barang')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi"
                          rows="4"
                          data-label="Deskripsi"
                          required
                          class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:outline-none {{ $errors->has('deskripsi') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-600' }}">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                {{-- Harga --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                    <input type="number"
                           name="harga"
                           min="0"
                           value="{{ old('harga', $produk->harga) }}"
                           data-label="Harga"
                           required
                           class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:outline-none {{ $errors->has('harga') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-600' }}">
                    @error('harga')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Stok --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                    <input type="number"
                           name="stok"
                           min="0"
                           value="{{ old('stok', $produk->stok) }}"
                           data-label="Stok"
                           required
                           class="w-full rounded-lg border px-3 py-2 text-sm focus:ring-2 focus:outline-none {{ $errors->has('stok') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-green-600' }}">
                    @error('stok')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>

                <div class="flex items-center gap-4" data-radio-group="is_active">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="radio"
                               name="is_active"
                               value="1"
                               data-label="Status"
                               required
                               @checked((int) old('is_active', $produk->is_active) === 1)>
                        <span>Aktif</span>
                    </label>

                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="radio"
                               name="is_active"
                               value="0"
                               data-label="Status"
                               @checked((int) old('is_active', $produk->is_active) === 0)>
                        <span>Nonaktif</span>
                    </label>
                </div>

                @error('is_active')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Gambar (opsional) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar (opsional)</label>

                @if (!empty($produk->gambar))
                    <img src="{{ asset('storage/' . $produk->gambar) }}"
                         class="w-28 h-28 object-cover rounded-lg border mb-3"
                         alt="gambar">
                @endif

                <input type="file"
                       name="gambar"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white">
                <p class="text-xs text-gray-500 mt-1">jpg/jpeg/png/webp, maks 2MB.</p>

                @error('gambar')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('admin.toko.barang',  $user) }}"
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

{{-- SCRIPT VALIDASI CLIENT-SIDE (opsional) --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('formBarang');
  if (!form) return;

  function ensureErrorEl(container) {
    let el = container.querySelector('.js-field-error');
    if (!el) {
      el = document.createElement('p');
      el.className = 'js-field-error mt-1 text-xs text-red-600';
      container.appendChild(el);
    }
    return el;
  }

  function clearError(container) {
    const el = container.querySelector('.js-field-error');
    if (el) el.remove();
  }

  function markInvalid(input) {
    input.classList.remove('border-gray-300');
    input.classList.add('border-red-500');
  }

  function markValid(input) {
    input.classList.remove('border-red-500');
    if (!input.classList.contains('border-gray-300')) input.classList.add('border-gray-300');
  }

  function validateTextNumberField(input) {
    const wrapper = input.closest('div');
    if (!wrapper) return true;

    clearError(wrapper);
    markValid(input);

    const label = input.dataset.label || 'Field ini';

    if (!input.value || !String(input.value).trim()) {
      markInvalid(input);
      ensureErrorEl(wrapper).textContent = `${label} wajib diisi.`;
      return false;
    }

    if (input.type === 'number') {
      const minAttr = input.getAttribute('min');
      const val = Number(input.value);
      if (!Number.isFinite(val)) {
        markInvalid(input);
        ensureErrorEl(wrapper).textContent = `${label} wajib diisi.`;
        return false;
      }
      if (minAttr !== null && val < Number(minAttr)) {
        markInvalid(input);
        ensureErrorEl(wrapper).textContent = `${label} minimal ${minAttr}.`;
        return false;
      }
    }

    return true;
  }

  function validateRadioGroup(name) {
    const radios = form.querySelectorAll(`input[type="radio"][name="${name}"]`);
    if (!radios.length) return true;

    const groupContainer = form.querySelector(`[data-radio-group="${name}"]`);
    if (!groupContainer) return true;

    clearError(groupContainer);

    const checked = Array.from(radios).some(r => r.checked);
    const label = radios[0].dataset.label || 'Pilihan ini';

    if (!checked) {
      ensureErrorEl(groupContainer).textContent = `${label} wajib dipilih.`;
      return false;
    }
    return true;
  }

  form.addEventListener('submit', (e) => {
    let ok = true;

    const req = form.querySelectorAll('[required]:not([type="file"])');
    req.forEach((el) => {
      if (el.type === 'radio') return;
      if (!validateTextNumberField(el)) ok = false;
    });

    if (!validateRadioGroup('is_active')) ok = false;

    if (!ok) {
      e.preventDefault();
      const firstErr = form.querySelector('.js-field-error') || form.querySelector('.border-red-500');
      if (firstErr) firstErr.scrollIntoView({behavior:'smooth', block:'center'});
    }
  });

  form.querySelectorAll('input:not([type="file"]), textarea').forEach(el => {
    el.addEventListener('input', () => {
      if (el.type === 'radio') return;
      validateTextNumberField(el);
    });
    el.addEventListener('change', () => {
      if (el.type === 'radio') validateRadioGroup(el.name);
    });
  });
});
</script>
@endsection
