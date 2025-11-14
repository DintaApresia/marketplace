{{-- Profile > Buyer prefs (khusus pembeli) --}}
<div class="p-4 sm:p-6">
  <h2 class="text-lg font-semibold text-green-700">Preferensi Pembeli</h2>
  <p class="text-sm text-gray-600">Atur alamat pengiriman & notifikasi.</p>

  {{-- Alamat pengiriman (contoh form sederhana) --}}
  <form method="POST" action="#" class="mt-4 grid gap-4 sm:grid-cols-2">
    @csrf
    <div>
      <label class="block text-sm text-gray-700">Nama Penerima</label>
      <input name="receiver_name" class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600" placeholder="Nama penerima">
    </div>
    <div>
      <label class="block text-sm text-gray-700">No. Telepon</label>
      <input name="phone" class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600" placeholder="08xxxxxxxxxx">
    </div>
    <div class="sm:col-span-2">
      <label class="block text-sm text-gray-700">Alamat</label>
      <input name="address_line" class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600" placeholder="Jalan, RT/RW, Kel/Desa">
    </div>
    <div>
      <label class="block text-sm text-gray-700">Kota/Kabupaten</label>
      <input name="city" class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600" placeholder="Kota/Kab">
    </div>
    <div>
      <label class="block text-sm text-gray-700">Kode Pos</label>
      <input name="postal_code" class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600" placeholder="60111">
    </div>

    <div class="sm:col-span-2">
      <button class="rounded-md bg-green-700 text-white px-4 py-2 hover:bg-green-800">
        Simpan Alamat
      </button>
    </div>
  </form>

  {{-- Notifikasi --}}
  <div class="mt-6">
    <h3 class="font-medium text-gray-800">Notifikasi</h3>
    <form method="POST" action="#" class="mt-2 grid gap-2">
      @csrf
      <label class="inline-flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" class="rounded border-gray-300 text-green-700 focus:ring-green-600" checked>
        Promo & voucher
      </label>
      <label class="inline-flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" class="rounded border-gray-300 text-green-700 focus:ring-green-600" checked>
        Update status pesanan
      </label>
      <button class="mt-3 self-start rounded-md bg-green-700 text-white px-4 py-2 hover:bg-green-800">
        Simpan Preferensi
      </button>
    </form>
  </div>
</div>
