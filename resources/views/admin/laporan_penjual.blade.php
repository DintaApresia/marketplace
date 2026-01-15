@extends('layouts.admin')
@section('title','Laporan Penjual')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Laporan Penjualan per Penjual
            </h1>
            <p class="text-sm text-gray-500">
                Rekap pesanan dengan status <span class="font-medium">selesai</span>
            </p>
        </div>

        <div class="text-sm text-gray-500">
            Dicetak: {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    {{-- TABEL --}}
    <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 border text-center">No</th>
                        <th class="px-4 py-3 border">Nama Penjual</th>
                        <th class="px-4 py-3 border text-center">Total Transaksi</th>
                        <th class="px-4 py-3 border text-center">Produk Terjual</th>
                        <th class="px-4 py-3 border text-right">Subtotal</th>
                        <th class="px-4 py-3 border">Transaksi Terakhir</th>
                        <th class="px-4 py-3 border text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $i => $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border text-center">
                                {{ $i + 1 }}
                            </td>

                            <td class="px-4 py-2 border font-medium">
                                {{ $row->nama_penjual }}
                            </td>

                            <td class="px-4 py-2 border text-center">
                                {{ $row->total_transaksi }}
                            </td>

                            <td class="px-4 py-2 border text-center">
                                {{ $row->total_produk }}
                            </td>

                            <td class="px-4 py-2 border text-right font-semibold text-green-600">
                                Rp {{ number_format($row->subtotal,0,',','.') }}
                            </td>

                            <td class="px-4 py-2 border">
                                {{ \Carbon\Carbon::parse($row->last_transaction)->format('d M Y') }}
                            </td>

                            <td class="px-4 py-2 border text-center">
                                <a href="{{ route('admin.laporan.penjual.detail', $row->penjual_id) }}"
                                   class="inline-block px-3 py-1.5 rounded-md bg-indigo-600 text-white text-xs hover:bg-indigo-700 transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                Belum ada data penjualan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
