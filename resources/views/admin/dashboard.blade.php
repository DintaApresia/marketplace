@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Judul --}}
    <h1 class="text-2xl font-bold text-gray-800 mb-2">
        Dashboard Admin
    </h1>
    <p class="text-sm text-gray-500 mb-6">
        Halaman ini khusus untuk admin. Nanti dari sini bisa mengelola user, penjual, dan barang.
    </p>

    <!-- {{-- Info singkat --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="p-4 bg-white rounded shadow">
            <p class="text-gray-500 text-sm">Total User</p>
            <p class="text-3xl font-bold">{{ $totalUsers }}</p>
        </div>

        <div class="p-4 bg-white rounded shadow">
            <p class="text-gray-500 text-sm">Total Penjual</p>
            <p class="text-3xl font-bold">{{ $totalPenjual }}</p>
        </div>

        <div class="p-4 bg-white rounded shadow">
            <p class="text-gray-500 text-sm">Total Pembeli</p>
            <p class="text-3xl font-bold">{{ $totalPembeli }}</p>
        </div>
    </div> -->

    {{-- GRAFIK --}}
    <div class="bg-white rounded-lg shadow border border-gray-100 p-5 mb-8">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">
            Grafik Jumlah User
        </h2>

        <canvas id="userChart" height="120"></canvas>
    </div>

    {{-- Menu --}}
    <div class="bg-white rounded-lg shadow border border-gray-100 p-4">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">
            Menu Admin
        </h2>

        <div class="flex flex-col gap-2 text-sm">
            <a href="{{ route('admin.user') }}" class="px-4 py-2 rounded border hover:bg-gray-50">
                Kelola User
            </a>
            <a href="{{ route('admin.penjual') }}" class="px-4 py-2 rounded border hover:bg-gray-50">
                Verifikasi Penjual
            </a>
            <a href="{{ route('admin.toko.show')}}" class="px-4 py-2 rounded border hover:bg-gray-50">
                Kelola Toko
            </a>
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('userChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['User', 'Admin', 'Penjual', 'Pembeli'],
            datasets: [{
                label: 'Jumlah',
                data: [
                    {{ $totalUsers }},
                    {{ $totalAdmin }},
                    {{ $totalPenjual }},
                    {{ $totalPembeli }}
                ],
                backgroundColor: [
                    'rgba(59, 130, 246, 0.7)',   // User
                    'rgba(239, 68, 68, 0.7)',    // Admin
                    'rgba(16, 185, 129, 0.7)',   // Penjual
                    'rgba(234, 179, 8, 0.7)'     // Pembeli
                ],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
</script>

@endsection
