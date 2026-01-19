{{-- resources/views/penjual/daftar.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Sebagai Penjual — SecondLife</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen text-gray-800">

  <div class="max-w-3xl mx-auto mt-10 px-4">

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

    {{-- PENDING --}}
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
        <div class="mt-4 bg-white border rounded-xl p-4 sm:p-6 space-y-4">
          <h2 class="text-sm font-semibold text-gray-800">
            Data yang kamu ajukan sebagai penjual
          </h2>

          <div>
            <label class="block text-xs font-medium text-gray-500">Nama Toko</label>
            <input class="mt-1 w-full rounded-md border px-3 py-2 text-sm bg-gray-100"
                   value="{{ $penjual->nama_toko }}" disabled>
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-500">Alamat Toko / Gudang</label>
            <textarea rows="3"
              class="mt-1 w-full rounded-md border px-3 py-2 text-sm bg-gray-100"
              disabled>{{ $penjual->alamat_toko }}</textarea>
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-500">Rekening Penjual</label>
            <input class="mt-1 w-full rounded-md border px-3 py-2 text-sm bg-gray-100"
                   value="{{ $penjual->rekening }}" disabled>
          </div>

          <div>
            <label class="block text-xs font-medium text-gray-500">Nama Pemilik Rekening</label>
            <input class="mt-1 w-full rounded-md border px-3 py-2 text-sm bg-gray-100"
                   value="{{ $penjual->nama_rekening }}" disabled>
          </div>

          @if(!empty($penjual->kartu_identitas ?? null))
            <div>
              <label class="block text-xs font-medium text-gray-500">Kartu Identitas</label>
              <img src="{{ asset('storage/'.$penjual->kartu_identitas) }}"
                   class="mt-2 max-h-64 rounded-md border">
            </div>
          @endif
        </div>
      @endif

    {{-- FORM DAFTAR --}}
    @else
      <div class="mt-4 bg-white border rounded-xl p-4 sm:p-6">

        @if($user->seller_status === 'rejected')
          <div class="mb-4 rounded-md bg-red-50 text-red-800 px-4 py-2 text-sm">
            Permintaanmu sebelumnya untuk menjadi penjual <b>ditolak</b>.
            Silakan perbaiki data dan ajukan ulang.
          </div>
        @endif

        @if($errors->any())
          <div class="mb-4 rounded-md bg-red-50 text-red-800 px-4 py-2 text-sm">
            {{ $errors->first() }}
          </div>
        @endif

        <form method="POST"
              action="{{ route('penjual.daftar.submit') }}"
              enctype="multipart/form-data"
              class="space-y-4">
          @csrf

          <div>
            <label class="block text-sm font-medium text-gray-700">Nama Toko</label>
            <input name="nama_toko" required
              value="{{ old('nama_toko', $penjual->nama_toko ?? '') }}"
              class="mt-1 w-full rounded-md border px-3 py-2 text-sm">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Alamat Toko / Gudang</label>
            <textarea name="alamat_toko" rows="3" required
              class="mt-1 w-full rounded-md border px-3 py-2 text-sm">{{ old('alamat_toko', $penjual->alamat_toko ?? '') }}</textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Kartu Identitas</label>
            <input type="file" name="kartu_identitas" required
              class="mt-1 w-full rounded-md border px-3 py-2 text-sm">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Rekening Penjual</label>
            <input name="rekening" required
              value="{{ old('rekening', $penjual->rekening ?? '') }}"
              class="mt-1 w-full rounded-md border px-3 py-2 text-sm">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Nama Pemilik Rekening</label>
            <input name="nama_rekening" required
              value="{{ old('nama_rekening', $penjual->nama_rekening ?? $user->name) }}"
              class="mt-1 w-full rounded-md border px-3 py-2 text-sm">
          </div>

          <div class="flex justify-between pt-4 border-t">
            <a href="{{ route('pembeli.profile') }}"
               class="text-sm text-gray-500 hover:text-gray-700">
              ← Kembali ke profil
            </a>

            <button type="submit"
              class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 text-sm">
              Kirim Permintaan Jadi Penjual
            </button>
          </div>
        </form>
      </div>
    @endif

  </div>

</body>
</html>
