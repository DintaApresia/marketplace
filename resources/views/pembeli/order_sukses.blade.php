<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Berhasil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind --}}
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900">

<div class="max-w-3xl mx-auto p-6">

    <div class="rounded-xl border bg-white p-6 text-center space-y-4">

        {{-- ICON --}}
        <div class="flex justify-center">
            <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-8 w-8 text-green-600"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <h1 class="text-2xl font-semibold text-gray-800">
            Pesanan Berhasil Dibuat
        </h1>

        <p class="text-gray-600">
            Terima kasih! Pesanan kamu sudah kami terima dan akan segera diproses.
        </p>

        {{-- INFO ORDER --}}
        <div class="mt-4 rounded-lg bg-gray-50 p-4 text-left text-sm space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-500">ID Pesanan</span>
                <span class="font-semibold">#{{ $order->id }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal Pembelian</span>
                <span class="font-semibold">
                    {{ $order->created_at->translatedFormat('d F Y, H:i') }}
                </span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Metode Pembayaran</span>
                <span class="font-semibold uppercase">
                    {{ $order->metode_pembayaran }}
                </span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Total Bayar</span>
                <span class="font-semibold text-green-700">
                    Rp{{ number_format($order->total_bayar, 0, ',', '.') }}
                </span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-500">Status Pesanan</span>
                <span class="font-semibold">
                    {{ ucfirst($order->status_pesanan) }}
                </span>
            </div>
        </div>

        {{-- INFO TAMBAHAN --}}
        @if($order->metode_pembayaran === 'transfer')
            <p class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                ⏳ Bukti pembayaran sedang menunggu verifikasi penjual.
            </p>
        @else
            <p class="text-sm text-blue-700 bg-blue-50 border border-blue-200 rounded-lg p-3">
                💵 Pembayaran dilakukan saat barang diterima (COD).
            </p>
        @endif

        {{-- ACTION --}}
        <div class="pt-4 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('pembeli.checkout') }}"
               class="rounded-lg border px-4 py-2 text-sm font-semibold hover:bg-gray-50">
                Belanja Lagi
            </a>

            <a href="{{ route('pembeli.keranjang') }}"
               class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                Kembali ke Keranjang
            </a>
        </div>

    </div>

</div>

</body>
</html>
