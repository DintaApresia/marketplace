@extends('layouts.penjual')
@section('content')
<div class="bg-white rounded-lg shadow border border-gray-100 p-6">

  @if(session('success'))
  <div class="mb-4 rounded-md bg-green-100 border border-green-300 p-3 text-green-800 text-sm">
    {{ session('success') }}
  </div>
  @endif
  <h2 class="text-2xl font-bold text-green-800">Profil Penjual</h2>
  <p class="text-sm text-gray-600 mb-4">
    Halaman ini digunakan untuk mengelola profil penjual dan informasi toko yang akan ditampilkan kepada pembeli,
    termasuk data rekening, kontak, serta lokasi pickup pesanan.
  </p>

  {{-- Pengaturan toko --}}
  <form method="POST"
      action="{{ route('penjual.profile.update') }}"
      enctype="multipart/form-data">

    @csrf
    @method('PATCH')

    {{-- Baris 1–2: data dasar toko --}}
    <div class="grid gap-6 sm:grid-cols-2">
      {{-- Nama --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Nama Penjual</label>
        <input
        type="text"
        name="nama_penjual"
        value="{{ old('name', $penjual->nama_penjual) }}"
        required
        maxlength="255"
        class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
        placeholder="Nama user"
        >
        @error('name')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
      </div>

      {{-- Email --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input
        type="email"
        name="email"
        value="{{ old('email', $user->email) }}"
        required
        maxlength="255"
        readonly
        class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
        placeholder="email@contoh.com"
        >
        @error('email')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Nama Toko</label>
        <input
          name="nama_toko"
          class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
          placeholder="SecondLife Store"
          value="{{ old('nama_toko', $penjual->nama_toko ?? '') }}"
        >
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">No. Telepon</label>
        <input
          name="no_telp"
          class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
          placeholder="08xxxxxxxxxx"
          value="{{ old('no_telp', $penjual->no_telp ?? '') }}"
        >
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Nama Rekening</label>
        <input
          name="nama_rekening"
          class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
          placeholder="Nama pemilik rekening"
          value="{{ old('nama_rekening', $penjual->nama_rekening ?? '') }}"
        >
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">No. Rekening</label>
        <input
          name="rekening"
          class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
          placeholder="1234567890"
          value="{{ old('no_rekening', $penjual->rekening ?? '') }}"
        >
      </div>

      <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-700">Kartu Identitas (KTP/SIM/Kartu lain)</label>

        @if(auth()->user()->role === 'admin')
            <a href="{{ asset('storage/'.$penjual->kartu_identitas) }}" target="_blank">Lihat KTP</a>
        @else
            <p class="text-xs text-gray-500">Kartu identitas tersimpan.</p>
        @endif
      </div>

    </div>

    {{-- Alamat + koordinat --}}
    <div class="space-y-4 mt-6">
      {{-- Alamat Toko --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Alamat Toko</label>
        <input
          id="alamat_toko"
          name="alamat_toko"
          class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
          placeholder="Contoh: Jl. Melati No. 8, Surabaya"
          value="{{ old('alamat_toko', $penjual->alamat_toko ?? '') }}"
        >
        <p id="status-lokasi-toko" class="text-xs text-gray-500 mt-1">
          Peta akan mengikuti alamat atau titik yang kamu pilih.
        </p>
      </div>

      {{-- Latitude & Longitude --}}
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Latitude</label>
          <input
            id="lat_toko"
            name="latitude"
            value="{{ old('latitude', $penjual->latitude ?? '') }}"
            class="mt-1 w-full rounded-md border px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-green-600"
            readonly
          >
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Longitude</label>
          <input
            id="lng_toko"
            name="longitude"
            value="{{ old('longitude', $penjual->longitude ?? '') }}"
            class="mt-1 w-full rounded-md border px-3 py-2 bg-gray-50 focus:ring-2 focus:ring-green-600"
            readonly
          >
        </div>
      </div>

      {{-- Peta Lokasi Toko --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Toko di Peta</label>
        <div id="map-toko" class="w-full h-56 sm:h-64 rounded-md border"></div>
        <p class="mt-1 text-xs text-gray-500">
          Klik pada peta atau geser pin untuk mengatur lokasi toko. Latitude & longitude akan terisi otomatis.
        </p>
      </div>
    </div>

    {{-- Tombol Simpan --}}
    <div class="flex items-center justify-end">
      <button class="rounded-md bg-green-700 text-white px-4 py-2 hover:bg-green-800">
        Simpan Profile
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
    if (typeof L === 'undefined') {
        console.error('Leaflet belum ke-load');
        return;
    }

    const latInput  = document.getElementById('lat_toko');
    const lngInput  = document.getElementById('lng_toko');
    const addrInput = document.getElementById('alamat_toko');
    const statusEl  = document.getElementById('status-lokasi-toko');

    // Default Indonesia
    let initialLat  = -2.5;
    let initialLng  = 118.0;
    let initialZoom = 5;

    @if(isset($penjual) && $penjual->latitude && $penjual->longitude)
        initialLat  = {{ $penjual->latitude }};
        initialLng  = {{ $penjual->longitude }};
        initialZoom = 16;
    @endif

    const map = L.map('map-toko').setView([initialLat, initialLng], initialZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // === Marker DRAGGABLE ===
    let marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

    function setLatLngInputs(lat, lng) {
        latInput.value = lat.toFixed(6);
        lngInput.value = lng.toFixed(6);
    }

    // ================================
    // ⭐ REVERSE GEOCODE (lat,lng → alamat)
    // ================================
    async function reverseGeocode(lat, lng) {
        statusEl.textContent = "Mengambil alamat dari titik...";

        try {
            const url =
                "https://nominatim.openstreetmap.org/reverse?format=jsonv2"
                + "&lat=" + lat
                + "&lon=" + lng;

            const res = await fetch(url);
            const data = await res.json();

            if (data && data.display_name) {
                addrInput.value = data.display_name;
                statusEl.textContent = "Alamat diperbarui.";
            } else {
                statusEl.textContent = "Alamat tidak ditemukan.";
            }
        } catch (err) {
            statusEl.textContent = "Gagal mengambil alamat.";
        }
    }

    // Saat marker selesai di-drag
    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        setLatLngInputs(pos.lat, pos.lng);
        reverseGeocode(pos.lat, pos.lng);
    });

    // Klik peta → pindahkan marker + reverse geocode
    map.on('click', function (e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        marker.setLatLng(e.latlng);
        setLatLngInputs(lat, lng);
        reverseGeocode(lat, lng);
    });

    // ================================
    // ⭐ FORWARD GEOCODE (alamat → lat,lng)
    // ================================
    let geocodeTimeout = null;

    async function geocodeAddress(query) {
        if (!query || query.length < 5) return;

        statusEl.textContent = "Mencari lokasi dari alamat...";

        try {
            const url =
                "https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q="
                + encodeURIComponent(query);

            const res = await fetch(url);
            const results = await res.json();

            if (!results.length) {
                statusEl.textContent = "Alamat tidak ditemukan.";
                return;
            }

            const place = results[0];
            const lat   = parseFloat(place.lat);
            const lng   = parseFloat(place.lon);

            setLatLngInputs(lat, lng);
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 16);

            statusEl.textContent = "Lokasi toko ditemukan dari alamat.";
        } catch (err) {
            statusEl.textContent = "Gagal memproses alamat.";
        }
    }

    addrInput.addEventListener('input', function () {
        const query = this.value.trim();
        clearTimeout(geocodeTimeout);
        geocodeTimeout = setTimeout(() => geocodeAddress(query), 600);
    });

    // Set awal input lat/lng
    setLatLngInputs(initialLat, initialLng);
});
</script>

<style>
  #map-toko {
      height: 300px !important;
      width: 100% !important;
      border-radius: 8px;
      border: 1px solid #ddd;
  }
</style>
@endsection