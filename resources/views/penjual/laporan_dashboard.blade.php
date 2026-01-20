@extends('layouts.penjual')

@section('content')
<div class="flex items-start justify-between mb-4 w-full">
    {{-- KIRI: Judul + deskripsi --}}
    <div>
        <h2 class="text-xl font-bold text-green-800">
            Laporan Penjualan
        </h2>

        <p class="text-sm text-gray-600 mt-1 max-w-xl">
            Halaman ini menampilkan ringkasan laporan penjualan toko, termasuk jumlah transaksi,
            total pendapatan, dan rekap penjualan berdasarkan periode tertentu.
        </p>
    </div>
</div>
<div class="max-w-4xl mx-auto bg-white p-8 border border-gray-300 text-sm text-gray-800">

    {{-- ================= HEADER ================= --}}
    <div class="text-center mb-6">
        <h1 class="text-lg font-bold uppercase text-green-700">
            Laporan Penjualan Penjual
        </h1>
        <p class="text-sm text-gray-600">
            SecondLife Marketplace
        </p>
    </div>

    <hr class="border-gray-400 mb-6">

    {{-- ================= IDENTITAS ================= --}}
    <table class="w-full mb-6">
        <tr>
            <td class="w-1/3 text-gray-600">Nama Penjual</td>
            <td class="w-1">:</td>
            <td class="font-medium">{{ auth()->user()->name }}</td>
        </tr>
        <tr>
            <td class="text-gray-600">Periode Laporan</td>
            <td>:</td>
            <td>
                {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                s.d.
                {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </td>
        </tr>
        <tr>
            <td class="text-gray-600">Tanggal Cetak</td>
            <td>:</td>
            <td>{{ now()->timezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
        </tr>
    </table>

    {{-- ================= FILTER ================= --}}
    <div class="mb-6 border border-green-200 bg-green-50 p-4 rounded">
        <form method="GET"
              action="{{ route('penjual.laporan') }}"
              class="flex flex-wrap gap-4 items-end">

            <div>
                <label class="block mb-1 text-xs text-gray-600">Dari Tanggal</label>
                <input type="date"
                       name="tanggal_mulai"
                       value="{{ request('tanggal_mulai') }}"
                       class="border px-3 py-2 rounded">
            </div>

            <div>
                <label class="block mb-1 text-xs text-gray-600">Sampai Tanggal</label>
                <input type="date"
                       name="tanggal_selesai"
                       value="{{ request('tanggal_selesai') }}"
                       class="border px-3 py-2 rounded">
            </div>

            <button class="px-4 py-2 bg-green-600 text-white rounded">
                Tampilkan
            </button>

            <a href="{{ route('penjual.download.laporan', request()->query()) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded">
                Download PDF
            </a>
        </form>
    </div>

    {{-- ================= I. RINGKASAN PENJUALAN ================= --}}
    <div class="mb-6">
        <h2 class="font-semibold mb-2 border-l-4 border-green-600 pl-2 text-green-700">
            I. Ringkasan Penjualan
        </h2>

        <table class="w-full border border-collapse">
            <tr class="bg-gray-100">
                <th class="border px-3 py-2 text-left w-1/2">Total Pesanan</th>
                <td class="border px-3 py-2">{{ $pesananMasuk }}</td>
            </tr>
            <tr>
                <th class="border px-3 py-2 text-left">Total Terjual</th>
                <td class="border px-3 py-2 font-semibold text-green-700">
                    Rp {{ number_format($totalTerjual ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ================= RINCIAN PRODUK TERJUAL ================= --}}
    <div class="mb-6">
        <h2 class="font-semibold mb-2 border-l-4 border-green-600 pl-2 text-green-700">
            Rincian Produk Terjual
        </h2>

        @if($produkTerjual->count())
            <div class="overflow-x-auto">
                <table class="w-full border border-collapse text-sm">
                    <thead class="bg-slate-700">
                        <tr>
                            <th class="border px-3 text-white py-2 w-10 text-center">No</th>
                            <th class="border px-3 text-white py-2">Tgl Pembelian</th>
                            <th class="border px-3 text-white py-2">Nama Pembeli</th>
                            <th class="border px-3 text-white py-2">Nama Produk</th>
                            <th class="border px-3 text-white py-2">Status Pesanan</th>
                            <th class="border px-3 text-white py-2 text-center">Jumlah</th>
                            <th class="border px-3 text-white py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produkTerjual as $index => $item)
                            <tr>
                                <td class="border px-3 py-2 text-center">
                                    {{ $index + 1 }}
                                </td>
                                <td class="border px-3 py-2">
                                    {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d M Y') }}
                                </td>
                                <td class="border px-3 py-2">
                                    {{ $item->nama_pembeli }}
                                </td>
                                <td class="border px-3 py-2">
                                    {{ $item->nama_barang }}
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    @php
                                        $statusColor = match($item->status_pesanan) {
                                            'dikemas' => 'text-yellow-600',
                                            'dikirim' => 'text-blue-600',
                                            'selesai' => 'text-green-700',
                                            'ditolak' => 'text-red-600',
                                            default => 'text-gray-600'
                                        };
                                    @endphp

                                    <span class="font-semibold {{ $statusColor }}">
                                        {{ ucfirst($item->status_pesanan) }}
                                    </span>
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    {{ $item->jumlah }}
                                </td>
                                <td class="border px-3 py-2 text-right font-medium text-green-700">
                                    Rp {{ number_format($item->subtotal_item, 0, ',', '.') }}
                                </td>
                            </tr>

                            <tr class="bg-gray-100 font-semibold">
                        @endforeach
                        <tr class="bg-gray-100 font-semibold">
                            <td colspan="6" class="border px-3 py-2 text-right">
                                Total Subtotal
                            </td>
                            <td class="border px-3 py-2 text-right text-green-700">
                                Rp {{ number_format($totalSubtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500">
                Tidak ada produk terjual pada periode ini.
            </p>
        @endif
    </div>


    {{-- ================= II. RINGKASAN PRODUK ================= --}}
    <div class="mb-6">
        <h2 class="font-semibold mb-2 border-l-4 border-blue-600 pl-2 text-blue-700">
            II. Ringkasan Produk
        </h2>

        <table class="w-full border border-collapse">
            <tr class="bg-gray-100">
                <th class="border px-3 py-2 text-left w-1/2">Total Produk</th>
                <td class="border px-3 py-2">{{ $totalProduk }}</td>
            </tr>
            <tr>
                <th class="border px-3 py-2 text-left">Produk Aktif</th>
                <td class="border px-3 py-2">{{ $produkAktif }}</td>
            </tr>
            <tr>
                <th class="border px-3 py-2 text-left">Produk Nonaktif</th>
                <td class="border px-3 py-2">{{ $produkNonaktif }}</td>
            </tr>
        </table>
    </div>

    {{-- ================= III. STATUS PESANAN ================= --}}
    <div class="mb-10">
        <h2 class="font-semibold mb-2 border-l-4 border-gray-600 pl-2 text-gray-700">
            III. Ringkasan Status Pesanan
        </h2>

        <table class="w-full border border-collapse">
            <tr class="bg-gray-100">
                <th class="border px-3 py-2 text-left w-1/2">Dikemas</th>
                <td class="border px-3 py-2">{{ $pesananDikemas }}</td>
            </tr>
            <tr>
                <th class="border px-3 py-2 text-left">Dikirim</th>
                <td class="border px-3 py-2">{{ $pesananDikirim }}</td>
            </tr>
            <tr>
                <th class="border px-3 py-2 text-left">Selesai</th>
                <td class="border px-3 py-2">{{ $pesananSelesai }}</td>
            </tr>
            <tr>
                <th class="border px-3 py-2 text-left">Ditolak</th>
                <td class="border px-3 py-2 text-red-600">
                    {{ $pesananDitolak }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ================= TANDA TANGAN ================= --}}
    <div class="flex justify-end mt-12">
        <div class="text-center">
            <p class="text-gray-600">{{ now()->format('d M Y') }}</p>
            <p class="mt-16 font-semibold text-gray-800">
                {{ auth()->user()->name }}
            </p>
            <p class="text-xs text-gray-500">Penjual</p>
        </div>
    </div>

</div>
@endsection