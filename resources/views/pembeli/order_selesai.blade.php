<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Pesanan</title>
  @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">

@php
  $status = $order->status_pesanan ?? 'menunggu';

  $badge = match($status) {
    'menunggu','dikemas' => 'bg-yellow-100 text-yellow-800',
    'dikirim'            => 'bg-blue-100 text-blue-800',
    'selesai'            => 'bg-green-100 text-green-800',
    'ditolak'            => 'bg-red-100 text-red-800',
    default              => 'bg-gray-100 text-gray-800',
  };

  $logs = $order->statusLogs ?? collect();

  // timestamp per status (pertama kali muncul)
  $tsMenunggu = optional($logs->firstWhere('status','menunggu'))->created_at;
  $tsDikemas  = optional($logs->firstWhere('status','dikemas'))->created_at;
  $tsDikirim  = optional($logs->firstWhere('status','dikirim'))->created_at;
  $tsSelesai  = optional($logs->firstWhere('status','selesai'))->created_at;
  $tsDitolak  = optional($logs->firstWhere('status','ditolak'))->created_at;

  $firstItem = optional($order->items)->first();
  $penjual = optional(optional($firstItem)->produk)->penjual;

  $rated = $rated ?? collect();
@endphp

{{-- HEADER --}}
<div class="sticky top-0 z-40 bg-white border-b">
  <div class="max-w-5xl mx-auto px-4 py-3 flex items-center gap-3">
    <a href="{{ url('/pembeli/orders') }}"
       class="text-gray-600 hover:text-gray-900 text-xl">
      ←
    </a>

    <div class="flex-1">
      <h1 class="text-lg font-semibold text-gray-800">Detail Pesanan</h1>
      <p class="text-xs text-gray-500">ORD-{{ $order->id }}</p>
    </div>

    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium {{ $badge }}">
      {{ ucwords(str_replace('_',' ', $status)) }}
    </span>
  </div>
</div>

<div class="max-w-5xl mx-auto px-4 py-6 space-y-4">

  {{-- FLASH --}}
  @if(session('success'))
    <div class="p-3 rounded-xl bg-green-50 text-green-800 text-sm border">
      {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="p-3 rounded-xl bg-red-50 text-red-800 text-sm border">
      {{ session('error') }}
    </div>
  @endif

  {{-- INFO PESANAN --}}
  <div class="bg-white border rounded-2xl p-4">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
      <div>
        <p class="text-sm text-gray-500">Pesanan</p>
        <h2 class="text-lg font-semibold text-gray-900">#{{ $order->id }}</h2>

        <div class="mt-1 text-sm text-gray-600 space-y-0.5">
          <div>
            <span class="text-gray-500">Tanggal dibuat:</span>
            <span class="font-medium">{{ optional($order->created_at)->format('d M Y, H:i') }}</span>
          </div>
          <div>
            <span class="text-gray-500">Terakhir update:</span>
            <span class="font-medium">{{ optional($order->updated_at)->format('d M Y, H:i') }}</span>
          </div>
          @if($penjual)
            <div>
              <span class="text-gray-500">Penjual:</span>
              <span class="font-medium">{{ $penjual->nama_toko ?? $penjual->nama_penjual ?? '-' }}</span>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- RINGKASAN --}}
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div class="border rounded-xl p-3">
        <p class="text-xs text-gray-500">Subtotal</p>
        <p class="font-semibold text-gray-900">
          Rp {{ number_format((int)($order->subtotal ?? 0), 0, ',', '.') }}
        </p>
      </div>

      <div class="border rounded-xl p-3">
        <p class="text-xs text-gray-500">Ongkir</p>
        <p class="font-semibold text-gray-900">
          Rp {{ number_format((int)($order->ongkir ?? 0), 0, ',', '.') }}
        </p>
      </div>

      <div class="border rounded-xl p-3">
        <p class="text-xs text-gray-500">Total Bayar</p>
        <p class="font-semibold text-gray-900">
          Rp {{ number_format((int)($order->total_bayar ?? $order->total ?? 0), 0, ',', '.') }}
        </p>
      </div>

      <div class="border rounded-xl p-3">
        <p class="text-xs text-gray-500">Metode Bayar</p>
        <p class="font-semibold text-gray-900">
          {{ $order->metode_pembayaran ?? '-' }}
        </p>
      </div>

      <div class="border rounded-xl p-3">
        <p class="text-xs text-gray-500">Status Pembayaran</p>
        <p class="font-semibold text-gray-900">
          {{ ucwords(str_replace('_',' ', $order->status_pembayaran ?? '-')) }}
        </p>
      </div>

      <div class="border rounded-xl p-3">
        <p class="text-xs text-gray-500">Catatan</p>
        <p class="font-semibold text-gray-900 truncate">
          {{ $order->catatan ?: '—' }}
        </p>
      </div>
    </div>
  </div>

  {{-- STATUS + TIMESTAMP + TIMELINE --}}
  <div class="bg-white border rounded-2xl p-4">
    <h2 class="font-semibold text-gray-800 mb-3">Status Pesanan & Waktu</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
      <div class="flex justify-between border rounded-xl p-3">
        <span class="text-gray-600">Menunggu</span>
        <span class="font-semibold text-gray-900">
          {{ $tsMenunggu ? $tsMenunggu->format('d M Y, H:i') : '—' }}
        </span>
      </div>

      <div class="flex justify-between border rounded-xl p-3">
        <span class="text-gray-600">Dikemas</span>
        <span class="font-semibold text-gray-900">
          {{ $tsDikemas ? $tsDikemas->format('d M Y, H:i') : '—' }}
        </span>
      </div>

      <div class="flex justify-between border rounded-xl p-3">
        <span class="text-gray-600">Dikirim</span>
        <span class="font-semibold text-gray-900">
          {{ $tsDikirim ? $tsDikirim->format('d M Y, H:i') : '—' }}
        </span>
      </div>

      <div class="flex justify-between border rounded-xl p-3">
        <span class="text-gray-600">Selesai</span>
        <span class="font-semibold text-gray-900">
          {{ $tsSelesai ? $tsSelesai->format('d M Y, H:i') : '—' }}
        </span>
      </div>

      <div class="flex justify-between border rounded-xl p-3 sm:col-span-2">
        <span class="text-gray-600">Ditolak</span>
        <span class="font-semibold text-gray-900">
          {{ $tsDitolak ? $tsDitolak->format('d M Y, H:i') : '—' }}
        </span>
      </div>
    </div>

    <div class="mt-4">
      <h3 class="text-sm font-semibold text-gray-800 mb-2">Timeline</h3>
      @if($logs->isEmpty())
        <div class="text-sm text-gray-500">Belum ada riwayat status.</div>
      @else
        <ol class="space-y-2">
          @foreach($logs as $log)
            <li class="flex items-start gap-3">
              <div class="mt-1 w-2.5 h-2.5 rounded-full bg-gray-400"></div>
              <div class="text-sm">
                <div class="font-medium text-gray-800">
                  {{ ucwords($log->status) }}
                  <span class="text-xs text-gray-500">• {{ $log->created_at->format('d M Y, H:i') }}</span>
                </div>

                @if($log->actor_role)
                  <div class="text-xs text-gray-400">oleh {{ $log->actor_role }}</div>
                @endif

                @if($log->catatan)
                  <div class="text-xs text-gray-500">{{ $log->catatan }}</div>
                @endif
              </div>
            </li>
          @endforeach
        </ol>
      @endif
    </div>
  </div>

  {{-- ADUAN (SELALU BISA SUBMIT) --}}
  <div class="bg-white border rounded-2xl p-4">
    <h2 class="font-semibold text-gray-800">Aduan Pesanan</h2>
    @if(!$aduan)

      <p class="text-sm text-gray-600 mt-1">
        Kamu bisa mengajukan aduan untuk pesanan ini.
      </p>

      <form action="{{ route('pembeli.orders.aduan.store', $order->id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="mt-4 space-y-3">
        @csrf

        {{-- Judul --}}
        <div>
          <label class="text-sm font-medium text-gray-700">Judul Aduan</label>
          <input type="text"
                name="judul"
                value="{{ old('judul') }}"
                required
                class="w-full mt-1 border rounded-xl px-3 py-2 text-sm">
          @error('judul')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Deskripsi --}}
        <div>
          <label class="text-sm font-medium text-gray-700">Deskripsi</label>
          <textarea name="deskripsi"
                    rows="3"
                    required
                    class="w-full mt-1 border rounded-xl px-3 py-2 text-sm"
                    placeholder="Jelaskan masalahnya...">{{ old('deskripsi') }}</textarea>
          @error('deskripsi')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Bukti --}}
        <div>
          <label class="text-sm font-medium text-gray-700">Bukti (opsional)</label>
          <input type="file"
                name="bukti[]"
                multiple
                accept="image/*"
                class="block w-full mt-1 text-sm
                        file:mr-3 file:px-4 file:py-2
                        file:rounded-xl file:border-0
                        file:bg-gray-100 file:text-gray-700
                        hover:file:bg-gray-200">
          @error('bukti.*')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div class="flex justify-end">
          <button type="submit"
                  class="px-4 py-2 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700">
            Kirim Aduan
          </button>
        </div>
      </form>

    @else

    {{-- ===============================
        THREAD ADUAN
    =============================== --}}

      <div class="mt-4 space-y-4">

        {{-- PESAN PEMBELI --}}
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-sm">
          <div class="text-xs text-blue-600 font-semibold mb-1">
            PEMBELI • {{ $aduan->created_at->format('d M Y, H:i') }}
          </div>

          <div class="font-semibold text-gray-800">
            {{ $aduan->judul }}
          </div>

          <div class="mt-1 text-gray-700">
            {{ $aduan->deskripsi }}
          </div>

          {{-- Bukti --}}
          @if(!empty($aduan->bukti))
            @php
              $imgs = is_array($aduan->bukti)
                ? $aduan->bukti
                : json_decode($aduan->bukti, true);
            @endphp

            @if(!empty($imgs))
              <div class="mt-3 flex flex-wrap gap-2">
                @foreach($imgs as $img)
                  <a href="{{ asset('storage/'.$img) }}" target="_blank">
                    <img src="{{ asset('storage/'.$img) }}"
                        class="w-16 h-16 rounded-xl object-cover border">
                  </a>
                @endforeach
              </div>
            @endif
          @endif
        </div>

        {{-- BALASAN PENJUAL --}}
        @if($aduan->catatan_penjual)
          <div class="bg-gray-50 border rounded-2xl p-4 text-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">
              PENJUAL • {{ optional($aduan->tgl_catatan_penjual)->format('d M Y, H:i') }}
            </div>
            <div class="text-gray-800">
              {{ $aduan->catatan_penjual }}
            </div>
          </div>
        @endif

        {{-- BALASAN ADMIN --}}
        @if($aduan->catatan_admin)
          <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 text-sm">
            <div class="text-xs text-yellow-700 font-semibold mb-1">
              ADMIN • {{ optional($aduan->tgl_catatan_admin)->format('d M Y, H:i') }}
            </div>
            <div class="text-gray-800">
              {{ $aduan->catatan_admin }}
            </div>
          </div>
        @endif

        @if(!$aduan->catatan_penjual && !$aduan->catatan_admin)
          <div class="text-sm text-gray-500">
            Aduan sedang menunggu balasan dari penjual atau admin.
          </div>
        @endif
      </div>
    @endif
  </div>

  {{-- LIST PRODUK + (RATING HANYA SAAT SELESAI) --}}
  <div class="bg-white border rounded-2xl overflow-hidden">
    <div class="p-4 border-b">
      <h2 class="font-semibold">Produk di Pesanan</h2>
      <p class="text-sm text-gray-600">
        {{ $status === 'selesai'
            ? 'Silakan beri rating untuk setiap produk di pesanan ini.'
            : 'Rating akan tersedia setelah pesanan selesai.' }}
      </p>
    </div>

    <div class="divide-y">
      @foreach($order->items as $item)
        @php
          $p = $item->produk;
          $already = $rated[$item->produk_id] ?? null;
        @endphp

        <div class="p-4 flex gap-4">
          {{-- GAMBAR --}}
          <div class="w-14 h-14 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
            @if($p && $p->gambar)
              <img src="{{ asset('storage/'.$p->gambar) }}" class="w-full h-full object-cover">
            @else
              <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Img</div>
            @endif
          </div>

          {{-- INFO --}}
          <div class="flex-1">
            <div class="flex justify-between gap-3">
              <div>
                <p class="font-semibold text-gray-900">{{ $p->nama_barang ?? 'Produk' }}</p>
                <p class="text-sm text-gray-600">
                  Qty {{ $item->jumlah }} ·
                  Rp {{ number_format($item->harga_satuan ?? ($p->harga ?? 0),0,',','.') }}
                </p>
              </div>

              @if($status === 'selesai' && $already)
                <div class="text-right space-y-1">
                  <div class="text-yellow-600 text-sm">
                    {{ str_repeat('★', $already->rating) }}{{ str_repeat('☆', 5-$already->rating) }}
                  </div>
                  <button onclick="openEditReview({{ $item->produk_id }})"
                          class="text-xs text-blue-600 hover:underline">
                    Edit Review
                  </button>
                </div>
              @endif
            </div>

            {{-- FORM RATING (hanya kalau selesai) --}}
            @if($status === 'selesai')
              <form method="POST"
                    action="{{ route('pembeli.orders.rating.store', $order->id) }}"
                    enctype="multipart/form-data"
                    class="mt-3 space-y-2">
                @csrf
                <input type="hidden" name="produk_id" value="{{ $item->produk_id }}">

                {{-- Rating --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                  <label class="text-sm text-gray-700 w-20">Rating</label>

                  @if($already)
                    <select disabled
                            class="border rounded-xl px-3 py-2 text-sm w-full sm:w-56 bg-gray-100 cursor-not-allowed">
                      @for($i=5; $i>=1; $i--)
                        <option value="{{ $i }}" @selected($already->rating == $i)>
                          {{ $i }} -
                          {{ $i==5?'Sangat puas':($i==4?'Puas':($i==3?'Cukup':($i==2?'Kurang':'Buruk'))) }}
                        </option>
                      @endfor
                    </select>
                    <input type="hidden" name="rating" value="{{ $already->rating }}">
                  @else
                    <select name="rating"
                            class="border rounded-xl px-3 py-2 text-sm w-full sm:w-56">
                      @for($i=5; $i>=1; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                      @endfor
                    </select>
                  @endif
                </div>

                {{-- Ulasan --}}
                <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                  <label class="text-sm text-gray-700 w-20 pt-2">Ulasan</label>
                  <textarea name="review"
                            rows="2"
                            class="border rounded-xl px-3 py-2 text-sm w-full
                                   text-left leading-relaxed {{ $already ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                            placeholder="Tulis ulasan (opsional)"
                            {{ $already ? 'readonly' : '' }}>{{ old('review', $already->review ?? '') }}</textarea>
                </div>

                {{-- Foto lama --}}
                @if($already && !empty($already->review_images))
                  @php
                    $imgs = is_array($already->review_images)
                      ? $already->review_images
                      : json_decode($already->review_images, true);
                  @endphp
                  @if(!empty($imgs))
                    <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                      <label class="text-sm text-gray-700 w-20 pt-2">Foto</label>
                      <div class="flex flex-wrap gap-2">
                        @foreach($imgs as $img)
                          <a href="{{ asset('storage/'.$img) }}" target="_blank">
                            <img src="{{ asset('storage/'.$img) }}"
                                 class="w-16 h-16 rounded-xl object-cover border"
                                 alt="Review Image">
                          </a>
                        @endforeach
                      </div>
                    </div>
                  @endif
                @endif

                {{-- Upload foto baru --}}
                @if(!$already)
                  <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                    <label class="text-sm text-gray-700 w-20 pt-2">Foto</label>
                    <div class="w-full space-y-2">
                      <input type="file"
                             name="review_images[]"
                             multiple
                             accept="image/*"
                             class="block w-full text-sm
                                    file:mr-3 file:px-4 file:py-2
                                    file:rounded-xl file:border-0
                                    file:bg-gray-100 file:text-gray-700
                                    hover:file:bg-gray-200">
                      <p class="text-xs text-gray-500">Bisa pilih foto. (jpg/png/webp)</p>
                    </div>
                  </div>
                @endif

                {{-- Submit --}}
                @if(!$already)
                  <div class="flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm hover:bg-blue-700">
                      Kirim Review
                    </button>
                  </div>
                @endif

                @error('rating')
                  <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('review')
                  <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('review_images.*')
                  <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror
              </form>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

{{-- MODAL EDIT REVIEW --}}
<div id="editReviewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">
  <div class="bg-white w-full max-w-lg rounded-2xl shadow-lg">
    <div class="p-4 border-b flex justify-between items-center">
      <h3 class="font-semibold text-gray-800">Edit Review Produk</h3>
      <button onclick="closeEditReview()" class="text-gray-500 hover:text-gray-700">✕</button>
    </div>

    <form id="editReviewForm" method="POST" enctype="multipart/form-data" class="p-4 space-y-3">
      @csrf
      @method('PUT')

      <input type="hidden" name="produk_id" id="edit_produk_id">

      <div>
        <label class="text-sm text-gray-700">Rating</label>
        <select name="rating" id="edit_rating" class="w-full mt-1 border rounded-xl px-3 py-2 text-sm">
          @for($i=5; $i>=1; $i--)
            <option value="{{ $i }}">{{ $i }}</option>
          @endfor
        </select>
      </div>

      <div>
        <label class="text-sm text-gray-700">Ulasan</label>
        <textarea name="review" id="edit_review" rows="3"
                  class="w-full mt-1 border rounded-xl px-3 py-2 text-sm"></textarea>
      </div>

      <div>
        <label class="text-sm text-gray-700">Foto Review</label>
        <div id="edit_old_images" class="mt-2 flex flex-wrap gap-2"></div>
        <p class="text-xs text-gray-500 mt-1">Centang untuk menghapus foto</p>
      </div>

      <div>
        <input type="file" name="review_images[]" multiple accept="image/*"
               class="block w-full text-sm file:mr-3 file:px-4 file:py-2 file:rounded-xl file:border-0 file:bg-gray-100">
      </div>

      <div class="flex justify-end gap-2 pt-3">
        <button type="button" onclick="closeEditReview()" class="px-4 py-2 rounded-xl border text-sm">
          Batal
        </button>
        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  const reviews = @json($rated);

  function openEditReview(produkId) {
    const modal = document.getElementById('editReviewModal');
    const review = reviews[produkId];
    if (!review) return;

    document.getElementById('edit_produk_id').value = produkId;
    document.getElementById('edit_rating').value = review.rating;
    document.getElementById('edit_review').value = review.review ?? '';

    document.getElementById('editReviewForm').action =
      "{{ route('pembeli.orders.rating.update', $order->id) }}";

    const imgBox = document.getElementById('edit_old_images');
    imgBox.innerHTML = '';

    if (review.review_images) {
      review.review_images.forEach((img) => {
        imgBox.innerHTML += `
          <label class="relative group">
            <img src="/storage/${img}" class="w-16 h-16 rounded-xl object-cover border">
            <input type="checkbox" name="delete_images[]" value="${img}" class="absolute top-1 right-1">
          </label>
        `;
      });
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeEditReview() {
    const modal = document.getElementById('editReviewModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }
</script>

</body>
</html>
