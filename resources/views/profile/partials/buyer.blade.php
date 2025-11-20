@include('profile.partials.account', ['user' => $user])

<div class="p-4 sm:p-6">
  <h2 class="text-lg font-semibold text-green-700">Preferensi Pembeli</h2>
  <p class="text-sm text-gray-600">Atur alamat pengiriman & lokasi pengiriman.</p>
  
  @if(session('success'))
    <div class="mt-3 mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
  @endif

  {{-- FORM PREFERENSI PEMBELI --}}
  <form method="POST" action="{{ route('pembeli.preferensi') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
    @csrf

    {{-- Nama & Telepon --}}
    <div>
      <label class="block text-sm text-gray-700">Nama Penerima</label>
      <input
        name="receiver_name"
        value="{{ old('receiver_name', $pembeli->nama_pembeli ?? '') }}"
        class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
        placeholder="Nama penerima">
    </div>

    <div>
      <label class="block text-sm text-gray-700">No. Telepon</label>
      <input
        name="phone"
        value="{{ old('phone', $pembeli->no_telp ?? '') }}"
        class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
        placeholder="08xxxxxxxxxx">
    </div>

    {{-- Alamat (user ketik, map akan mengikuti) --}}
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

    {{-- Peta Leaflet --}}
    <div class="sm:col-span-2">
      <label class="block text-sm text-gray-700 mb-1">Lokasi di Peta</label>
      <div id="map" class="w-full h-56 sm:h-64 rounded-md border"></div>
      <p class="mt-1 text-xs text-gray-500">
        Kamu juga bisa klik di peta untuk menggeser titik lokasi secara manual.
      </p>
    </div>

    {{-- Latitude & Longitude (otomatis) --}}
    <div>
      <label class="block text-sm text-gray-700">Latitude</label>
      <input
        id="latitude"
        name="latitude"
        value="{{ old('latitude', $pembeli->latitude ?? '') }}"
        class="mt-1 w-full rounded-md border px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-green-600"
        readonly>
    </div>

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
  </form>
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

    // Posisi awal map
    let initialLat = -2.5;
    let initialLng = 118.0;
    let initialZoom = 5;

    @if(isset($pembeli) && $pembeli->latitude && $pembeli->longitude)
        initialLat = {{ $pembeli->latitude }};
        initialLng = {{ $pembeli->longitude }};
        initialZoom = 16;
    @endif

    const map = L.map('map').setView([initialLat, initialLng], initialZoom);

    // Layer dasar OSM
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;
    let geocodeTimeout = null;

    // Kalau sudah ada koordinat di DB, tampilkan marker
    @if(isset($pembeli) && $pembeli->latitude && $pembeli->longitude)
        marker = L.marker([{{ $pembeli->latitude }}, {{ $pembeli->longitude }}]).addTo(map);
    @endif

    // Fungsi: alamat -> koordinat (forward geocoding)
    async function geocodeAddress(query) {
      if (!query || query.length < 5) {
        statusEl.textContent = '';
        return;
      }

      try {
        statusEl.textContent = 'Mencari lokasi dari alamat...';

        const url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q='
          + encodeURIComponent(query);

        const res = await fetch(url, { headers: { 'Accept-Language': 'id' } });

        if (!res.ok) throw new Error('Gagal ambil geocoding');

        const results = await res.json();
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
        console.error(err);
        statusEl.textContent = 'Terjadi kesalahan saat mencari alamat.';
      }
    }

    // Saat user mengetik alamat → debounce → geocode
    addressInput.addEventListener('input', function () {
      const query = this.value.trim();
      clearTimeout(geocodeTimeout);

      if (!query || query.length < 5) {
        statusEl.textContent = '';
        return;
      }

      geocodeTimeout = setTimeout(function () {
        geocodeAddress(query);
      }, 700);
    });

    // Klik map → geser titik & update lat/lng
    map.on('click', function (e) {
      const lat = e.latlng.lat;
      const lon = e.latlng.lng;

      latInput.value = lat.toFixed(6);
      lngInput.value = lon.toFixed(6);

      if (marker) marker.setLatLng(e.latlng);
      else marker = L.marker(e.latlng).addTo(map);

      map.setView(e.latlng, 16);
      statusEl.textContent = 'Titik lokasi di peta diperbarui secara manual.';
    });
  });
</script>
