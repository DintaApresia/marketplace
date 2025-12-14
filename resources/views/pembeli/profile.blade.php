@extends('layouts.pembeli')
@section('title','Profil — SecondLife')
@section('content')
<div class="p-6 max-w-3xl mx-auto space-y-10">

    {{-- =======================================
            INFORMASI AKUN
    ======================================== --}}
    <div class="bg-white border shadow-sm rounded-xl p-6 space-y-4">
        <h2 class="text-lg font-semibold text-gray-800">Informasi Akun</h2>
        <p class="text-sm text-gray-600">Perbarui informasi dasar akunmu.</p>

        <form method="POST" action="{{ route('pembeli.preferensi') }}" class="grid sm:grid-cols-2 gap-4">
            @csrf

            {{-- Email --}}
            <div class="sm:col-span-2">
                <label class="text-sm font-medium text-gray-700">Email</label>
                <input type="email"
                       name="email"
                       value="{{ $user->email }}"
                       class="mt-1 w-full border rounded-md px-3 py-2 text-sm focus:ring-green-600">
            </div>

            {{-- Nama --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Nama Penerima</label>
                <input name="receiver_name"
                       value="{{ $pembeli->nama_pembeli ?? '' }}"
                       class="mt-1 w-full border rounded-md px-3 py-2 text-sm focus:ring-green-600"
                       placeholder="Nama penerima">
            </div>

            {{-- No. Telepon --}}
            <div>
                <label class="text-sm font-medium text-gray-700">No. Telepon</label>
                <input name="phone"
                       value="{{ $pembeli->no_telp ?? '' }}"
                       class="mt-1 w-full border rounded-md px-3 py-2 text-sm focus:ring-green-600"
                       placeholder="08xxxxxxxxxx">
            </div>

            {{-- Tombol --}}
            <div class="sm:col-span-2">
                <button class="bg-green-700 text-white px-4 py-2 rounded-md text-sm hover:bg-green-800">
                    Simpan Informasi Akun
                </button>
            </div>
        </form>
    </div>

    {{-- =======================================
        RIWAYAT PESANAN (SHORTCUT BUTTON)
    ======================================== --}}
    <div class="bg-white border shadow-sm rounded-xl p-6">
        <h2 class="text-lg font-semibold text-gray-800">Riwayat Pesanan</h2>
        <p class="text-sm text-gray-600 mt-1">
            Lihat semua pesanan yang pernah kamu buat.
        </p>

        <a href="{{ route('pembeli.orders') }}"
          class="inline-flex items-center gap-2 mt-4 bg-green-700 text-white px-4 py-2 rounded-md text-sm hover:bg-green-800 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.293 2.293A1 1 0 0 0 6.618 17h10.764a1 1 0 0 0 .911-1.447L17 13M7 13h10M9 21h.01M15 21h.01" />
            </svg>
            Lihat Pesanan Saya
        </a>
    </div>

    {{-- =======================================
            ALAMAT PENGIRIMAN
    ======================================== --}}
    <div class="bg-white border shadow-sm rounded-xl p-6 space-y-4">
        <h2 class="text-lg font-semibold text-gray-800">Alamat Pengiriman</h2>
        <p class="text-sm text-gray-600">Atur alamat lengkap untuk pengiriman pesanan.</p>

        <form method="POST" action="{{ route('pembeli.preferensi') }}" class="space-y-4">
            @csrf

            {{-- Alamat --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Alamat Lengkap</label>
                <input id="address_line"
                       name="address_line"
                       value="{{ $pembeli->alamat ?? '' }}"
                       class="mt-1 w-full border rounded-md px-3 py-2 text-sm focus:ring-green-600"
                       placeholder="Masukan alamat, misal: Jalan Soekarno Hatta No. 10">
                <p id="location-status" class="text-xs text-gray-500 mt-1">Map akan menyesuaikan otomatis.</p>
            </div>

            {{-- Koordinat --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Latitude</label>
                    <input id="latitude"
                           name="latitude"
                           value="{{ $pembeli->latitude ?? '' }}"
                           class="mt-1 w-full bg-gray-50 border rounded-md px-3 py-2 text-sm"
                           readonly>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Longitude</label>
                    <input id="longitude"
                           name="longitude"
                           value="{{ $pembeli->longitude ?? '' }}"
                           class="mt-1 w-full bg-gray-50 border rounded-md px-3 py-2 text-sm"
                           readonly>
                </div>
            </div>

            {{-- MAP --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Lokasi pada Map</label>
                <div id="map" class="w-full h-48 rounded-md border mt-1"></div>
            </div>

            {{-- Tombol --}}
            <button class="bg-green-700 text-white px-4 py-2 rounded-md text-sm hover:bg-green-800">
                Simpan Alamat
            </button>
        </form>
    </div>


    {{-- =======================================
            STATUS PENJUAL
    ======================================== --}}
    <div class="bg-white border shadow-sm rounded-xl p-6 space-y-3">
        <h2 class="text-lg font-semibold text-gray-800">Status Akun Penjual</h2>

        @if($user->seller_status === 'pending')
            <p class="text-sm text-yellow-800">
                Pengajuan sebagai penjual sedang <b>ditinjau</b> oleh admin.
            </p>
            <a href="{{ route('penjual.pengajuan-saya') }}"
                class="inline-block bg-green-700 text-white px-3 py-1.5 rounded-md text-xs hover:bg-green-800">
                Lihat Pengajuan
            </a>

        @elseif($user->seller_status === 'rejected')
            <p class="text-sm text-red-700">
                Pengajuan sebelumnya <b>ditolak</b>. Silakan perbaiki data dan ajukan ulang.
            </p>
            <a href="{{ route('penjual.pengajuan-saya') }}"
                class="inline-block bg-green-700 text-white px-3 py-1.5 rounded-md text-xs hover:bg-green-800">
                Detail Penolakan
            </a>

        @else
            <p class="text-sm text-gray-700">
                Saat ini kamu masih terdaftar sebagai <b>pembeli</b>.
            </p>
            <a href="{{ route('penjual.daftar') }}"
               class="inline-block bg-green-700 text-white px-3 py-1.5 rounded-md text-xs hover:bg-green-800">
                Daftar jadi Penjual
            </a>
        @endif
    </div>

</div>
@endsection

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