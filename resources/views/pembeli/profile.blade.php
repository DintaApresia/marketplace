@extends('layouts.pembeli')

@section('title','Profil — SecondLife')

@section('content')
<div class="p-6 max-w-3xl mx-auto space-y-4">

    {{-- ALERT SUCCESS / ERROR --}}
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-2 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-2 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-2 rounded-lg">
            <div class="font-semibold mb-1">Periksa input kamu:</div>
            <ul class="list-disc pl-5 text-sm space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- INFORMASI AKUN --}}
    <div class="bg-white border shadow-sm rounded-xl p-6 space-y-2">
        <div class="px-6 py-3 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Informasi Akun</h2>
            <p class="text-sm text-gray-500">
                Data ini digunakan untuk identitas akun dan komunikasi
            </p>
        </div>

        <form method="POST" action="{{ route('pembeli.preferensi') }}" class="grid sm:grid-cols-2 gap-4">
            @csrf

            {{-- Email --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Email</label>
                <input type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    readonly
                    class="mt-1 w-full border rounded-md px-3 py-2 text-sm
                        bg-gray-100 cursor-not-allowed
                        focus:ring-green-600 focus:border-green-600">
                <p class="text-xs text-gray-500">
                    Data ini digunakan untuk identitas akun dan komunikasi
                </p>
            </div>

            {{-- Nama User --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Nama User</label>
                <input name="name"
                    value="{{ old('name', $user->name) }}"
                    class="mt-1 w-full border rounded-md px-3 py-2 text-sm
                        focus:ring-green-600 focus:border-green-600"
                    placeholder="Nama user">
            </div>

            {{-- Nama Penerima --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Nama Penerima</label>
                <input name="receiver_name"
                    value="{{ old('receiver_name', $pembeli->nama_pembeli ?? '') }}"
                    class="mt-1 w-full border rounded-md px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600"
                    placeholder="Nama penerima">

                <p class="text-xs text-gray-600 mt-0.5">
                    Digunakan pada label pengiriman
                </p>
            </div>

            {{-- No. Telepon --}}
            <div>
                <label class="text-sm font-medium text-gray-700">No. Telepon</label>
                <input name="phone"
                    value="{{ old('phone', $pembeli->no_telp ?? '') }}"
                    class="mt-1 w-full border rounded-md px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600"
                    placeholder="08xxxxxxxxxx">
                <p class="text-xs text-gray-600 mt-0.5">
                    Aktif & dapat dihubungi kurir
                </p>
            </div>

            {{-- Tombol --}}
            <div class="sm:col-span-2">
                <button type="submit"
                        class="bg-green-700 text-white px-4 py-2 rounded-md text-sm hover:bg-green-800">
                    Simpan Informasi Akun
                </button>
            </div>
        </form>
    </div>

    {{-- RIWAYAT PESANAN --}}
    <div class="bg-white border shadow-sm rounded-xl p-6">
        <div class="px-6 py-3 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Riwayat Pesanan</h2>
            <p class="text-sm text-gray-500">
                Daftar semua transaksi yang pernah kamu lakukan
            </p>
        </div>

        {{-- BODY --}}
        <div class="p-6 space-y-3">
            <p class="text-sm text-gray-600">
                Cek status dan detail pesanan.
            </p>

            <a href="{{ route('pembeli.orders.index') }}"
            class="inline-flex items-center gap-2 bg-green-700 text-white
                    px-4 py-2 rounded-md text-sm hover:bg-green-800 transition w-fit">
                Lihat Pesanan Saya
            </a>
        </div>
    </div>

    {{-- ALAMAT PENGIRIMAN --}}
    <div class="bg-white border shadow-sm rounded-xl p-6 space-y-4">
        <div class="px-6 py-3 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Alamat Pengiriman</h2>
            <p class="text-xs text-gray-500">
                Digunakan untuk perhitungan ongkir dan pengiriman
            </p>
        </div>

        <form method="POST" action="{{ route('pembeli.alamat') }}" class="space-y-4">
            @csrf

            <div>
                <label class="text-sm font-medium text-gray-700">Alamat Lengkap</label>
                <input id="alamat"
                       name="alamat"
                       value="{{ old('alamat', $pembeli->alamat ?? '') }}"
                       class="mt-1 w-full border rounded-md px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600"
                       placeholder="Masukan alamat">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Latitude</label>
                    <input id="latitude" name="latitude"
                           value="{{ old('latitude', $pembeli->latitude ?? '') }}"
                           class="mt-1 w-full bg-gray-50 border rounded-md px-3 py-2 text-sm" readonly>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Longitude</label>
                    <input id="longitude" name="longitude"
                           value="{{ old('longitude', $pembeli->longitude ?? '') }}"
                           class="mt-1 w-full bg-gray-50 border rounded-md px-3 py-2 text-sm" readonly>
                </div>
            </div>

            <div>
                <div id="map" class="w-full h-48 rounded-md border"></div>
            </div>

            <button type="submit"
                    class="bg-green-700 text-white px-4 py-2 rounded-md text-sm hover:bg-green-800">
                Simpan Alamat
            </button>
        </form>
    </div>

    {{-- STATUS PENJUAL --}}
    <div class="bg-white border shadow-sm rounded-xl p-6 space-y-3">
        <h2 class="text-lg font-semibold text-gray-800">Status Akun Penjual</h2>

        @if($user->seller_status === 'pending')
            <p class="text-sm text-yellow-800">
                Pengajuan sebagai penjual sedang ditinjau admin.
            </p>
        @elseif($user->seller_status === 'rejected')
            <p class="text-sm text-red-700">
                Pengajuan sebelumnya ditolak.
            </p>
            <a href="{{ route('penjual.pengajuan-saya') }}"
                class="inline-block bg-green-700 text-white px-3 py-1.5 rounded-md text-xs">
                Detail Penolakan
            </a>
        @else
            <p class="text-sm text-gray-700">
                Saat ini kamu masih terdaftar sebagai pembeli.
            </p>
        @endif
    </div>

</div>
@endsection


@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  #map {
      height: 300px !important;
      width: 100% !important;
      border-radius: 8px;
      border: 1px solid #ddd;
  }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const latInput      = document.getElementById('latitude');
    const lngInput      = document.getElementById('longitude');
    const addressInput  = document.getElementById('alamat');
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

    @if(isset($pembeli) && $pembeli->latitude && $pembeli->longitude)
        marker = L.marker([{{ $pembeli->latitude }}, {{ $pembeli->longitude }}]).addTo(map);
    @endif

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
          statusEl.textContent = 'Tidak bisa mengambil alamat (HTTP ' + res.status + ').';
          return;
        }

        const data = await res.json();

        if (data && data.display_name) {
          addressInput.value = data.display_name;
          statusEl.textContent = 'Alamat berhasil diperbarui dari titik peta.';
        } else {
          statusEl.textContent = 'Alamat tidak ditemukan untuk titik ini.';
        }
      } catch (err) {
        console.error(err);
        statusEl.textContent = 'Gagal mengambil alamat dari titik peta.';
      }
    }

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
          statusEl.textContent = 'Gagal mencari alamat (HTTP ' + res.status + ').';
          return;
        }

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

    addressInput.addEventListener('input', function () {
      const query = this.value.trim();
      clearTimeout(geocodeTimeout);
      geocodeTimeout = setTimeout(() => geocodeAddress(query), 700);
    });

    map.on('click', function (e) {
      const lat = e.latlng.lat;
      const lon = e.latlng.lng;

      latInput.value = lat.toFixed(6);
      lngInput.value = lon.toFixed(6);

      if (marker) marker.setLatLng(e.latlng);
      else marker = L.marker(e.latlng).addTo(map);

      map.setView(e.latlng, 16);

      addressInput.value = 'Lat: ' + lat.toFixed(6) + ', Lng: ' + lon.toFixed(6);
      statusEl.textContent = 'Titik peta diperbarui, mengambil alamat...';

      reverseGeocode(lat, lon);
    });

});
</script>

@endpush
