{{-- Profile > Seller settings (khusus penjual) --}}
<div class="p-4 sm:p-6">
  <h2 class="text-lg font-semibold text-green-700">Pengaturan Toko</h2>
  <p class="text-sm text-gray-600">Nama toko, rekening, & lokasi pickup.</p>

  {{-- Pengaturan toko --}}
  <form method="POST" action="#" class="mt-4 grid gap-4 sm:grid-cols-2">
    @csrf
    <div>
      <label class="block text-sm text-gray-700">Nama Toko</label>
      <input name="shop_name" class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600" placeholder="SecondLife Store">
    </div>
    <div>
      <label class="block text-sm text-gray-700">No. Telepon</label>
      <input name="shop_phone" class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600" placeholder="08xxxxxxxxxx">
    </div>
    <div class="sm:col-span-2">
      <label class="block text-sm text-gray-700">Deskripsi Toko</label>
      <textarea name="shop_description" rows="3" class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600" placeholder="Deskripsi singkat toko..."></textarea>
    </div>

    <div>
      <label class="block text-sm text-gray-700">Bank</label>
      <input name="bank" class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600" placeholder="BCA/BNI/BRI...">
    </div>
    <div>
      <label class="block text-sm text-gray-700">No. Rekening</label>
      <input name="bank_account" class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600" placeholder="1234567890">
    </div>

    <div class="sm:col-span-2 flex items-center justify-between">
      {{-- Ganti '#' dengan route('penjual.produk.create') saat route sudah ada --}}
      <a href="#" class="inline-flex items-center rounded-md bg-green-700 px-4 py-2 text-white hover:bg-green-800">
        + Tambah Barang
      </a>
      <button class="rounded-md bg-green-700 text-white px-4 py-2 hover:bg-green-800">
        Simpan Pengaturan
      </button>
    </div>
  </form>

  {{-- Lokasi Pickup (opsional) --}}
  <div class="mt-6">
    <div class="flex items-center justify-between">
      <h3 class="font-medium text-gray-800">Lokasi Pickup</h3>
      <a href="#" class="text-sm text-white bg-green-700 px-3 py-1.5 rounded-md hover:bg-green-800">
        + Tambah Lokasi
      </a>
    </div>

    {{-- Contoh daftar lokasi (dummy) --}}
    <div class="mt-3 grid gap-3 sm:grid-cols-2">
      <article class="rounded-lg border p-3">
        <div class="text-sm font-medium text-gray-800">Gudang Utama</div>
        <div class="text-sm text-gray-600">Jl. Melati No. 8, Surabaya</div>
        <div class="mt-2 flex gap-2">
          <button class="text-sm text-green-700 hover:underline">Edit</button>
          <button class="text-sm text-rose-600 hover:underline">Hapus</button>
        </div>
      </article>
    </div>
  </div>
</div>
