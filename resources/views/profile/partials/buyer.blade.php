<div class="p-4 sm:p-6">
  <h2 class="text-lg font-semibold text-green-700">Preferensi Pembeli</h2>
  <p class="text-sm text-gray-600">Atur alamat pengiriman & lokasi pengiriman.</p>

  @if(session('success'))
    <div class="mt-3 mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-lg">
      {{ session('success') }}
    </div>
  @endif

  <form method="POST" action="{{ route('pembeli.preferensi') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
    @csrf

    {{-- Nama --}}
    <div>
      <label class="block text-sm text-gray-700">Nama Penerima</label>
      <input
        name="receiver_name"
        value="{{ old('receiver_name', $pembeli->nama_pembeli ?? '') }}"
        class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
        placeholder="Nama penerima">
    </div>

    {{-- No Telp --}}
    <div>
      <label class="block text-sm text-gray-700">No. Telepon</label>
      <input
        name="phone"
        value="{{ old('phone', $pembeli->no_telp ?? '') }}"
        class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
        placeholder="08xxxxxxxxxx">
    </div>

    {{-- Alamat --}}
    <div class="sm:col-span-2">
      <label class="block text-sm text-gray-700">Alamat</label>
      <input
        id="address_line"
        name="address_line"
        value="{{ old('address_line', $pembeli->alamat ?? '') }}"
        class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
        placeholder="Tulis alamat lengkap, contoh: Jalan Soekarno Hatta No. 10, Surabaya">
      <p id="location-status" class="mt-1 text-xs text-gray-500">
        Map akan menyesuaikan otomatis berdasarkan alamat yang kamu ketik (tunggu sebentar setelah mengetik).
      </p>
    </div>

    {{-- Latitude --}}
    <div>
      <label class="block text-sm text-gray-700">Latitude</label>
      <input
        id="latitude"
        name="latitude"
        value="{{ old('latitude', $pembeli->latitude ?? '') }}"
        class="mt-1 w-full rounded-md border px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-green-600"
        readonly>
    </div>

    {{-- Longitude --}}
    <div>
      <label class="block text-sm text-gray-700">Longitude</label>
      <input
        id="longitude"
        name="longitude"
        value="{{ old('longitude', $pembeli->longitude ?? '') }}"
        class="mt-1 w-full rounded-md border px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-green-600"
        readonly>
    </div>

    {{-- TOMBOL SIMPAN --}}
    <div class="sm:col-span-2 mt-3">
      <button class="rounded-md bg-green-700 text-white px-4 py-2 hover:bg-green-800">
        Simpan Preferensi
      </button>
    </div>

    {{-- Peta Leaflet --}}
    <div class="sm:col-span-2">
      <label class="block text-sm text-gray-700 mb-1">Lokasi di Peta</label>
      <div id="map" class="w-full h-56 sm:h-64 rounded-md border"></div>
      <p class="mt-1 text-xs text-gray-500">
        Kamu juga bisa klik di peta untuk menggeser titik lokasi secara manual.
      </p>
    </div>
  </form>

  {{-- 🔹 Status Akun Penjual --}}
<div class="mt-8 border-t pt-4">
  <h3 class="text-sm font-semibold text-gray-800">
    Status Akun Penjual
  </h3>

  @if($user->seller_status === 'pending')
    <p class="mt-1 text-sm text-yellow-800">
      Permintaan kamu untuk menjadi <span class="font-semibold">penjual</span> sedang dalam proses peninjauan oleh admin.
      Kamu masih dapat berbelanja seperti biasa sebagai pembeli. Notifikasi akan muncul di sini setelah ada keputusan.
    </p>

    @if($user->penjual)
      <a href="{{ route('penjual.pengajuan-saya') }}"
         class="mt-2 inline-flex items-center rounded-md bg-green-700 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-800">
        Lihat Data Pengajuan Penjual
      </a>
    @endif

  @elseif($user->seller_status === 'rejected')
    <p class="mt-1 text-sm text-red-700">
      Permintaan kamu untuk menjadi <span class="font-semibold">penjual</span> sebelumnya
      <span class="font-semibold">ditolak</span> oleh admin.
      Periksa kembali data toko yang kamu ajukan, lalu kirim ulang permintaan jika sudah diperbaiki.
    </p>

    @if($user->penjual)
      <a href="{{ route('penjual.pengajuan-saya') }}"
         class="mt-2 inline-flex items-center rounded-md bg-green-700 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-800">
        Lihat Data Pengajuan Penjual
      </a>
    @endif
  @else
    <p class="mt-1 text-sm text-gray-700">
      Saat ini akun kamu terdaftar sebagai <span class="font-semibold">pembeli</span> di SecondLife.
      Jika ingin membuka lapak dan menjual barang, kamu perlu mendaftar sebagai
      <span class="font-semibold">penjual</span> terlebih dahulu.
    </p>
    <p class="mt-1 text-sm text-gray-600">
      Setelah mengajukan pendaftaran, tim admin akan meninjau data kamu.
      Jika disetujui, akunmu akan diaktifkan sebagai penjual dan kamu bisa mengakses dashboard penjual.
    </p>
    <a href="{{ route('penjual.daftar') }}"
       class="mt-2 inline-flex items-center rounded-md bg-green-700 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-800">
      Daftar jadi Penjual
    </a>
  @endif
</div>


{{-- LEAFLET CSS --}}
<link
  rel="stylesheet"
  href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>

{{-- LEAFLET JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const latInput      = document.getElementById('latitude');
    const lngInput      = document.getElementById('longitude');
    const addressInput  = document.getElementById('address_line');
    const statusEl      = document.getElementById('location-status');

    let initialLat = -2.5;
    let initialLng = 118.0;
    let initialZoom = 5;

    @if(isset($pembeli) && $pembeli->latitude && $pembeli->longitude)
        initialLat = {{ $pembeli->latitude }};
        initialLng = {{ $pembeli->longitude }};
        initialZoom = 16;
    @endif

    const map = L.map('map').setView([initialLat, initialLng], initialZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;
    let geocodeTimeout = null;

    // kalau sudah ada titik dari DB, munculkan
    @if(isset($pembeli) && $pembeli->latitude && $pembeli->longitude)
        marker = L.marker([{{ $pembeli->latitude }}, {{ $pembeli->longitude }}]).addTo(map);
    @endif

    // ======================
    // 1. REVERSE GEOCODING (lat,lng -> alamat)
    // ======================
    async function reverseGeocode(lat, lon) {
      try {
        statusEl.textContent = 'Mengambil alamat dari titik peta...';

        const url =
          'https://nominatim.openstreetmap.org/reverse?format=jsonv2'
          + '&lat=' + encodeURIComponent(lat)
          + '&lon=' + encodeURIComponent(lon)
          + '&addressdetails=1';

        const res = await fetch(url, { headers: { 'Accept-Language': 'id' } });

        if (!res.ok) {
          console.warn('Reverse geocode HTTP error', res.status);
          statusEl.textContent = 'Tidak bisa mengambil alamat (HTTP ' + res.status + ').';
          return;
        }

        const data = await res.json();
        console.log('Reverse geocode result:', data); // buat cek di console

        if (data && data.display_name) {
          addressInput.value = data.display_name;
          statusEl.textContent = 'Alamat berhasil diperbarui dari titik peta.';
        } else {
          statusEl.textContent = 'Alamat tidak ditemukan untuk titik ini.';
        }
      } catch (err) {
        console.error('Reverse geocode error:', err);
        statusEl.textContent = 'Gagal mengambil alamat dari titik peta.';
      }
    }

    // ======================
    // 2. FORWARD GEOCODING (alamat -> lat,lng)
    // ======================
    async function geocodeAddress(query) {
      if (!query || query.length < 5) {
        statusEl.textContent = '';
        return;
      }

      try {
        statusEl.textContent = 'Mencari lokasi dari alamat...';

        const url =
          'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q='
          + encodeURIComponent(query);

        const res = await fetch(url, { headers: { 'Accept-Language': 'id' } });

        if (!res.ok) {
          console.warn('Forward geocode HTTP error', res.status);
          statusEl.textContent = 'Gagal mencari alamat (HTTP ' + res.status + ').';
          return;
        }

        const results = await res.json();
        console.log('Forward geocode result:', results);

        if (!results || results.length === 0) {
          statusEl.textContent = 'Alamat tidak ditemukan. Coba lebih spesifik.';
          return;
        }

        const place = results[0];
        const lat   = parseFloat(place.lat);
        const lon   = parseFloat(place.lon);

        latInput.value = lat.toFixed(6);
        lngInput.value = lon.toFixed(6);

        const latLng = [lat, lon];

        if (marker) marker.setLatLng(latLng);
        else marker = L.marker(latLng).addTo(map);

        map.setView(latLng, 16);
        statusEl.textContent = 'Lokasi ditemukan dari alamat.';
      } catch (err) {
        console.error('Forward geocode error:', err);
        statusEl.textContent = 'Terjadi kesalahan saat mencari alamat.';
      }
    }

    // ketika user ngetik alamat
    addressInput.addEventListener('input', function () {
      const query = this.value.trim();
      clearTimeout(geocodeTimeout);
      geocodeTimeout = setTimeout(() => geocodeAddress(query), 700);
    });

    // ======================
    // 3. KLIK MAP -> UPDATE LAT/LNG + ALAMAT
    // ======================
    map.on('click', function (e) {
      const lat = e.latlng.lat;
      const lon = e.latlng.lng;

      latInput.value = lat.toFixed(6);
      lngInput.value = lon.toFixed(6);

      if (marker) {
        marker.setLatLng(e.latlng);
      } else {
        marker = L.marker(e.latlng).addTo(map);
      }

      map.setView(e.latlng, 16);

      // fallback langsung: isi alamat dengan "Lat: xx, Lng: yy"
      addressInput.value = 'Lat: ' + lat.toFixed(6) + ', Lng: ' + lon.toFixed(6);
      statusEl.textContent = 'Titik peta diperbarui, mengambil alamat...';

      // lalu coba ambil alamat asli
      reverseGeocode(lat, lon);
    });

});
</script>

<style>
  #map {
      height: 300px !important;   /* tinggi kecil */
      width: 100% !important;     /* supaya responsive */
      border-radius: 8px;
      border: 1px solid #ddd;
  }
</style>