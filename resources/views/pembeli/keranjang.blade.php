@extends('layouts.pembeli')
@section('title','Keranjang')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
  <div id="toast" class="fixed top-5 right-5 z-50 hidden"></div>

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

  <h1 class="text-lg font-semibold mb-2">Keranjang</h1>
  <a href="{{ route('pembeli.dashboard') }}"
     class="text-xs sm:text-sm text-gray-500 hover:text-gray-700">
    ← Kembali ke halaman utama
  </a>

  @if(empty($cart))
    <p class="text-sm text-gray-500 mt-4">Keranjang masih kosong.</p>
  @else

  {{-- ================= FORM CHECKOUT ================= --}}
  <form method="GET" action="{{ route('pembeli.checkout') }}" id="form-checkout">
    @csrf

    <div class="space-y-3 mt-4">
      @foreach($cart as $item)

      <div class="bg-white border rounded-lg p-3 flex gap-3 items-center cart-item"
           data-id="{{ $item['id'] }}"
           data-harga="{{ $item['harga'] }}"
           data-qty="{{ $item['qty'] }}"
           data-stok="{{ $item['stok'] }}">

        {{-- CHECKLIST --}}
        <input type="checkbox"
               name="checkout_items[]"
               value="{{ $item['id'] }}"
               class="cart-check w-4 h-4 accent-green-600"
               checked>

        {{-- GAMBAR --}}
        <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden shrink-0">
          @if($item['gambar'])
            <img src="{{ asset('storage/'.$item['gambar']) }}"
                 class="w-full h-full object-cover" />
          @endif
        </div>

        {{-- INFO --}}
        <div class="flex-1">
          <div class="text-sm font-medium">{{ $item['nama'] }}</div>
          <div class="text-sm text-green-700 font-semibold">
            Rp {{ number_format($item['harga'],0,',','.') }}
          </div>

          {{-- QTY --}}
          <div class="mt-2 flex items-center gap-2 flex-wrap">
            <button type="button"
                    class="qty-btn minus w-7 h-7 border rounded hover:bg-gray-100">−</button>

            <span class="w-6 text-center text-sm qty-text">{{ $item['qty'] }}</span>

            <button type="button"
                    class="qty-btn plus w-7 h-7 border rounded hover:bg-gray-100">+</button>

            {{-- STOK --}}
            <div class="text-xs text-gray-500">
              Sisa stok: <span class="stok-text">{{ $item['stok'] }}</span>

              <span class="stok-badge {{ $item['stok']==1 ? '' : 'hidden' }}
                    ml-2 px-2 py-0.5 rounded bg-orange-100 text-orange-700">
                Stok terakhir
              </span>

              <span class="stok-low {{ in_array($item['stok'],[2,3]) ? '' : 'hidden' }}
                    ml-2 px-2 py-0.5 rounded bg-yellow-100 text-yellow-700">
                Hampir habis
              </span>
            </div>

            {{-- HAPUS (BUKAN FORM) --}}
            <button type="button"
                    class="hapus-btn text-xs px-2 py-1 rounded border
                           text-red-600 hover:bg-red-50"
                    data-id="{{ $item['id'] }}">
              Hapus
            </button>
          </div>
        </div>

        {{-- SUBTOTAL --}}
        <div class="text-sm font-semibold whitespace-nowrap item-subtotal">
          Rp {{ number_format($item['harga'] * $item['qty'],0,',','.') }}
        </div>
      </div>

      @endforeach
    </div>

    {{-- TOTAL TERPILIH --}}
    <div class="mt-4 flex items-center justify-between bg-white border rounded-lg p-4">
      <div class="text-sm text-gray-600">
        Total Terpilih (<span id="selectedCount">0</span> item)
      </div>
      <div class="text-lg font-bold text-green-700" id="selectedTotal">
        Rp 0
      </div>
    </div>

    <button type="submit"
      class="mt-3 w-full bg-green-600 hover:bg-green-700
             text-white py-2 rounded-md text-sm">
      Checkout Produk Terpilih
    </button>
  </form>

  {{-- FORM HAPUS TERSEMBUNYI --}}
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

{{-- ================= JAVASCRIPT ================= --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
  const rupiah = n => 'Rp ' + (n||0).toLocaleString('id-ID');

  const toast = (msg, ok=true) => {
    const t = document.getElementById('toast');
    t.innerHTML = `
      <div class="${ok?'bg-green-600':'bg-red-600'}
           text-white text-sm px-4 py-3 rounded shadow">
        ${msg}
      </div>`;
    t.classList.remove('hidden');
    setTimeout(()=>t.classList.add('hidden'),2000);
  };

  const hitungTotal = () => {
    let total=0, count=0;
    document.querySelectorAll('.cart-item').forEach(item=>{
      const chk = item.querySelector('.cart-check');
      if(!chk || !chk.checked) return;
      total += Number(item.dataset.harga)*Number(item.dataset.qty);
      count++;
    });
    document.getElementById('selectedTotal').textContent = rupiah(total);
    document.getElementById('selectedCount').textContent = count;
  };

  const syncBtn = item => {
    const q = Number(item.dataset.qty);
    const s = Number(item.dataset.stok);
    item.querySelector('.minus').disabled = q<=1;
    item.querySelector('.plus').disabled  = q>=s;
  };

  const updateQty = async (item, action) => {
    try{
      const id = item.dataset.id;
      const res = await fetch(`/pembeli/keranjang/${id}/ajax`,{
        method:'PATCH',
        headers:{
          'Content-Type':'application/json',
          'X-CSRF-TOKEN':csrf,
          'X-Requested-With':'XMLHttpRequest'
        },
        body:JSON.stringify({action})
      });
      const data = await res.json();
      if(!data.ok) return toast(data.message,false);

      item.dataset.qty = data.qty;
      item.dataset.stok = data.stok;
      item.querySelector('.qty-text').textContent = data.qty;
      item.querySelector('.item-subtotal').textContent = rupiah(data.itemSubtotal);
      item.querySelector('.stok-text').textContent = data.stok;

      syncBtn(item);
      hitungTotal();
    }catch(e){
      toast('Gagal update qty',false);
    }
  };

  document.querySelectorAll('.cart-item').forEach(item=>{
    item.querySelector('.plus').onclick  = ()=>updateQty(item,'plus');
    item.querySelector('.minus').onclick = ()=>updateQty(item,'minus');
    item.querySelector('.cart-check').onchange = hitungTotal;
    item.querySelector('.hapus-btn').onclick = ()=>{
      document.getElementById('hapus-'+item.dataset.id).submit();
    };
    syncBtn(item);
  });

  document.getElementById('form-checkout').addEventListener('submit',e=>{
    if(![...document.querySelectorAll('.cart-check')].some(c=>c.checked)){
      e.preventDefault();
      toast('Pilih minimal 1 produk',false);
    }
  });

  hitungTotal();
});
</script>
@endsection
