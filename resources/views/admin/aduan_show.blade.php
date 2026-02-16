@extends('layouts.admin')

@section('title', 'Detail Aduan')

@section('content')
@php
    $kodeOrder = $aduan->kode_order ?? ('#'.$aduan->order_id);

    // bukti itu cast array di model, tapi ini view pakai query builder.
    // Jadi kita decode aman (kalau sudah array, dipakai langsung).
    $buktiArr = [];
    if (!empty($aduan->bukti)) {
        $buktiArr = is_array($aduan->bukti) ? $aduan->bukti : (json_decode($aduan->bukti, true) ?: []);
    }

    $badgeAduan = function($s){
        $s = strtolower((string)$s);
        return match($s){
            'menunggu'   => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            'diproses'   => 'bg-blue-100 text-blue-800 border border-blue-200',
            'selesai'    => 'bg-green-100 text-green-800 border border-green-200',
            'dibatalkan' => 'bg-red-100 text-red-800 border border-red-200',
            default      => 'bg-gray-100 text-gray-700 border border-gray-200',
        };
    };
@endphp

<div class="space-y-4">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-800">Detail Aduan</h1>
            <p class="text-sm text-gray-500">
                AD{{ str_pad($aduan->id, 3, '0', STR_PAD_LEFT) }}
                • Pesanan: <span class="font-medium text-gray-700">{{ $kodeOrder }}</span>
            </p>
        </div>

        {{-- ✅ Kembali selalu ke TAB aduan (list), karena ini halaman detail aduan --}}
        <a href="{{ route('admin.transaksi.index', ['tab'=>'aduan']) }}"
           class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
            ← Kembali
        </a>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- CARD: INFO ADUAN --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 space-y-4 lg:col-span-2">

            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-sm text-gray-500">Judul</div>
                    <div class="text-lg font-semibold text-gray-800">{{ $aduan->judul }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $aduan->created_at ?? '-' }}</div>
                </div>

                <span class="px-2 py-1 rounded-md text-xs {{ $badgeAduan($aduan->status_aduan ?? '') }}">
                    {{ $aduan->status_aduan ?? '-' }}
                </span>
            </div>

            <div>
                <div class="text-sm text-gray-500">Deskripsi</div>
                <div class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $aduan->deskripsi }}</div>
            </div>

            {{-- BUKTI (ARRAY) --}}
            <div>
                <div class="text-sm text-gray-500">Bukti</div>

                @if(count($buktiArr) === 0)
                    <div class="text-sm text-gray-500 mt-1">Tidak ada bukti.</div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-2">
                        @foreach($buktiArr as $path)
                            <a href="{{ asset('storage/'.$path) }}" target="_blank"
                               class="block border rounded-lg overflow-hidden hover:shadow">
                                <img src="{{ asset('storage/'.$path) }}" class="w-full h-28 object-cover">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- CATATAN PENJUAL --}}
            <div class="border rounded-lg p-3 bg-gray-50">
                <div class="text-xs text-gray-500">Catatan Penjual</div>
                @if($aduan->catatan_penjual)
                    <div class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $aduan->catatan_penjual }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $aduan->tgl_catatan_penjual ?? '-' }}</div>
                @else
                    <div class="text-sm text-gray-500 mt-1">Belum ada tanggapan penjual.</div>
                @endif
            </div>

            {{-- CATATAN ADMIN --}}
            <div class="border rounded-lg p-3 bg-gray-50">
                <div class="text-xs text-gray-500">Catatan Admin</div>
                @if($aduan->catatan_admin)
                    <div class="text-sm text-gray-700 mt-1 whitespace-pre-line">{{ $aduan->catatan_admin }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $aduan->tgl_catatan_admin ?? '-' }}</div>
                @else
                    <div class="text-sm text-gray-500 mt-1">Belum ada catatan admin.</div>
                @endif
            </div>

        </div>

        {{-- CARD: INFO ORDER + AKSI ADMIN --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 space-y-4">

            <div>
                <div class="text-sm text-gray-500">Pesanan</div>
                <div class="text-sm font-semibold text-gray-800">{{ $kodeOrder }}</div>
                <div class="text-xs text-gray-500 mt-1">Status Pesanan: {{ $aduan->status_pesanan ?? '-' }}</div>
            </div>

            <div class="pt-2 border-t">
                <div class="text-sm text-gray-500">Pembeli</div>
                <div class="text-sm text-gray-800">{{ $aduan->pembeli_nama ?? '-' }}</div>
            </div>

            <div class="pt-2 border-t">
                <div class="text-sm text-gray-500">Nama Toko</div>
                <div class="text-sm text-gray-800">{{ $aduan->nama_toko ?? '-' }}</div>
            </div>

            {{-- ✅ tombol ke detail transaksi: kirim from=aduan + aduan_id biar tombol kembali di detail transaksi balik ke detail aduan --}}
            <div class="pt-2 border-t">
                <a href="{{ route('admin.transaksi.show', ['id'=>$aduan->order_id, 'from'=>'aduan', 'aduan_id'=>$aduan->id]) }}"
                   class="w-full inline-flex justify-center px-3 py-2 rounded-lg border text-blue-600 hover:bg-blue-50 text-sm">
                    ▶ Lihat Detail Transaksi
                </a>
            </div>

            {{-- AKSI ADMIN --}}
            <div class="pt-2 border-t space-y-3">
                <div class="text-sm font-semibold text-gray-800">Aksi Admin</div>

                {{-- Update status aduan --}}
                <form method="POST" action="{{ route('admin.aduan.updateStatus', ['id'=>$aduan->id]) }}" class="space-y-2">
                    @csrf

                    <select name="status_aduan" class="border rounded-lg px-3 py-2 text-sm w-full">
                        @foreach(['menunggu','diproses','selesai','dibatalkan'] as $s)
                            <option value="{{ $s }}" @selected(($aduan->status_aduan ?? '') === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>

                    <button class="w-full px-3 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                        Simpan Status
                    </button>
                </form>

                {{-- Catatan admin / tanggapan admin --}}
                <form method="POST" action="{{ route('admin.aduan.tanggapi', ['id'=>$aduan->id]) }}" class="space-y-2">
                    @csrf

                    <textarea name="catatan_admin" rows="4"
                              class="w-full border rounded-lg px-3 py-2 text-sm"
                              placeholder="Tulis tanggapan/keputusan admin...">{{ old('catatan_admin', $aduan->catatan_admin) }}</textarea>

                    @error('catatan_admin')
                        <div class="text-xs text-red-600">{{ $message }}</div>
                    @enderror

                    <button class="w-full px-3 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                        Simpan Catatan
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- TIMELINE STATUS ORDER --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <h2 class="font-semibold text-gray-800 mb-3">Timeline Status Pesanan</h2>

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
                            @if(isset($l->catatan) && $l->catatan)
                                <div class="text-sm text-gray-600 mt-1">{{ $l->catatan }}</div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

</div>
@endsection
