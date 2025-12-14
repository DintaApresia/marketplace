{{-- resources/views/orders/index.blade.php --}}
@extends('layouts.pembeli') {{-- ganti kalau layout-mu namanya beda --}}

@section('title', 'Riwayat Pesanan')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">
  <h1 class="text-2xl font-bold mb-6 text-gray-800">
    Riwayat Pesanan Saya
  </h1>

  {{-- ALERT KOSONG (kalau nanti mau dipakai kondisi) --}}
  {{-- <div class="bg-white rounded-lg shadow p-6 text-center text-gray-600">
      Kamu belum memiliki pesanan.
  </div> --}}

  <div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
            Kode Order
          </th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
            Tanggal
          </th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
            Jumlah Item
          </th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
            Total
          </th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
            Status
          </th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>

      <tbody class="bg-white divide-y divide-gray-200 text-sm">
        {{-- DUMMY DATA 1 --}}
        <tr>
          <td class="px-4 py-3 whitespace-nowrap">
            ORD-20251127001
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
            27 Nov 2025 10:30
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
            3 item
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
            Rp 150.000
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
              Selesai
            </span>
          </td>
          <td class="px-4 py-3 text-right whitespace-nowrap">
            <button class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
              Lihat Detail
            </button>
          </td>
        </tr>

        {{-- DUMMY DATA 2 --}}
        <tr>
          <td class="px-4 py-3 whitespace-nowrap">
            ORD-20251126015
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
            26 Nov 2025 19:45
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
            1 item
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
            Rp 75.000
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
              Menunggu Pembayaran
            </span>
          </td>
          <td class="px-4 py-3 text-right whitespace-nowrap">
            <button class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
              Lihat Detail
            </button>
          </td>
        </tr>

        {{-- DUMMY DATA 3 --}}
        <tr>
          <td class="px-4 py-3 whitespace-nowrap">
            ORD-20251125003
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
            25 Nov 2025 14:10
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
            2 item
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
            Rp 210.000
          </td>
          <td class="px-4 py-3 whitespace-nowrap">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
              Dibatalkan
            </span>
          </td>
          <td class="px-4 py-3 text-right whitespace-nowrap">
            <button class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
              Lihat Detail
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- PAGINATION DUMMY --}}
  <div class="mt-4 flex justify-between items-center text-xs text-gray-500">
    <p>Menampilkan 1–3 dari 3 pesanan</p>
    <div class="inline-flex rounded-md shadow-sm border border-gray-200 overflow-hidden">
      <button class="px-3 py-1 border-r border-gray-200 hover:bg-gray-50" disabled>
        ‹
      </button>
      <button class="px-3 py-1 bg-indigo-50 text-indigo-600 font-semibold">
        1
      </button>
      <button class="px-3 py-1 border-l border-gray-200 hover:bg-gray-50" disabled>
        ›
      </button>
    </div>
  </div>

  <a href="{{ route('pembeli.profile') }}"
               class="text-xs sm:text-sm text-gray-500 hover:text-gray-700">
              ← Kembali ke halaman profil
            </a>
</div>
@endsection
