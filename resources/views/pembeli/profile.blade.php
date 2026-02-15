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

    {{-- validasi input --}}
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
                       value="{{ old('email', $user->email) }}"
                       readonly
                       class="mt-1 w-full border rounded-md px-3 py-2 text-sm bg-gray-100">
            </div>

            {{-- Nama User --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Nama User</label>
                <input name="name"
                       value="{{ old('name', $user->name) }}"
                       class="mt-1 w-full border rounded-md px-3 py-2 text-sm">
            </div>

            {{-- Nama Penerima --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Nama Penerima</label>
                <input name="receiver_name"
                       value="{{ old('receiver_name', $pembeli->nama_pembeli ?? '') }}"
                       class="mt-1 w-full border rounded-md px-3 py-2 text-sm">
                <p class="text-xs text-gray-600 mt-0.5">
                    Digunakan pada label pengiriman
                </p>
            </div>

            {{-- No Telepon --}}
            <div>
                <label class="text-sm font-medium text-gray-700">No. Telepon</label>
                <input type="text"
                       name="phone"
                       inputmode="numeric"
                       maxlength="13"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                       value="{{ old('phone', $pembeli->no_telp ?? '') }}"
                       class="mt-1 w-full border rounded-md px-3 py-2 text-sm">
                <p class="text-xs text-gray-600 mt-0.5">
                    Aktif & dapat dihubungi kurir
                </p>
            </div>

            <div class="sm:col-span-2">
                <button type="submit"
                        class="bg-green-700 text-white px-4 py-2 rounded-md text-sm hover:bg-green-800">
                    Simpan Informasi Akun
                </button>
            </div>
        </form>
    </div>

    {{-- RIWAYAT PESANAN (TETAP ADA, TIDAK DIHAPUS) --}}
    <div class="bg-white border shadow-sm rounded-xl p-6">
        <div class="px-6 py-3 border-b bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Riwayat Pesanan</h2>
            <p class="text-sm text-gray-500">
                Daftar semua transaksi yang pernah kamu lakukan
            </p>
        </div>

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

    {{-- ALAMAT PENGIRIMAN (INI YANG DIPERBAIKI) --}}
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
                       class="mt-1 w-full border rounded-md px-3 py-2 text-sm"
                       placeholder="Masukkan alamat lengkap">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">Latitude</label>
                    <input id="latitude"
                           name="latitude"
                           value="{{ old('latitude', $pembeli->latitude ?? '') }}"
                           readonly
                           class="mt-1 w-full bg-gray-50 border rounded-md px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Longitude</label>
                    <input id="longitude"
                           name="longitude"
                           value="{{ old('longitude', $pembeli->longitude ?? '') }}"
                           readonly
                           class="mt-1 w-full bg-gray-50 border rounded-md px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <div id="map" class="w-full h-72 rounded-md border"></div>
                <p id="location-status" class="text-xs text-gray-500 mt-1"></p>
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

        @elseif($user->seller_status === 'approved')
            <p class="text-sm text-green-700">
                Akun kamu sudah aktif sebagai penjual.
            </p>

        @else
            <p class="text-sm text-gray-700">
                Saat ini kamu masih terdaftar sebagai pembeli.
            </p>

            <a href="{{ route('penjual.daftar.submit') }}"
            class="inline-block mt-2 bg-green-700 text-white px-4 py-2 rounded-md text-sm hover:bg-green-800">
                Daftar sebagai Penjual
            </a>
        @endif
    </div>


</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    //ambil input lat and long
    const latInput     = document.getElementById('latitude');
    const lngInput     = document.getElementById('longitude');
    const alamatInput  = document.getElementById('alamat');
    const statusEl     = document.getElementById('location-status');
    
    //set nilai awal lat and long
    let lat  = latInput.value ? parseFloat(latInput.value) : -6.200000;
    let lng  = lngInput.value ? parseFloat(lngInput.value) : 106.816666;
    let zoom = latInput.value ? 16 : 6;

    const map = L.map('map').setView([lat, lng], zoom); //inisialisasi map

    //peta berisi gambar bangunan dll
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = L.marker([lat, lng], { draggable: true }).addTo(map); //geser pin

    /* ========= MAP → ALAMAT ========= */
    async function reverseGeocode(lat, lng) {
        statusEl.textContent = 'Mengambil alamat dari peta...';

        const res = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`,
            { headers: { 'Accept-Language': 'id' } }
        );

        const data = await res.json();
        if (data.display_name) {
            alamatInput.value = data.display_name;
            statusEl.textContent = 'Alamat diperbarui dari peta';
        }
    }

    //memperbarui map ketika digeser
    marker.on('dragend', function () {
        const pos = marker.getLatLng();
        latInput.value = pos.lat.toFixed(6);
        lngInput.value = pos.lng.toFixed(6);
        reverseGeocode(pos.lat, pos.lng);
    });

    //update map ketika diklik
    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        latInput.value = e.latlng.lat.toFixed(6);
        lngInput.value = e.latlng.lng.toFixed(6);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });

    /* ========= ALAMAT → MAP ========= */
    let typingTimer;

    //memproses map setelah mengetik
    alamatInput.addEventListener('input', function () {
        clearTimeout(typingTimer);

        const query = this.value.trim();
        if (query.length < 5) return;

        typingTimer = setTimeout(async () => {
            statusEl.textContent = 'Mencari lokasi dari alamat...';

            const res = await fetch(
                `https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=${encodeURIComponent(query)}`,
                { headers: { 'Accept-Language': 'id' } }
            );

            const data = await res.json();
            if (!data.length) {
                statusEl.textContent = 'Alamat tidak ditemukan';
                return;
            }

            const lat = parseFloat(data[0].lat);
            const lng = parseFloat(data[0].lon);

            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);

            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 16);

            statusEl.textContent = 'Lokasi ditemukan dari alamat';
        }, 700);
    });

});
</script>

@endpush