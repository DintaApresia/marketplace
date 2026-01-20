@extends('layouts.pembeli')
@section('title','Keranjang')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
  <div id="toast" class="fixed top-5 right-5 z-50 hidden"></div>

  {{-- FLASH --}}
  @if(session('success'))
    <div class="mb-3 p-3 rounded bg-green-50 text-green-800 text-sm border border-green-100">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="mb-3 p-3 rounded bg-red-50 text-red-700 text-sm border border-red-100">
      {{ session('error') }}
    </div>
  @endif

  {{-- ALERT PROFIL BELUM LENGKAP --}}
  @if(!$profilLengkap)
    <div class="mb-4 p-4 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
      ⚠️ Untuk melanjutkan ke checkout, silakan lengkapi data profil terlebih dahulu.
      <a href="{{ route('pembeli.profile') }}" class="underline font-medium ml-1">
        Lengkapi Profil
      </a>
    </div>
  @endif

  <h1 class="text-lg font-semibold mb-2">Keranjang</h1>
  <a href="{{ route('pembeli.dashboard') }}"
     class="text-xs sm:text-sm text-gray-500 hover:text-gray-700">
    ← Kembali ke halaman utama
  </a>

  @if(empty($cart))
    <p class="text-sm text-gray-500 mt-4">Keranjang masih kosong.</p>
  @else

  {{-- FORM CHECKOUT --}}
  <form method="GET"
        action="{{ route('pembeli.checkout') }}"
        id="form-checkout"
        data-profil-lengkap="{{ $profilLengkap ? '1' : '0' }}">

    <div class="space-y-3 mt-4">
      @foreach($cart as $item)
      <div class="bg-white border rounded-lg p-3 flex gap-3 items-center cart-item"
           data-id="{{ $item['id'] }}"
           data-penjual="{{ $item['penjual_id'] }}"
           data-harga="{{ $item['harga'] }}"
           data-qty="{{ $item['qty'] }}"
           data-stok="{{ $item['stok'] }}">

        <input
          type="checkbox"
          name="items[]"
          value="{{ $item['id'] }}"
          class="cart-check w-4 h-4 accent-green-600">

        <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden shrink-0">
          @if($item['gambar'])
            <img src="{{ asset('storage/'.$item['gambar']) }}"
                 class="w-full h-full object-cover">
          @endif
        </div>

        <div class="flex-1 space-y-0.5">

          <div class="text-sm text-gray-600 font-medium">
            🏪 {{ $item['nama_penjual'] }}
          </div>

          <div class="text-sm font-medium text-gray-800">
            {{ $item['nama'] }}
          </div>

          <div class="text-sm font-semibold text-green-700">
            Rp {{ number_format($item['harga'],0,',','.') }}
          </div>

          <div class="mt-2 flex items-center gap-2 flex-wrap">
            <button type="button" class="qty-btn minus w-7 h-7 border rounded">−</button>
            <span class="w-6 text-center text-sm qty-text">{{ $item['qty'] }}</span>
            <button type="button" class="qty-btn plus w-7 h-7 border rounded">+</button>

            <div class="text-xs text-gray-500">
              Sisa stok: <span class="stok-text">{{ $item['stok'] }}</span>
            </div>
        
            {{-- 🔥 Hampir habis --}}
            @if($item['stok'] < 5)
              <div class="text-[11px] px-2 py-0.5 rounded-full
                          bg-red-50 text-red-600 border border-red-200">
                ⚠ Hampir habis
              </div>
            @endif
            <button type="button"
                    class="hapus-btn text-xs px-2 py-1 rounded border text-red-600"
                    data-id="{{ $item['id'] }}">
              Hapus
            </button>
          </div>
        </div>

        <div class="text-sm font-semibold whitespace-nowrap item-subtotal">
          Rp {{ number_format($item['harga'] * $item['qty'],0,',','.') }}
        </div>
      </div>
      @endforeach
    </div>

    {{-- TOTAL --}}
    <div class="mt-4 flex items-center justify-between bg-white border rounded-lg p-4">
      <div class="text-sm text-gray-600">
        Total Terpilih (<span id="selectedCount">0</span> item)
      </div>
      <div class="text-lg font-bold text-green-700" id="selectedTotal">
        Rp 0
      </div>
    </div>

    <button type="submit"
      class="mt-3 w-full py-2 rounded-md text-sm text-white
      {{ $profilLengkap ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed' }}"
      {{ !$profilLengkap ? 'disabled' : '' }}>
      Checkout Produk Terpilih
    </button>
  </form>

  {{-- FORM HAPUS --}}
  @foreach($cart as $item)
    <form id="hapus-{{ $item['id'] }}"
          method="POST"
          action="{{ route('pembeli.keranjang.hapus', $item['id']) }}"
          class="hidden">
      @csrf
      @method('DELETE')
    </form>
  @endforeach

  @endif
</div>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

  /* =====================
   * UTIL
   * ===================== */
  const rupiah = n => 'Rp ' + (n || 0).toLocaleString('id-ID');
  const toast  = m => alert(m);

  const selectedTotal = document.getElementById('selectedTotal');
  const selectedCount = document.getElementById('selectedCount');
  const form          = document.getElementById('form-checkout');

  /* =====================
   * HITUNG TOTAL TERPILIH
   * ===================== */
  const hitungTotal = () => {
    let total = 0, count = 0;

    document.querySelectorAll('.cart-item').forEach(item => {
      const check = item.querySelector('.cart-check');
      if (check.checked) {
        total += Number(item.dataset.harga) * Number(item.dataset.qty);
        count++;
      }
    });

    if (selectedTotal) selectedTotal.textContent = rupiah(total);
    if (selectedCount) selectedCount.textContent = count;
  };

  /* =====================
   * CHECKBOX CHECKOUT
   * ===================== */
  document.querySelectorAll('.cart-check').forEach(check => {
    check.addEventListener('change', hitungTotal);
  });

  /* =====================
   * HAPUS ITEM
   * ===================== */
  document.querySelectorAll('.hapus-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;
      if (confirm('Hapus produk dari keranjang?')) {
        const formHapus = document.getElementById(`hapus-${id}`);
        if (formHapus) formHapus.submit();
      }
    });
  });

  /* =====================
   * VALIDASI SUBMIT
   * ===================== */
  if (form) {
    form.addEventListener('submit', e => {
      const profilLengkap = form.dataset.profilLengkap === '1';

      if (!profilLengkap) {
        e.preventDefault();
        toast('Lengkapi data profil terlebih dahulu.');
        return;
      }

      const checked = document.querySelectorAll('.cart-check:checked');
      if (!checked.length) {
        e.preventDefault();
        toast('Pilih minimal 1 produk.');
        return;
      }

      // VALIDASI 1 TOKO
      const penjualSet = new Set();
      checked.forEach(c => {
        const item = c.closest('.cart-item');
        penjualSet.add(item.dataset.penjual);
      });

      if (penjualSet.size > 1) {
        e.preventDefault();
        toast('Checkout hanya bisa dilakukan dari satu toko.');
      }
    });
  }

  /* =====================
   * TOMBOL MINUS
   * ===================== */
  document.querySelectorAll('.qty-btn.minus').forEach(btn => {
    btn.addEventListener('click', () => {
      const item  = btn.closest('.cart-item');
      const qtyEl = item.querySelector('.qty-text');
      const qty   = Number(qtyEl.textContent.trim());
      const id    = item.dataset.id;

      // qty = 1 → konfirmasi hapus
      if (qty === 1) {
        if (confirm('Anda yakin akan menghapus produk ini dari keranjang?')) {
          const formHapus = document.getElementById(`hapus-${id}`);
          if (formHapus) formHapus.submit();
        }
        return;
      }

      // qty > 1 → kurangi
      const newQty = qty - 1;
      qtyEl.textContent = newQty;
      item.dataset.qty  = newQty;

      const harga = Number(item.dataset.harga);
      const subtotalEl = item.querySelector('.item-subtotal');
      if (subtotalEl) {
        subtotalEl.textContent =
          'Rp ' + (newQty * harga).toLocaleString('id-ID');
      }

      hitungTotal();
    });
  });

  /* =====================
   * TOMBOL PLUS
   * ===================== */
  document.querySelectorAll('.qty-btn.plus').forEach(btn => {
    btn.addEventListener('click', () => {
      const item  = btn.closest('.cart-item');
      const qtyEl = item.querySelector('.qty-text');
      const qty   = Number(qtyEl.textContent.trim());
      const stok  = Number(item.dataset.stok);
      const harga = Number(item.dataset.harga);

      // qty >= stok → stop
      if (qty >= stok) {
        alert('Jumlah sudah mencapai stok maksimum.');
        return;
      }

      // tambah qty
      const newQty = qty + 1;
      qtyEl.textContent = newQty;
      item.dataset.qty  = newQty;

      const subtotalEl = item.querySelector('.item-subtotal');
      if (subtotalEl) {
        subtotalEl.textContent =
          'Rp ' + (newQty * harga).toLocaleString('id-ID');
      }

      hitungTotal();
    });
  });

  // inisialisasi awal
  hitungTotal();

});
</script>


@endsection
