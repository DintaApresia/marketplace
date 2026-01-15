<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nilai Produk</title>
  @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">

{{-- HEADER --}}
<div class="sticky top-0 z-40 bg-white border-b">
  <div class="max-w-5xl mx-auto px-4 py-3 flex items-center gap-3">
    <a href="{{ url('/pembeli/orders') }}"
       class="text-gray-600 hover:text-gray-900 text-xl">
      ←
    </a>
    <h1 class="text-lg font-semibold text-gray-800">
      Nilai Produk
    </h1>
  </div>
</div>

{{-- CONTENT --}}
<div class="max-w-5xl mx-auto px-4 py-6 space-y-4">

  {{-- INFO PESANAN --}}
  <div class="bg-white border rounded-2xl p-4">
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="text-sm text-gray-500">Pesanan</p>
        <h2 class="text-lg font-semibold">#{{ $order->id }}</h2>
        <p class="text-sm text-gray-600">
          {{ optional($order->updated_at)->format('d M Y, H:i') }}
        </p>
      </div>

      <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
        Selesai
      </span>
    </div>

    {{-- RINGKASAN --}}
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div class="border rounded-xl p-3">
        <p class="text-xs text-gray-500">Total Bayar</p>
        <p class="font-semibold">
          Rp {{ number_format($order->total_bayar ?? $order->total ?? 0, 0, ',', '.') }}
        </p>
      </div>

      <div class="border rounded-xl p-3">
        <p class="text-xs text-gray-500">Tanggal</p>
        <p class="font-semibold">
          {{ optional($order->updated_at)->format('d M Y, H:i') }}
        </p>
      </div>

      <div class="border rounded-xl p-3">
        <p class="text-xs text-gray-500">Metode Bayar</p>
        <p class="font-semibold">
          {{ $order->metode_pembayaran ?? '-' }}
        </p>
      </div>
    </div>

    <p class="mt-4 text-sm text-gray-600">
      Silakan beri rating untuk setiap produk di pesanan ini.
    </p>
  </div>

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

  {{-- LIST PRODUK --}}
  <div class="bg-white border rounded-2xl overflow-hidden">
    <div class="p-4 border-b">
      <h2 class="font-semibold">Produk di Pesanan</h2>
      <p class="text-sm text-gray-600">Beri rating, ulasan, dan foto (opsional).</p>
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
            @endif
          </div>

          {{-- INFO --}}
          <div class="flex-1">
            <div class="flex justify-between gap-3">
              <div>
                <p class="font-semibold">{{ $p->nama_barang ?? 'Produk' }}</p>
                <p class="text-sm text-gray-600">
                  Qty {{ $item->jumlah }} ·
                  Rp {{ number_format($item->harga_satuan,0,',','.') }}
                </p>
              </div>

              @if($already)
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

            {{-- FORM RATING --}}
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
                  {{-- tampil saja --}}
                  <select disabled
                          class="border rounded-xl px-3 py-2 text-sm w-full sm:w-56 bg-gray-100 cursor-not-allowed">
                    @for($i=5; $i>=1; $i--)
                      <option value="{{ $i }}" @selected($already->rating == $i)>
                        {{ $i }} -
                        {{ $i==5?'Sangat puas':($i==4?'Puas':($i==3?'Cukup':($i==2?'Kurang':'Buruk'))) }}
                      </option>
                    @endfor
                  </select>

                  {{-- supaya nilai tetap ada (kalau diperlukan) --}}
                  <input type="hidden" name="rating" value="{{ $already->rating }}">
                @else
                  {{-- editable --}}
                  <select name="rating"
                          class="border rounded-xl px-3 py-2 text-sm w-full sm:w-56">
                    @for($i=5; $i>=1; $i--)
                      <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                  </select>
                @endif
              </div>


              {{-- -Ulasan --}}
              <div class="flex flex-col sm:flex-row sm:items-start gap-2">
                <label class="text-sm text-gray-700 w-20 pt-2">Ulasan</label>

                <textarea name="review"
                rows="2"
                class="border rounded-xl px-3 py-2 text-sm w-full
                      text-left leading-relaxed
                      {{ $already ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                placeholder="Tulis ulasan (opsional)"
                {{ $already ? 'readonly' : '' }}>{{ old('review', $already->review ?? '') }}</textarea>
              </div>

              {{-- FOTO REVIEW (SELALU TAMPIL JIKA ADA FOTO) --}}
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

              {{-- INPUT UPLOAD FOTO (HANYA JIKA BELUM ADA REVIEW) --}}
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

                    <p class="text-xs text-gray-500">
                      Bisa pilih foto. (jpg/png/webp)
                    </p>
                  </div>
                </div>
              @endif

              {{-- SUBMIT --}}
              @if(!$already)
                <div class="flex justify-end">
                  <button type="submit"
                          class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm hover:bg-blue-700">
                    Kirim Review
                  </button>
                </div>
              @endif


              {{-- Errors --}}
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
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

{{-- MODAL EDIT REVIEW --}}
<div id="editReviewModal"
class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4">

  <div class="bg-white w-full max-w-lg rounded-2xl shadow-lg">
    <div class="p-4 border-b flex justify-between items-center">
      <h3 class="font-semibold text-gray-800">Edit Review Produk</h3>
      <button onclick="closeEditReview()" class="text-gray-500 hover:text-gray-700">✕</button>
    </div>
    
    <form id="editReviewForm"
      method="POST"
      enctype="multipart/form-data"
      class="p-4 space-y-3">
      @csrf
      @method('PUT')
      
      <input type="hidden" name="produk_id" id="edit_produk_id">
      {{-- Rating --}}
      <div>
        <label class="text-sm text-gray-700">Rating</label>
        <select name="rating"
          id="edit_rating"
          class="w-full mt-1 border rounded-xl px-3 py-2 text-sm">
        @for($i=5; $i>=1; $i--)
          <option value="{{ $i }}">{{ $i }}</option>
        @endfor
        </select>
      </div>

      {{-- Review --}}
      <div>
        <label class="text-sm text-gray-700">Ulasan</label>
        <textarea name="review"
          id="edit_review"
          rows="3"
          class="w-full mt-1 border rounded-xl px-3 py-2 text-sm"></textarea>
      </div>

      {{-- Gambar lama --}}
      <div>
        <label class="text-sm text-gray-700">Foto Review</label>
        <div id="edit_old_images" class="mt-2 flex flex-wrap gap-2"></div>
        <p class="text-xs text-gray-500 mt-1">
          Centang untuk menghapus foto
        </p>
      </div>

      {{-- Upload gambar baru --}}
      <div>
        <input type="file"
              name="review_images[]"
              multiple
              accept="image/*"
              class="block w-full text-sm file:mr-3 file:px-4 file:py-2 file:rounded-xl file:border-0 file:bg-gray-100">
      </div>

        <div class="flex justify-end gap-2 pt-3">
          <button type="button"
                onclick="closeEditReview()"
                class="px-4 py-2 rounded-xl border text-sm">
            Batal
          </button>
          <button type="submit"
              class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm">
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

    document.getElementById('edit_produk_id').value = produkId;
    document.getElementById('edit_rating').value = review.rating;
    document.getElementById('edit_review').value = review.review ?? '';

    // set action
    document.getElementById('editReviewForm').action =
      "{{ route('pembeli.orders.rating.update', $order->id) }}";

    // tampilkan gambar lama
    const imgBox = document.getElementById('edit_old_images');
    imgBox.innerHTML = '';

    if (review.review_images) {
      review.review_images.forEach((img, idx) => {
        imgBox.innerHTML += `
          <label class="relative group">
            <img src="/storage/${img}"
                 class="w-16 h-16 rounded-xl object-cover border">
            <input type="checkbox"
                   name="delete_images[]"
                   value="${img}"
                   class="absolute top-1 right-1">
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
