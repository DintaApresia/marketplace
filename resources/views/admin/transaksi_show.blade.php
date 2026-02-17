@extends('layouts.admin')

@section('title', 'Detail Transaksi')

@section('content')
@php
    $kode = $order->kode_order ?? ('#'.$order->id);

    // helper url gambar (storage / full url)
    $imgUrl = function ($path) {
        if (!$path) return null;
        return \Illuminate\Support\Str::startsWith($path, ['http://','https://'])
            ? $path
            : asset('storage/'.$path);
    };

    // normalisasi ratings agar aman dipakai
    $ratings = $ratings ?? collect();

    // hitung rata-rata kalau ada rating
    $avgRating = null;
    if ($ratings instanceof \Illuminate\Support\Collection && $ratings->count() > 0) {
        $avgRating = round($ratings->avg(function($r){
            return (int)($r->rating ?? 0);
        }), 2);
    }

    // ID penjual yang ditampilkan (cukup 1)
    $idPenjualTampil = $order->penjual_user_id ?? $order->penjual_id ?? null;

    // bukti pembayaran (kalau ada)
    $buktiUrl = null;
    if (!empty($order->bukti_pembayaran)) {
        $buktiUrl = $imgUrl($order->bukti_pembayaran);
    }
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

    {{-- ✅ ROW 1: INFO PEMBELI | INFO PENJUAL (dibuat rapat, minim space kosong) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- CARD: PEMBELI --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-gray-800">Info Pembeli</div>
                    <div class="text-xs text-gray-500">Identitas akun pembeli</div>
                </div>
            </div>

            <div class="mt-3 space-y-2">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-sm text-gray-500">Nama</div>
                    <div class="text-sm font-medium text-gray-800 text-right">
                        {{ $order->pembeli_nama ?? '-' }}
                    </div>
                </div>

                <div class="flex items-start justify-between gap-3">
                    <div class="text-sm text-gray-500">Email</div>
                    <div class="text-sm text-gray-700 text-right break-all">
                        {{ $order->pembeli_email ?? '-' }}
                    </div>
                </div>

                <div class="flex items-start justify-between gap-3">
                    <div class="text-sm text-gray-500">ID Pembeli</div>
                    <div class="text-sm text-gray-700 text-right">
                        {{ $order->user_id ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD: PENJUAL --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-gray-800">Info Penjual</div>
                    <div class="text-xs text-gray-500">Identitas toko & pemilik</div>
                </div>
            </div>

            <div class="mt-3 space-y-2">
                <div class="flex items-start justify-between gap-3">
                    <div class="text-sm text-gray-500">Nama Toko</div>
                    <div class="text-sm font-medium text-gray-800 text-right">
                        {{ $order->nama_toko ?? '-' }}
                    </div>
                </div>

                <div class="flex items-start justify-between gap-3">
                    <div class="text-sm text-gray-500">Pemilik</div>
                    <div class="text-sm text-gray-700 text-right">
                        {{ $order->penjual_nama ?? '-' }}
                    </div>
                </div>

                <div class="flex items-start justify-between gap-3">
                    <div class="text-sm text-gray-500">Email</div>
                    <div class="text-sm text-gray-700 text-right break-all">
                        {{ $order->penjual_email ?? '-' }}
                    </div>
                </div>

                <div class="flex items-start justify-between gap-3">
                    <div class="text-sm text-gray-500">ID Penjual</div>
                    <div class="text-sm text-gray-700 text-right">
                        {{ $idPenjualTampil ?? '-' }}
                    </div>
                </div>

                @if(empty($order->nama_toko) && empty($order->penjual_nama))
                    <div class="mt-2 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-2">
                        Catatan: Identitas penjual tidak ditemukan. Kemungkinan data order lama (penjual_id menunjuk ke id yang tidak cocok di users/penjuals).
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ✅ ROW 2: RINGKASAN PESANAN (full width) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-start justify-between gap-3">
            <div>
                <div class="text-sm font-semibold text-gray-800">Ringkasan Pesanan</div>
                <div class="text-xs text-gray-500">Status, pembayaran, total, dan bukti (jika transfer)</div>
            </div>
        </div>

        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">

            <div class="border rounded-lg p-3">
                <div class="text-xs text-gray-500">Status Pesanan</div>
                <div class="mt-1">
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $order->status_pesanan ?? '-' }}
                    </span>
                </div>
            </div>

            <div class="border rounded-lg p-3">
                <div class="text-xs text-gray-500">Status Pembayaran</div>
                <div class="mt-1">
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $order->status_pembayaran ?? '-' }}
                    </span>
                </div>
            </div>

            <div class="border rounded-lg p-3">
                <div class="text-xs text-gray-500">Metode Pembayaran</div>
                <div class="mt-1 text-sm text-gray-800">
                    {{ $order->metode_pembayaran ?? '-' }}
                </div>
            </div>

            <div class="border rounded-lg p-3">
                <div class="text-xs text-gray-500">Dibuat</div>
                <div class="mt-1 text-sm text-gray-800">
                    {{ $order->created_at ?? '-' }}
                </div>
            </div>

            @if(($from ?? '') === 'riwayat')
                <div class="border rounded-lg p-3">
                    <div class="text-xs text-gray-500">Tanggal Selesai</div>
                    <div class="mt-1 text-sm text-gray-800">
                        {{ $tglSelesai ?? '-' }}
                    </div>
                </div>
            @endif

            <div class="border rounded-lg p-3">
                <div class="text-xs text-gray-500">Total</div>
                <div class="mt-1 text-sm font-semibold text-gray-800">
                    {{ $order->total ?? '-' }}
                </div>
            </div>

            {{-- Bukti pembayaran (hanya kalau transfer) --}}
            @if(($order->metode_pembayaran ?? '') === 'transfer')
                <div class="border rounded-lg p-3 sm:col-span-2 lg:col-span-3">
                    <div class="text-xs text-gray-500">Bukti Pembayaran</div>

                    @if($buktiUrl)
                        <div class="mt-2 flex items-center gap-3">
                            <a href="{{ $buktiUrl }}" target="_blank"
                               class="w-16 h-16 rounded-lg overflow-hidden border bg-gray-50 hover:opacity-90 flex-shrink-0">
                                <img src="{{ $buktiUrl }}" class="w-full h-full object-cover" alt="Bukti Pembayaran">
                            </a>
                            <div class="text-sm">
                                <div class="text-gray-800 font-medium">Bukti tersedia</div>
                                <a href="{{ $buktiUrl }}" target="_blank"
                                   class="text-xs text-blue-600 hover:underline">
                                    Lihat ukuran penuh
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="mt-2 text-sm text-gray-500 italic">Belum ada bukti pembayaran.</div>
                    @endif
                </div>
            @endif

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

    {{-- ✅ RATING & ULASAN --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="font-semibold text-gray-800">Rating & Ulasan</h2>
                <p class="text-sm text-gray-500">Rating pembeli untuk produk pada pesanan ini.</p>
            </div>

            @if($avgRating !== null)
                <div class="text-right">
                    <div class="text-xs text-gray-500">Rata-rata</div>
                    <div class="text-lg font-semibold text-gray-800">{{ $avgRating }}/5</div>
                </div>
            @endif
        </div>

        @if(!($ratings instanceof \Illuminate\Support\Collection) || $ratings->count() === 0)
            <div class="text-sm text-gray-500 mt-3">Belum ada rating untuk pesanan ini.</div>
        @else
            <div class="mt-4 space-y-3">
                @foreach($ratings as $r)
                    @php
                        $imgs = [];
                        if (!empty($r->review_images)) {
                            $decoded = is_array($r->review_images)
                                ? $r->review_images
                                : json_decode($r->review_images, true);
                            if (is_array($decoded)) $imgs = $decoded;
                        }

                        $nilai = (int)($r->rating ?? 0);
                        $nilai = max(0, min(5, $nilai));
                    @endphp

                    <div class="border rounded-lg p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm font-medium text-gray-800">
                                    {{ $r->nama_barang ?? ('Produk ID: '.($r->produk_id ?? '-')) }}
                                </div>

                                <div class="mt-1 text-sm text-yellow-600">
                                    {{ str_repeat('★', $nilai) }}{{ str_repeat('☆', 5-$nilai) }}
                                    <span class="text-xs text-gray-500 ml-2">{{ $nilai }}/5</span>
                                </div>

                                @if(!empty($r->review))
                                    <div class="text-sm text-gray-700 mt-2">
                                        “{{ $r->review }}”
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 mt-2 italic">Tidak ada komentar.</div>
                                @endif
                            </div>

                            <div class="text-xs text-gray-500 whitespace-nowrap">
                                {{ !empty($r->created_at) ? \Carbon\Carbon::parse($r->created_at)->format('d M Y H:i') : '-' }}
                            </div>
                        </div>

                        @if(!empty($imgs))
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($imgs as $img)
                                    @php $url = $imgUrl($img); @endphp
                                    @if($url)
                                        <a href="{{ $url }}" target="_blank" class="block">
                                            <img src="{{ $url }}" class="w-16 h-16 rounded-lg object-cover border" alt="Review image">
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
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
