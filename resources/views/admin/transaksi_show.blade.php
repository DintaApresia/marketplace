@extends('layouts.admin')

@section('title', 'Detail Transaksi')

@section('content')
@php
    $kode = $order->kode_order ?? ('#'.$order->id);
@endphp

<div class="space-y-4">

    {{-- header + tombol kembali --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Detail Transaksi</h1>
            <p class="text-sm text-gray-500">
                Order: <span class="font-medium text-gray-700">{{ $kode }}</span>
            </p>
        </div>

        @if(($from ?? '') === 'aduan' && !empty($aduanId))
            <a href="{{ route('admin.aduan.show', ['id' => $aduanId]) }}"
               class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                ← Kembali
            </a>
        @else
            <a href="{{ route('admin.transaksi.index', ['tab' => ($fromTab ?? 'monitoring')]) }}"
               class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                ← Kembali
            </a>
        @endif
    </div>

    {{-- ringkasan --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- CARD: STATUS --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="space-y-3">
                <div>
                    <div class="text-sm text-gray-500">Status</div>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $order->status_pesanan ?? '-' }}
                        </span>
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">Dibuat</div>
                    <div class="text-sm text-gray-700 mt-1">{{ $order->created_at ?? '-' }}</div>
                </div>

                @if(($from ?? '') === 'riwayat')
                    <div>
                        <div class="text-sm text-gray-500">Tanggal Selesai</div>
                        <div class="text-sm text-gray-700 mt-1">{{ $tglSelesai ?? '-' }}</div>
                    </div>
                @endif

                @if(property_exists($order,'metode_pembayaran') || isset($order->metode_pembayaran))
                    <div>
                        <div class="text-sm text-gray-500">Metode Pembayaran</div>
                        <div class="text-sm text-gray-700 mt-1">{{ $order->metode_pembayaran ?? '-' }}</div>
                    </div>
                @endif

                @if(property_exists($order,'total') || isset($order->total))
                    <div>
                        <div class="text-sm text-gray-500">Total</div>
                        <div class="text-sm font-semibold text-gray-800 mt-1">{{ $order->total ?? '-' }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- CARD: PEMBELI --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="space-y-3">
                <div>
                    <div class="text-sm text-gray-500">Pembeli</div>
                    <div class="text-sm font-medium text-gray-800 mt-1">{{ $order->pembeli_nama ?? '-' }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $order->pembeli_email ?? '' }}</div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">ID Pembeli</div>
                    <div class="text-sm text-gray-700 mt-1">{{ $order->user_id ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- CARD: PENJUAL --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="space-y-3">
                <div>
                    <div class="text-sm text-gray-500">Penjual (Nama Toko)</div>
                    <div class="text-sm font-medium text-gray-800 mt-1">
                        {{ $order->nama_toko ?? '-' }}
                    </div>

                    {{-- nama penjual dari user (kalau ada) --}}
                    @if(!empty($order->penjual_nama))
                        <div class="text-xs text-gray-500 mt-1">Pemilik: {{ $order->penjual_nama }}</div>
                    @endif

                    @if(!empty($order->penjual_email))
                        <div class="text-xs text-gray-500 mt-1">{{ $order->penjual_email }}</div>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="text-sm text-gray-500">ID Penjual (orders)</div>
                        <div class="text-sm text-gray-700 mt-1">{{ $order->penjual_id ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">User ID Penjual</div>
                        <div class="text-sm text-gray-700 mt-1">{{ $order->penjual_user_id ?? '-' }}</div>
                    </div>
                </div>

                @if(empty($order->nama_toko) && empty($order->penjual_nama))
                    <div class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-2">
                        Catatan: Identitas penjual tidak ditemukan. Kemungkinan data order lama (penjual_id menunjuk ke id yang tidak cocok di users/penjuals).
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- timeline status --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <h2 class="font-semibold text-gray-800 mb-3">Timeline Status (order_status_logs)</h2>

        @if($logs->count() === 0)
            <div class="text-sm text-gray-500">Belum ada log status.</div>
        @else
            <ol class="space-y-3">
                @foreach($logs as $l)
                    <li class="flex gap-3">
                        <div class="mt-1 w-2 h-2 rounded-full bg-gray-400"></div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-medium text-gray-800">
                                    {{ $l->status ?? 'Perubahan Status' }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $l->created_at ?? '-' }}</div>
                            </div>
                            @if(!empty($l->catatan))
                                <div class="text-sm text-gray-600 mt-1">{{ $l->catatan }}</div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

    {{-- aduan terkait --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <h2 class="font-semibold text-gray-800 mb-3">Aduan Terkait Pesanan</h2>

        @if($aduans->count() === 0)
            <div class="text-sm text-gray-500">Tidak ada aduan untuk pesanan ini.</div>
        @else
            <div class="space-y-3">
                @foreach($aduans as $a)
                    <div class="border rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <div class="font-medium text-gray-800">{{ $a->judul }}</div>
                            <div class="text-xs text-gray-500">{{ $a->created_at ?? '-' }}</div>
                        </div>

                        <div class="text-sm text-gray-600 mt-1">{{ $a->deskripsi }}</div>

                        <div class="mt-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-slate-100 text-slate-700 border border-slate-200">
                                Status Aduan: {{ $a->status_aduan ?? '-' }}
                            </span>
                        </div>

                        @if(!empty($a->catatan_penjual))
                            <div class="mt-3 text-sm">
                                <div class="text-xs text-gray-500">Tanggapan Penjual</div>
                                <div class="text-gray-700">{{ $a->catatan_penjual }}</div>
                                @if(!empty($a->tgl_catatan_penjual))
                                    <div class="text-xs text-gray-400 mt-1">{{ $a->tgl_catatan_penjual }}</div>
                                @endif
                            </div>
                        @endif

                        @if(!empty($a->catatan_admin))
                            <div class="mt-3 text-sm">
                                <div class="text-xs text-gray-500">Catatan Admin</div>
                                <div class="text-gray-700">{{ $a->catatan_admin }}</div>
                                @if(!empty($a->tgl_catatan_admin))
                                    <div class="text-xs text-gray-400 mt-1">{{ $a->tgl_catatan_admin }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
