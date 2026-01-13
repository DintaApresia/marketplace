@extends('layouts.penjual')

@section('title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

    {{-- STATUS TOKO (INFO SAJA) --}}
    <div class="p-5 bg-white shadow rounded border">
        <h3 class="text-sm font-semibold text-gray-600">Status Toko</h3>
        <p class="text-xl font-bold mt-2 text-green-700">Aktif</p>
        <p class="text-xs text-gray-500 mt-2">Toko Anda sudah bisa menerima pesanan.</p>
    </div>

    {{-- TOTAL PRODUK (DONUT) --}}
    <div class="p-5 bg-white shadow rounded border">
        <h3 class="text-sm font-semibold text-gray-600">Total Produk</h3>

        <div class="mt-3 flex items-center gap-4">
            <div class="w-24 h-24">
                <canvas id="produkChart"
                    data-aktif="{{ $produkAktif }}"
                    data-nonaktif="{{ $produkNonaktif }}"></canvas>
            </div>

            <div>
                <p class="text-3xl font-bold text-blue-600">{{ $totalProduk }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    Aktif: <span class="font-semibold">{{ $produkAktif }}</span> |
                    Nonaktif: <span class="font-semibold">{{ $produkNonaktif }}</span>
                </p>
            </div>
        </div>

        @if ($totalProduk == 0)
            <p class="text-xs text-gray-500 mt-3">
                Belum ada produk. Tambahkan produk pertama Anda.
            </p>
        @endif
    </div>

    {{-- STATUS PESANAN (BAR) --}}
    <div class="p-5 bg-white shadow rounded border">
        <h3 class="text-sm font-semibold text-gray-600">Status Pesanan</h3>

        <p class="text-3xl font-bold mt-2 {{ $pesananMasuk > 0 ? 'text-yellow-600' : 'text-gray-400' }}">
            {{ $pesananMasuk }}
        </p>

        <div class="mt-3">
            <canvas id="pesananStatusChart"
                data-dikemas="{{ $pesananDikemas }}"
                data-dikirim="{{ $pesananDikirim }}"
                data-selesai="{{ $pesananSelesai }}"
                data-ditolak="{{ $pesananDitolak }}"
                height="90"></canvas>
        </div>

        <p class="text-xs mt-2 text-gray-500">
            Dikemas: <span class="font-semibold">{{ $pesananDikemas }}</span> |
            Dikirim: <span class="font-semibold">{{ $pesananDikirim }}</span> |
            Selesai: <span class="font-semibold">{{ $pesananSelesai }}</span> |
            Ditolak: <span class="font-semibold">{{ $pesananDitolak }}</span>
        </p>
    </div>

</div>

{{-- BAGIAN BAWAH --}}
<div class="mt-6 p-6 bg-white border rounded shadow">
    <h3 class="text-lg font-semibold text-gray-800">Informasi Toko</h3>
    <p class="text-sm text-gray-600 mt-2">
        Kelola produk, pesanan, dan informasi toko Anda melalui menu di sebelah kiri.
    </p>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Produk donut ---
    var produkEl = document.getElementById('produkChart');
    if (produkEl) {
        var aktif = parseInt(produkEl.dataset.aktif || '0', 10);
        var nonaktif = parseInt(produkEl.dataset.nonaktif || '0', 10);

        new Chart(produkEl, {
            type: 'doughnut',
            data: {
                labels: ['Aktif', 'Nonaktif'],
                datasets: [{
                    data: [aktif, nonaktif]
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                cutout: '70%'
            }
        });
    }

    // --- Pesanan bar ---
    var pesananEl = document.getElementById('pesananStatusChart');
    if (pesananEl) {
        var dikemas = parseInt(pesananEl.dataset.dikemas || '0', 10);
        var dikirim = parseInt(pesananEl.dataset.dikirim || '0', 10);
        var selesai = parseInt(pesananEl.dataset.selesai || '0', 10);
        var ditolak = parseInt(pesananEl.dataset.ditolak || '0', 10);

        new Chart(pesananEl, {
            type: 'bar',
            data: {
                labels: ['Dikemas', 'Dikirim', 'Selesai', 'Ditolak'],
                datasets: [{
                    label: 'Jumlah',
                    data: [dikemas, dikirim, selesai, ditolak]
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
});
</script>
@endpush

@endsection