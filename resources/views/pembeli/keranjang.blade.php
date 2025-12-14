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

    <h1 class="text-lg font-semibold mb-4">Keranjang</h1>

    @if(empty($cart))
        <p class="text-sm text-gray-500">Keranjang masih kosong.</p>
    @else
        <div class="space-y-3">
            @foreach($cart as $item)
            <div class="bg-white border rounded-lg p-3 flex gap-3 items-center cart-item"
            data-id="{{ $item['id'] }}"
            data-harga="{{ $item['harga'] }}"
            data-qty="{{ $item['qty'] }}"
            data-stok="{{ $item['stok'] ?? 999999 }}">

            {{-- CHECKLIST --}}
            <input type="checkbox"
                  class="cart-check w-4 h-4 accent-green-600"
                  checked>

            {{-- GAMBAR --}}
            <div class="w-16 h-16 bg-gray-100 rounded overflow-hidden shrink-0">
                @if($item['gambar'])
                    <img src="{{ asset('storage/'.$item['gambar']) }}" class="w-full h-full object-cover" />
                @endif
            </div>

            <div class="flex-1">
                <div class="text-sm font-medium">{{ $item['nama'] }}</div>
                <div class="text-sm text-green-700 font-semibold">
                    Rp {{ number_format($item['harga'],0,',','.') }}
                </div>

                {{-- QTY CONTROL AJAX --}}
                <div class="mt-2 flex items-center gap-2">
                    <button type="button"
                            class="qty-btn minus w-7 h-7 border rounded hover:bg-gray-100">−</button>

                    <span class="w-6 text-center text-sm qty-text">{{ $item['qty'] }}</span>

                    <button type="button"
                            class="qty-btn plus w-7 h-7 border rounded hover:bg-gray-100">+</button>

                    {{-- INFO STOK --}}
                    <div class="text-xs text-gray-500 mt-1 stok-info">
                      Sisa stok: <span class="stok-text">-</span>
                      <span class="stok-badge hidden ml-2 px-2 py-0.5 rounded bg-orange-100 text-orange-700">
                        Stok terakhir
                      </span>
                      <span class="stok-low hidden ml-2 px-2 py-0.5 rounded bg-yellow-100 text-yellow-700">
                        Hampir habis
                      </span>
                    </div>

                    {{-- Hapus tetap pakai form biasa --}}
                    <form method="POST" action="{{ route('pembeli.keranjang.hapus', $item['id']) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-xs px-2 py-1 rounded border text-red-600 hover:bg-red-50">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
            <div class="text-sm font-semibold whitespace-nowrap item-subtotal">
                Rp {{ number_format($item['harga'] * $item['qty'],0,',','.') }}
            </div>
        </div>
            @endforeach
        </div>

        <div class="mt-4 flex items-center justify-between bg-white border rounded-lg p-4">
          <div class="text-sm text-gray-600">
              Total Terpilih (<span id="selectedCount">0</span> item)
          </div>
          <div class="text-lg font-bold text-green-700" id="selectedTotal">
              Rp 0
          </div>
      </div>

        <button
          class="mt-3 w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-md text-sm">
          Checkout Produk Terpilih
      </button>

    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

  const formatRupiah = (num) => 'Rp ' + (num || 0).toLocaleString('id-ID');

  const toast = (msg, type = 'ok') => {
    const el = document.getElementById('toast');
    if (!el) { alert(msg); return; } // fallback biar pasti keliatan

    el.className = 'fixed top-5 right-5 z-50';
    el.innerHTML = `
      <div class="${type === 'ok' ? 'bg-green-600' : 'bg-red-600'} text-white text-sm px-4 py-3 rounded-lg shadow-lg flex items-center gap-2">
        <span>${type === 'ok' ? '✅' : '⚠️'}</span>
        <span>${msg}</span>
        <button class="ml-2 text-white/90 hover:text-white font-bold" onclick="this.closest('#toast').classList.add('hidden')">×</button>
      </div>`;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 2200);
  };

  const calcSelectedTotal = () => {
    let total = 0;
    let count = 0;

    document.querySelectorAll('.cart-item').forEach(item => {
      if (!item.querySelector('.cart-check')?.checked) return;
      const harga = Number(item.dataset.harga || 0);
      const qty = Number(item.dataset.qty || 0);
      total += harga * qty;
      count++;
    });

    document.getElementById('selectedTotal').textContent = formatRupiah(total);
    document.getElementById('selectedCount').textContent = count;
  };

  const syncButtons = (item) => {
    const qty = Number(item.dataset.qty || 1);
    const stok = Number(item.dataset.stok ?? 999999);

    const minus = item.querySelector('.qty-btn.minus');
    const plus  = item.querySelector('.qty-btn.plus');

    if (minus) minus.disabled = qty <= 1;
    if (plus) plus.disabled = stok <= 0 || qty >= stok;

    [minus, plus].forEach(btn => {
      if (!btn) return;
      btn.classList.toggle('opacity-50', btn.disabled);
      btn.classList.toggle('cursor-not-allowed', btn.disabled);
    });
  };

  const syncStockInfo = (item) => {
    const stok = Number(item.dataset.stok ?? 0);
    const stokText  = item.querySelector('.stok-text');
    const badgeLast = item.querySelector('.stok-badge');
    const badgeLow  = item.querySelector('.stok-low');

    if (stokText) stokText.textContent = (stok >= 0 ? stok : '-');
    if (badgeLast) badgeLast.classList.toggle('hidden', stok !== 1);
    if (badgeLow)  badgeLow.classList.toggle('hidden', !(stok === 2 || stok === 3));
  };

  const applyServerUpdate = (item, data) => {
    item.dataset.qty = data.qty;
    item.dataset.stok = data.stok;

    item.querySelector('.qty-text').textContent = data.qty;
    item.querySelector('.item-subtotal').textContent = formatRupiah(data.itemSubtotal);

    syncButtons(item);
    syncStockInfo(item);
    calcSelectedTotal();
  };

  const requestQty = async (item, action) => {
    const id = item.dataset.id;
    if (!id) return toast('data-id kosong (cek div.cart-item data-id).', 'err');
    if (!csrf) return toast('CSRF token tidak terbaca (cek meta csrf-token di <head>).', 'err');

    try {
      const res = await fetch(`{{ url('/pembeli/keranjang') }}/${id}/ajax`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest', // penting biar dianggap AJAX
        },
        body: JSON.stringify({ action })
      });

      const text = await res.text();
      let data = null;
      try { data = JSON.parse(text); } catch {}

      if (!data) {
        console.error('RESPON BUKAN JSON:', res.status, text);
        return toast(`Respon bukan JSON (status ${res.status}). Cek console.`, 'err');
      }

      if (!res.ok || !data.ok) {
        console.error('ERROR JSON:', res.status, data);
        // sync jika server kirim qty/stok
        if (data.qty != null) item.dataset.qty = data.qty;
        if (data.stok != null) item.dataset.stok = data.stok;

        syncButtons(item);
        syncStockInfo(item);
        calcSelectedTotal();

        return toast(data.message || 'Gagal update qty.', 'err');
      }

      applyServerUpdate(item, data);

    } catch (e) {
      console.error(e);
      toast('Koneksi bermasalah / fetch gagal. Cek console.', 'err');
    }
  };

  // bind checkbox
  document.querySelectorAll('.cart-check').forEach(chk => {
    chk.addEventListener('change', calcSelectedTotal);
  });

  // bind plus/minus
  document.querySelectorAll('.cart-item').forEach(item => {
    item.querySelector('.qty-btn.plus')?.addEventListener('click', () => requestQty(item, 'plus'));
    item.querySelector('.qty-btn.minus')?.addEventListener('click', () => requestQty(item, 'minus'));

    // init
    syncButtons(item);
    syncStockInfo(item);
  });

  calcSelectedTotal();
});
</script>


@endsection
