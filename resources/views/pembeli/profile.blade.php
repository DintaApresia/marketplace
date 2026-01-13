@extends('layouts.pembeli')

@section('title','Profil — SecondLife')

@section('content')
<div class="p-6 max-w-3xl mx-auto space-y-10">

    {{-- ALERT SUCCESS / ERROR --}}
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <div class="font-semibold mb-1">Periksa input kamu:</div>
            <ul class="list-disc pl-5 text-sm space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                    value="{{ old('email', $user->email) }}"
                    readonly
                    class="mt-1 w-full border rounded-md px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600 @error('email') border-red-500 @enderror"
                    placeholder="email@contoh.com">

                @error('email')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Nama Penerima</label>
                <input name="receiver_name"
                    value="{{ old('receiver_name', $pembeli->nama_pembeli ?? '') }}"
                    class="mt-1 w-full border rounded-md px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600 @error('receiver_name') border-red-500 @enderror"
                    placeholder="Nama penerima">

                @error('receiver_name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- No. Telepon --}}
            <div>
                <label class="text-sm font-medium text-gray-700">No. Telepon</label>
                <input name="phone"
                    value="{{ old('phone', $pembeli->no_telp ?? '') }}"
                    class="mt-1 w-full border rounded-md px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600 @error('phone') border-red-500 @enderror"
                    placeholder="08xxxxxxxxxx">

                @error('phone')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- {{-- Password --}}
            <div class="relative">
                <label class="text-sm font-medium text-gray-700">Password Baru</label>

                <input type="password"
                    id="password"
                    name="password"
                    class="mt-1 w-full border rounded-md px-3 py-2 pr-10 text-sm focus:ring-green-600 focus:border-green-600 @error('password') border-red-500 @enderror"
                    placeholder="Kosongkan jika tidak ingin ubah">

                {{-- Eye Icon --}}
                <button type="button"
                        onclick="togglePassword('password', this)"
                        class="absolute right-3 top-[38px] text-gray-500 hover:text-gray-700">

                    {{-- eye --}}
                    <svg class="w-5 h-5 eye-open" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5
                                c4.478 0 8.268 2.943 9.542 7
                                -1.274 4.057-5.064 7-9.542 7
                                -4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>

                    {{-- eye-off --}}
                    <svg class="w-5 h-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19
                                c-4.478 0-8.268-2.943-9.542-7
                                a9.956 9.956 0 012.223-3.592"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6.223 6.223A9.956 9.956 0 0112 5
                                c4.478 0 8.268 2.943 9.542 7
                                a9.978 9.978 0 01-4.132 5.411"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3l18 18"/>
                    </svg>
                </button>

                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div> -->

            <!-- {{-- Konfirmasi Password --}}
            <div class="relative">
                <label class="text-sm font-medium text-gray-700">Konfirmasi Password</label>

                <input type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="mt-1 w-full border rounded-md px-3 py-2 pr-10 text-sm focus:ring-green-600 focus:border-green-600"
                    placeholder="Ulangi password baru">

                <button type="button"
                        onclick="togglePassword('password_confirmation', this)"
                        class="absolute right-3 top-[38px] text-gray-500 hover:text-gray-700">

                    {{-- eye --}}
                    <svg class="w-5 h-5 eye-open" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5
                                c4.478 0 8.268 2.943 9.542 7
                                -1.274 4.057-5.064 7-9.542 7
                                -4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>

                    {{-- eye-off --}}
                    <svg class="w-5 h-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3l18 18"/>
                    </svg>
                </button>
            </div> -->

            {{-- Tombol --}}
            <div class="sm:col-span-2">
                <button type="submit"
                        class="bg-green-700 text-white px-4 py-2 rounded-md text-sm hover:bg-green-800">
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

        <a href="{{ route('pembeli.orders.index') }}"
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

        <form method="POST" action="{{ route('pembeli.alamat') }}" class="space-y-4">
            @csrf

            {{-- Alamat --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Alamat Lengkap</label>
                <input id="alamat"
                       name="alamat"
                       value="{{ old('alamat', $pembeli->alamat ?? '') }}"
                       class="mt-1 w-full border rounded-md px-3 py-2 text-sm focus:ring-green-600 focus:border-green-600"
                       placeholder="Masukan alamat, misal: Jalan Soekarno Hatta No. 10">
                <p id="location-status" class="text-xs text-gray-500 mt-1">Map akan menyesuaikan otomatis.</p>
            </div>

            {{-- Koordinat --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Latitude</label>
                    <input id="latitude"
                           name="latitude"
                           value="{{ old('latitude', $pembeli->latitude ?? '') }}"
                           class="mt-1 w-full bg-gray-50 border rounded-md px-3 py-2 text-sm"
                           readonly>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Longitude</label>
                    <input id="longitude"
                           name="longitude"
                           value="{{ old('longitude', $pembeli->longitude ?? '') }}"
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
            <button type="submit"
                    class="bg-green-700 text-white px-4 py-2 rounded-md text-sm hover:bg-green-800">
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

<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const openIcon = btn.querySelector('.eye-open');
    const closedIcon = btn.querySelector('.eye-closed');

    if (input.type === 'password') {
        input.type = 'text';
        openIcon.classList.add('hidden');
        closedIcon.classList.remove('hidden');
    } else {
        input.type = 'password';
        openIcon.classList.remove('hidden');
        closedIcon.classList.add('hidden');
    }
}
</script>


@endpush
