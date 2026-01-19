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
           data-harga="{{ $item['harga'] }}"
           data-qty="{{ $item['qty'] }}"
           data-stok="{{ $item['stok'] }}">

        <input type="checkbox" class="cart-check w-4 h-4 accent-green-600">

        <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden shrink-0">
          @if($item['gambar'])
            <img src="{{ asset('storage/'.$item['gambar']) }}"
                 class="w-full h-full object-cover">
          @endif
        </div>

        <div class="flex-1">
          <div class="text-sm font-medium">{{ $item['nama'] }}</div>
          <div class="text-sm text-green-700 font-semibold">
            Rp {{ number_format($item['harga'],0,',','.') }}
          </div>

          <div class="mt-2 flex items-center gap-2 flex-wrap">
            <button type="button" class="qty-btn minus w-7 h-7 border rounded">−</button>
            <span class="w-6 text-center text-sm qty-text">{{ $item['qty'] }}</span>
            <button type="button" class="qty-btn plus w-7 h-7 border rounded">+</button>

            <div class="text-xs text-gray-500">
              Sisa stok: <span class="stok-text">{{ $item['stok'] }}</span>
            </div>

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
  const rupiah = n => 'Rp ' + (n||0).toLocaleString('id-ID');
  const toast = (m, ok=false)=>alert(m);

  const hitungTotal = () => {
    let total=0,count=0;
    document.querySelectorAll('.cart-item').forEach(i=>{
      if(i.querySelector('.cart-check').checked){
        total += i.dataset.harga * i.dataset.qty;
        count++;
      }
    });
    selectedTotal.textContent = rupiah(total);
    selectedCount.textContent = count;
  };

  document.querySelectorAll('.cart-check').forEach(c=>{
    c.onchange = hitungTotal;
  });

  document.getElementById('form-checkout').addEventListener('submit',e=>{
    const profilLengkap = e.target.dataset.profilLengkap === '1';

    if(!profilLengkap){
      e.preventDefault();
      toast('Lengkapi data profil terlebih dahulu.');
      return;
    }

    if(!document.querySelector('.cart-check:checked')){
      e.preventDefault();
      toast('Pilih minimal 1 produk.');
    }
  });

  hitungTotal();
});
</script>
@endsection
