{{-- resources/views/penjual/daftar.blade.php --}}
@extends('layouts.pembeli')

@section('title','Daftar Sebagai Penjual — SecondLife')

@section('content')
  <div class="max-w-3xl mx-auto mt-6">

    {{-- Flash status umum --}}
    @if (session('status'))
      <div class="mb-4 rounded-md bg-green-50 text-green-800 px-4 py-2 text-sm">
        {{ session('status') }}
      </div>
    @endif

    {{-- Card judul --}}
    <div class="bg-white border rounded-xl p-4 sm:p-6">
      <h1 class="text-xl sm:text-2xl font-semibold text-green-700">
        Daftar Sebagai Penjual
      </h1>
      <p class="mt-1 text-sm text-gray-600">
        Lengkapi data toko di bawah ini untuk mengajukan permintaan sebagai penjual di SecondLife.
        Admin akan meninjau pengajuanmu sebelum akun penjual diaktifkan.
      </p>
    </div>

    {{-- SUDAH PENJUAL / APPROVED --}}
    @if(
      $user->role === 'penjual'
      && ($user->seller_status === 'approved' || $user->seller_status === 'verified')
    )
      <div class="mt-4 bg-white border rounded-xl p-4 sm:p-6">
        <p class="text-sm text-green-800">
          Akun kamu sudah terverifikasi sebagai <span class="font-semibold">penjual</span>.  
          Kamu dapat mengakses <span class="font-semibold">Dashboard Penjual</span> dari menu penjual.
        </p>
      </div>

    {{-- PENDING: TUNJUKKAN RINGKASAN DATA --}}
    @elseif($user->seller_status === 'pending')
      <div class="mt-4 bg-white border rounded-xl p-4 sm:p-6">
        <p class="text-sm text-yellow-800">
          Permintaan kamu untuk menjadi <span class="font-semibold">penjual</span> sedang dalam proses peninjauan oleh admin.
          Kamu akan mendapatkan notifikasi setelah admin memberikan keputusan.
        </p>
        <p class="mt-2 text-xs text-gray-500">
          Sambil menunggu, kamu masih bisa berbelanja seperti biasa sebagai pembeli.
        </p>
      </div>

      @if(isset($penjual))
        {{-- Ringkasan data yang diajukan --}}
        <div class="mt-4 bg-white border rounded-xl p-4 sm:p-6 space-y-4">
          <h2 class="text-sm font-semibold text-gray-800">
            Data yang kamu ajukan sebagai penjual
          </h2>

          {{-- Nama Toko --}}
          <div>
            <label class="block text-xs font-medium text-gray-500">Nama Toko</label>
            <input
              type="text"
              class="mt-1 block w-full rounded-md border px-3 py-2 text-sm bg-gray-100"
              value="{{ $penjual->nama_toko }}"
              disabled
            >
          </div>

          {{-- Alamat Toko --}}
          <div>
            <label class="block text-xs font-medium text-gray-500">Alamat Toko / Gudang</label>
            <textarea
              class="mt-1 block w-full rounded-md border px-3 py-2 text-sm bg-gray-100"
              rows="3"
              disabled>{{ $penjual->alamat_toko }}</textarea>
          </div>

          {{-- Rekening --}}
          <div>
            <label class="block text-xs font-medium text-gray-500">Rekening Penjual</label>
            <input
              type="text"
              class="mt-1 block w-full rounded-md border px-3 py-2 text-sm bg-gray-100"
              value="{{ $penjual->rekening }}"
              disabled
            >
          </div>

          {{-- Nama Pemilik Rekening --}}
          <div>
            <label class="block text-xs font-medium text-gray-500">Nama Pemilik Rekening</label>
            <input
              type="text"
              class="mt-1 block w-full rounded-md border px-3 py-2 text-sm bg-gray-100"
              value="{{ $penjual->nama_rekening }}"
              disabled
            >
          </div>

          {{-- Kartu Identitas --}}
          @if(!empty($penjual->kartu_identitas ?? null))
            @php
              $ext = strtolower(pathinfo($penjual->kartu_identitas, PATHINFO_EXTENSION));
            @endphp

            <div>
              <label class="block text-xs font-medium text-gray-500">Kartu Identitas</label>

              @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                {{-- Langsung tampilkan sebagai gambar --}}
                <div class="mt-2">
                  <img
                    src="{{ asset('storage/'.$penjual->kartu_identitas) }}"
                    alt="Kartu identitas"
                    class="max-h-64 rounded-md border"
                  >
                </div>
              @else
                {{-- Kalau ternyata PDF atau selain gambar, tetap kasih link --}}
                <a href="{{ asset('storage/'.$penjual->kartu_identitas) }}"
                  target="_blank"
                  class="mt-1 inline-flex text-xs text-blue-600 underline">
                  Buka file kartu identitas
                </a>
              @endif
            </div>
          @endif
        </div>
      @endif

    {{-- FORM DAFTAR (NONE / REJECTED) --}}
    @else
      <div class="mt-4 bg-white border rounded-xl p-4 sm:p-6">
        @if($user->seller_status === 'rejected')
          <div class="mb-4 rounded-md bg-red-50 text-red-800 px-4 py-2 text-sm">
            Permintaanmu sebelumnya untuk menjadi penjual <span class="font-semibold">ditolak</span> oleh admin.
            Silakan periksa dan perbaiki data toko di bawah ini sebelum mengajukan ulang.
          </div>
        @endif

        @if($errors->any())
          <div class="mb-4 rounded-md bg-red-50 text-red-800 px-4 py-2 text-sm">
            {{ $errors->first() }}
          </div>
        @endif

        <form
          method="POST"
          action="{{ route('penjual.daftar.submit') }}"
          class="space-y-4"
          enctype="multipart/form-data"
        >
          @csrf

          {{-- Nama Toko --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Nama Toko</label>
            <input
              type="text"
              name="nama_toko"
              value="{{ old('nama_toko', $penjual->nama_toko ?? '') }}"
              class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:ring-2 focus:ring-green-600"
              placeholder="Contoh: SecondLife Thrift Store"
              required
            >
            <p class="mt-1 text-xs text-gray-500">
              Gunakan nama toko yang jelas dan mudah diingat.
            </p>
          </div>

          {{-- Alamat Toko --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Alamat Toko / Gudang</label>
            <textarea
              name="alamat_toko"
              rows="3"
              class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:ring-2 focus:ring-green-600"
              placeholder="Tulis alamat lengkap lokasi toko / gudang."
              required
            >{{ old('alamat_toko', $penjual->alamat_toko ?? '') }}</textarea>
          </div>

          {{-- Kartu Identitas --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Kartu Identitas (KTP/SIM)</label>
            <input
              type="file"
              name="kartu_identitas"
              accept="image/*,.pdf"
              class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:ring-2 focus:ring-green-600"
              required
            >
            <p class="text-xs text-gray-500 mt-1">
              Unggah foto KTP/SIM atau identitas lainnya (format: JPG, PNG, atau PDF).
            </p>
          </div>

          {{-- Rekening --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Rekening Penjual</label>
            <input
              type="text"
              name="rekening"
              value="{{ old('rekening', $penjual->rekening ?? '') }}"
              class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:ring-2 focus:ring-green-600"
              placeholder="Contoh: BCA 1234567890"
              required
            >
            <p class="mt-1 text-xs text-gray-500">
              Rekening yang akan digunakan untuk menerima hasil penjualan.
            </p>
          </div>

          {{-- Nama Pemilik Rekening --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Nama Pemilik Rekening</label>
            <input
              type="text"
              name="nama_rekening"
              value="{{ old('nama_rekening', $penjual->nama_rekening ?? $user->name) }}"
              class="mt-1 block w-full rounded-md border px-3 py-2 text-sm focus:ring-2 focus:ring-green-600"
              placeholder="Nama sesuai buku tabungan"
              required
            >
          </div>

          <div class="flex items-center justify-between pt-4 border-t mt-4">
            <a href="{{ route('pembeli.profile') }}"
               class="text-xs sm:text-sm text-gray-500 hover:text-gray-700">
              ← Kembali ke halaman profil
            </a>

            <button
              type="submit"
              class="inline-flex items-center rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800"
            >
              Kirim Permintaan Jadi Penjual
            </button>
          </div>
        </form>
      </div>
    @endif
  </div>
@endsection
