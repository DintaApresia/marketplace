@extends('layouts.penjual')

@section('title','Laporan')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-4">
        <h1 class="text-xl font-semibold text-gray-800">
            Laporan Dashboard Penjual
        </h1>
        <p class="text-sm text-gray-500">
            Dicetak: {{ now()->format('d M Y H:i') }}
        </p>
    </div>

    {{-- FILTER PERIODE --}}
    <form method="GET"
          action="{{ route('penjual.laporan') }}"
          class="flex flex-wrap gap-3 items-end mb-6">

        <div>
            <label class="block text-sm mb-1">Dari Tanggal</label>
            <input type="date"
                   name="tanggal_mulai"
                   value="{{ request('tanggal_mulai') }}"
                   class="border rounded px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm mb-1">Sampai Tanggal</label>
            <input type="date"
                   name="tanggal_selesai"
                   value="{{ request('tanggal_selesai') }}"
                   class="border rounded px-3 py-2 text-sm">
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded text-sm">
            Tampilkan
        </button>

        <a href="{{ route('penjual.download.laporan', request()->query()) }}"
           class="bg-red-600 text-white px-4 py-2 rounded text-sm">
            Download PDF
        </a>
    </form>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Laporan Dashboard Penjual
            </h2>
            <p class="text-sm text-gray-500">
                Periode:
                <span class="font-medium text-gray-700">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                    –
                    {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </span>
            </p>
        </div>

        <span class="mt-2 sm:mt-0 inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">
            📊 Laporan Penjualan
        </span>
    </div>

    {{-- -Ringkasan --}}
    <div class="border rounded-lg p-4 bg-white">
        <h3 class="font-semibold mb-3">Ringkasan Pesanan</h3>

        <table class="w-full border-collapse text-sm">
            <tr class="bg-gray-100">
                <th class="border p-2 text-left">Total Pesanan</th>
                <td class="border p-2">{{ $pesananMasuk }}</td>
            </tr>
            <tr class="bg-gray-50 font-semibold">
                <th class="border p-2 text-left">Total Terjual</th>
                <td class="border p-2 text-green-700">
                    Rp {{ number_format($totalTerjual ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- RINGKASAN PRODUK --}}
    <div class="border rounded-md p-4 mb-5 bg-white">
        <h3 class="font-semibold text-gray-800 mb-3">Ringkasan Produk</h3>

        <table class="w-full text-sm border-collapse">
            <tbody>
                <tr>
                    <th class="border px-3 py-2 text-left w-1/2 bg-gray-50">
                        Total Produk
                    </th>
                    <td class="border px-3 py-2">
                        {{ $totalProduk }}
                    </td>
                </tr>
                <tr>
                    <th class="border px-3 py-2 text-left bg-gray-50">
                        Produk Aktif
                    </th>
                    <td class="border px-3 py-2">
                        {{ $produkAktif }}
                    </td>
                </tr>
                <tr>
                    <th class="border px-3 py-2 text-left bg-gray-50">
                        Produk Nonaktif
                    </th>
                    <td class="border px-3 py-2">
                        {{ $produkNonaktif }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>



    {{-- RINGKASAN PESANAN --}}
    <div class="border rounded-md p-4 bg-white">
        <h3 class="font-semibold text-gray-800 mb-3">Ringkasan Pesanan</h3>

        <table class="w-full text-sm border-collapse">
            <tbody>
                <tr>
                    <th class="border px-3 py-2 text-left w-1/2 bg-gray-50">
                        Total Pesanan
                    </th>
                    <td class="border px-3 py-2">
                        {{ $pesananMasuk }}
                    </td>
                </tr>
                <tr>
                    <th class="border px-3 py-2 text-left bg-gray-50">
                        Dikemas
                    </th>
                    <td class="border px-3 py-2">
                        {{ $pesananDikemas }}
                    </td>
                </tr>
                <tr>
                    <th class="border px-3 py-2 text-left bg-gray-50">
                        Dikirim
                    </th>
                    <td class="border px-3 py-2">
                        {{ $pesananDikirim }}
                    </td>
                </tr>
                <tr>
                    <th class="border px-3 py-2 text-left bg-gray-50">
                        Selesai
                    </th>
                    <td class="border px-3 py-2">
                        {{ $pesananSelesai }}
                    </td>
                </tr>
                <tr>
                    <th class="border px-3 py-2 text-left bg-gray-50">
                        Ditolak
                    </th>
                    <td class="border px-3 py-2">
                        {{ $pesananDitolak }}
                    </td>
                </tr>
                <tr class="font-semibold">
                    <th class="border px-3 py-2 text-left bg-gray-100">
                        Total Terjual
                    </th>
                    <td class="border px-3 py-2 text-gray-800">
                        Rp {{ number_format($totalTerjual ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>



</div>
@endsection
