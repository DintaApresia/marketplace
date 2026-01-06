@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">

    <h1 class="text-2xl font-bold mb-4">Verifikasi Penjual</h1>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 text-sm rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow border border-gray-100 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                        ID
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                        Nama Penjual
                    </th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                        Akun User
                    </th>

                    {{-- KOLOM BARU: DETAIL PERSYARATAN --}}
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                        Persyaratan
                    </th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                        Status Seller
                    </th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse ($penjuals as $penjual)
                    @php
                        $user   = $penjual->user;
                        $status = $user->seller_status ?? 'pending';
                    @endphp
                    <tr>
                        <td class="px-4 py-2 text-gray-700">
                            {{ $penjual->id }}
                        </td>
                        <td class="px-4 py-2 text-gray-700">
                            {{ $penjual->nama_penjual ?? '-' }}
                        </td>
                        <td class="px-4 py-2 text-gray-700">
                            @if($user)
                                <div class="font-medium">{{ $user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $user->email }}</div>
                            @else
                                <span class="text-xs text-red-500">User tidak ditemukan</span>
                            @endif
                        </td>

                        {{-- KOLOM DETAIL PERSYARATAN --}}
                        <td class="px-4 py-2 text-gray-700">
                            <details class="text-xs">
                                <summary class="cursor-pointer text-blue-600 hover:underline">
                                    Lihat
                                </summary>
                                <div class="mt-2 space-y-1">
                                    {{-- KARTU IDENTITAS --}}
                                    <div>
                                        <span class="font-semibold">Kartu Identitas:</span>
                                        @if($penjual->kartu_identitas)
                                            {{-- Jika kamu simpan sebagai FILE (path di storage) --}}
                                            <a href="{{ asset('storage/'.$penjual->kartu_identitas) }}"
                                            target="_blank"
                                            class="text-blue-600 hover:underline ml-1">
                                                Lihat Kartu Identitas
                                            </a>
                                        @else
                                            <span class="ml-1 text-red-500">Belum diunggah</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-semibold">Alamat:</span>
                                        {{ $penjual->alamat ?? '-' }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">No. Telp:</span>
                                        {{ $penjual->no_telp ?? '-' }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">Lokasi Map:</span>
                                        @if(!empty($penjual->latitude) && !empty($penjual->longitude))
                                            {{ $penjual->latitude }}, {{ $penjual->longitude }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                    {{-- Tambah baris lain kalau ada persyaratan lain --}}
                                </div>
                            </details>
                        </td>

                        {{-- Status Seller --}}
                        <td class="px-4 py-2 text-right">
                            @if ($status === 'verified')
                                <span class="inline-flex items-center px-3 py-1.5 text-xs rounded-md bg-green-600 hover:bg-green-700 text-white">
                                    Terverifikasi
                                </span>
                            @elseif ($status === 'rejected')
                                <span class="inline-flex items-center px-3 py-1.5 text-xs rounded-md bg-red-600 hover:bg-red-700 text-white">
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 text-xs rounded-md bg-yellow-600 hover:bg-red-700 text-white">
                                    Pending
                                </span>
                            @endif
                        </td>

                        {{-- aksi --}}
                       <td class="px-4 py-2 text-right">
                        @if ($user)
                            <div class="flex justify-end gap-2">

                                {{-- Tombol VERIFIKASI --}}
                                <form action="{{ route('admin.penjual.verify', $penjual->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Verifikasi penjual ini?')">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="status" value="verified">
                                    <button
                                        class="inline-flex px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                        Verifikasi
                                    </button>
                                </form>

                                {{-- Tombol TOLAK --}}
                                <form action="{{ route('admin.penjual.verify', $penjual->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Tolak penjual ini karena belum memenuhi persyaratan?')">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="status" value="rejected">
                                    <button
                                        class="inline-flex px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                        Tolak
                                    </button>
                                </form>

                            </div>
                        @endif
                    </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                            Belum ada data penjual.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $penjuals->links() }}
    </div>

</div>
@endsection
