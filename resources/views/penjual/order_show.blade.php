@extends('layouts.penjual')
@section('title','Detail Pesanan')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">

  <h1 class="text-2xl font-bold mb-6 text-gray-800">
    Detail Pesanan #{{ $order->kode_order ?? ('ORD-'.$order->id) }}
  </h1>

  @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-100 text-green-800 rounded-lg p-3 text-sm">
      {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-100 text-red-800 rounded-lg p-3 text-sm">
      {{ session('error') }}
    </div>
  @endif

  {{-- INFO PEMBELI + ALAMAT --}}
  <div class="bg-white rounded-lg shadow p-5 mb-4 space-y-3">
    <div>
      <div class="text-sm text-gray-600">Pembeli</div>
      <div class="font-semibold text-gray-800">
        {{ $order->user->name ?? 'Pembeli' }}
      </div>
      <div class="text-xs text-gray-500">
        {{ $order->user->email ?? '' }}
      </div>
    </div>

    <div class="pt-3 border-t space-y-2">
      <div>
        <div class="text-sm text-gray-600">Alamat Pengiriman</div>
        <div class="text-sm text-gray-800 leading-relaxed">
          {{ $order->alamat_pengiriman ?? '-' }}
        </div>
      </div>
    </div>
  </div>

  @php
    // helper URL gambar review (support storage path / full url)
    $imgUrl = function ($path) {
      if (!$path) return null;
      return \Illuminate\Support\Str::startsWith($path, ['http://','https://'])
        ? $path
        : asset('storage/'.$path);
    };

    // Map rating per produk_id (khusus order ini + pembeli ini)
    $ratingsMap = [];
    foreach ($itemsSeller as $it) {
      if (!$it->produk) continue;

      $row = \Illuminate\Support\Facades\DB::table('produk_rating')
        ->where('order_id', $order->id)
        ->where('user_id', $order->user_id)
        ->where('produk_id', $it->produk->id)
        ->first();

      if ($row) $ratingsMap[$it->produk->id] = $row;
    }

    // =========================
    // STATUS + TIMESTAMP + LOGS
    // =========================
    $status = $order->status_pesanan ?? 'menunggu';
    $logs   = $order->statusLogs ?? collect();

    $tsMenunggu = optional($logs->firstWhere('status','menunggu'))->created_at;
    $tsDikemas  = optional($logs->firstWhere('status','dikemas'))->created_at;
    $tsDikirim  = optional($logs->firstWhere('status','dikirim'))->created_at;
    $tsSelesai  = optional($logs->firstWhere('status','selesai'))->created_at;
    $tsDitolak  = optional($logs->firstWhere('status','ditolak'))->created_at;

    $canEdit = in_array($status, ['menunggu','dikemas','dikirim']);

    $badgeClass = match($status) {
      'menunggu' => 'bg-gray-200 text-gray-800 border border-gray-300',
      'dikemas'  => 'bg-yellow-200 text-yellow-900 border border-yellow-300',
      'dikirim'  => 'bg-blue-200 text-blue-900 border border-blue-300',
      'selesai'  => 'bg-green-200 text-green-900 border border-green-300',
      'ditolak'  => 'bg-red-200 text-red-900 border border-red-300',
      default    => 'bg-gray-100 text-gray-700 border border-gray-200',
    };
  @endphp

  {{-- GRID: KIRI (detail+rating) | KANAN (status+timeline+aduan) --}}
  {{-- Dibuat lebih rapi: kanan dibuat "sticky" + scroll internal supaya tidak bikin halaman terasa kepanjangan,
      dan kiri diisi ringkasan total agar tidak terasa kosong. Fungsionalitas tidak diubah. --}}
  <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 items-start">

    {{-- =========================
        KIRI: DETAIL PRODUK + RATING
    ========================= --}}
    <div class="lg:col-span-3 space-y-4">

      {{-- RINGKASAN ORDER (biar kiri ga kosong) --}}
      @php
        $totalSeller = 0;
        foreach ($itemsSeller as $it) {
          $p = $it->produk;
          $hargaTmp  = (int) ($it->harga ?? $p?->harga ?? 0);
          $qtyTmp    = (int) ($it->jumlah ?? $it->qty ?? $it->quantity ?? 0);
          $subTmp    = (int) ($it->subtotal ?? ($hargaTmp * $qtyTmp));
          $totalSeller += $subTmp;
        }
        $metode = $order->metode_pembayaran ?? '-';
      @endphp

      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-start justify-between gap-3">
          <div>
            <div class="text-sm text-gray-600">Ringkasan Pesanan</div>
            <div class="text-xs text-gray-500">Informasi singkat untuk order ini.</div>
          </div>
          <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
            {{ ucfirst($status) }}
          </span>
        </div>

        <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
          <div class="border rounded-lg p-3">
            <div class="text-xs text-gray-500">Metode Pembayaran</div>
            <div class="font-semibold text-gray-800 mt-0.5">{{ strtoupper($metode) }}</div>
          </div>
          <div class="border rounded-lg p-3">
            <div class="text-xs text-gray-500">Jumlah Item (produk kamu)</div>
            <div class="font-semibold text-gray-800 mt-0.5">{{ $itemsSeller->count() }}</div>
          </div>
          <div class="border rounded-lg p-3">
            <div class="text-xs text-gray-500">Total (produk kamu)</div>
            <div class="font-semibold text-gray-800 mt-0.5">Rp {{ number_format($totalSeller, 0, ',', '.') }}</div>
          </div>
        </div>

        @if($order->metode_pembayaran === 'transfer')
          <div class="mt-3 border-t pt-3 flex items-center justify-between gap-3">
            <div>
              <div class="text-xs text-gray-500">Bukti Pembayaran</div>
              <div class="text-sm text-gray-700">Klik gambar untuk lihat ukuran penuh.</div>
            </div>

            @if(!empty($order->bukti_pembayaran))
              <a href="{{ asset('storage/'.$order->bukti_pembayaran) }}" target="_blank"
                 class="block w-14 h-14 rounded-xl overflow-hidden bg-gray-100 border hover:opacity-90 flex-shrink-0">
                <img src="{{ asset('storage/'.$order->bukti_pembayaran) }}"
                     class="w-full h-full object-cover" alt="Bukti Pembayaran">
              </a>
            @else
              <span class="text-xs text-red-500 italic">Belum diupload</span>
            @endif
          </div>
        @endif
      </div>

      {{-- CARD DETAIL PRODUK + RATING (JADI SATU) --}}
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-5 py-4 border-b">
          <div class="font-semibold text-gray-800">Detail Produk</div>
          <div class="text-xs text-gray-500">Item yang termasuk produk kamu.</div>
        </div>

        {{-- TABEL PRODUK --}}
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produk</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Harga</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bukti Pembayaran</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Qty</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Subtotal</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Catatan</th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              @foreach($itemsSeller as $item)
                @php
                  $produk = $item->produk;
                  $harga  = (int) ($item->harga ?? $produk?->harga ?? 0);
                  $qty    = (int) ($item->jumlah ?? $item->qty ?? $item->quantity ?? 0);
                  $sub    = (int) ($item->subtotal ?? ($harga * $qty));
                @endphp

                <tr>
                  <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                      <div class="w-12 h-12 rounded overflow-hidden bg-gray-100 flex-shrink-0">
                        @if($produk && $produk->gambar)
                          <img src="{{ asset('storage/'.$produk->gambar) }}"
                               class="w-full h-full object-cover" alt="">
                        @else
                          <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">
                            No Img
                          </div>
                        @endif
                      </div>

                      <div class="min-w-0">
                        <div class="font-medium text-gray-800 truncate">
                          {{ $produk->nama_barang ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-500">ID Produk: {{ $produk->id ?? '-' }}</div>
                      </div>
                    </div>
                  </td>

                  <td class="px-4 py-3 whitespace-nowrap">
                    Rp {{ number_format($harga, 0, ',', '.') }}
                  </td>

                  <td class="px-4 py-3">
                    @if($order->metode_pembayaran === 'transfer')
                      @if(!empty($order->bukti_pembayaran))
                        <a href="{{ asset('storage/'.$order->bukti_pembayaran) }}" target="_blank"
                          class="block w-12 h-12 rounded overflow-hidden bg-gray-100 border hover:opacity-90">
                          <img
                            src="{{ asset('storage/'.$order->bukti_pembayaran) }}"
                            class="w-full h-full object-cover"
                            alt="Bukti Pembayaran">
                        </a>
                      @else
                        <span class="text-xs text-red-500 italic">Belum diupload</span>
                      @endif
                    @else
                      <span class="text-xs text-gray-400 italic">COD</span>
                    @endif
                  </td>

                  <td class="px-4 py-3 whitespace-nowrap">
                    {{ $qty }}
                  </td>

                  <td class="px-4 py-3 whitespace-nowrap font-semibold">
                    Rp {{ number_format($sub, 0, ',', '.') }}
                  </td>

                  {{-- CATATAN --}}
                  <td class="px-4 py-3 max-w-xs">
                    @if(!empty($order->catatan))
                      <div class="text-sm text-gray-700 break-words">
                        {{ $order->catatan }}
                      </div>
                    @else
                      <span class="text-xs text-gray-400 italic">
                        Tidak ada catatan
                      </span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- SECTION RATING DI BAWAH TABEL (MASIH DALAM CARD YANG SAMA) --}}
        <div class="border-t">
          <div class="px-5 py-4">
            <div class="font-semibold text-gray-800">Rating & Ulasan</div>
            <div class="text-xs text-gray-500">Penilaian pembeli untuk order ini.</div>
          </div>

          <div class="px-5 pb-5 space-y-4">
            @if(count($ratingsMap) === 0)
              <div class="text-center text-sm text-gray-500 py-6">
                Belum ada rating untuk pesanan ini.
              </div>
            @else
              @foreach($itemsSeller as $item)
                @php
                  $produk = $item->produk;
                  if (!$produk) continue;

                  $r = $ratingsMap[$produk->id] ?? null;

                  $imgs = [];
                  if ($r && !empty($r->review_images)) {
                    $decoded = json_decode($r->review_images, true);
                    if (is_array($decoded)) $imgs = $decoded;
                  }
                @endphp

                <div class="border rounded-lg p-4">
                  <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded overflow-hidden bg-gray-100 flex-shrink-0">
                      @if($produk->gambar)
                        <img src="{{ asset('storage/'.$produk->gambar) }}" class="w-full h-full object-cover" alt="">
                      @else
                        <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Img</div>
                      @endif
                    </div>

                    <div class="min-w-0 flex-1">
                      <div class="flex items-center justify-between gap-3">
                        <div class="font-semibold text-gray-800 truncate">
                          {{ $produk->nama_barang ?? '-' }}
                        </div>

                        @if($r)
                          <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 whitespace-nowrap">
                            {{ (int)($r->rating ?? 0) }}/5
                          </span>
                        @endif
                      </div>

                      @if(!$r)
                        <div class="text-xs text-gray-400 mt-1">Produk ini belum dinilai.</div>
                      @else
                        @if(!empty($r->review))
                          <div class="text-sm text-gray-700 mt-2">
                            “{{ $r->review }}”
                          </div>
                        @else
                          <div class="text-xs text-gray-400 mt-2">Tidak ada komentar.</div>
                        @endif

                        @if(!empty($imgs))
                          <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                            @foreach($imgs as $path)
                              @php $url = $imgUrl($path); @endphp
                              @if($url)
                                <a href="{{ $url }}" target="_blank" class="block">
                                  <img src="{{ $url }}" class="w-full h-16 object-cover rounded-md border" alt="">
                                </a>
                              @endif
                            @endforeach
                          </div>
                        @endif

                        <div class="text-xs text-gray-400 mt-2">
                          Diulas: {{ \Carbon\Carbon::parse($r->created_at)->format('d M Y H:i') }}
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            @endif
          </div>
        </div>
      </div>

    </div>

    {{-- =========================
        KANAN: STATUS + TIMELINE + ADUAN
        Dibuat sticky + scroll internal agar tidak "kepanjangan"
    ========================= --}}
    <div class="lg:col-span-2 space-y-4 lg:sticky lg:top-6">
      <div class="space-y-4 max-h-[calc(100vh-6.5rem)] overflow-auto pr-1">

        {{-- STATUS + TIMESTAMP + TIMELINE --}}
        <div class="bg-white rounded-lg shadow p-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-sm text-gray-600">Status Pesanan</div>
              <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                {{ ucfirst($status) }}
              </span>
            </div>

            @if($canEdit)
              <form method="POST" action="{{ route('penjual.orders.masuk.status', $order->id) }}" class="flex items-center gap-2">
                @csrf
                @method('PATCH')

                <select name="status" class="text-sm border rounded-md px-3 py-1.5">
                  @if($status == 'menunggu')
                    <option value="menunggu" selected>Menunggu</option>
                    <option value="dikemas">Dikemas</option>
                    <option value="ditolak">Ditolak</option>
                  @elseif($status == 'dikemas')
                    <option value="dikemas" selected>Dikemas</option>
                    <option value="dikirim">Dikirim</option>
                    <option value="ditolak">Ditolak</option>
                  @elseif($status == 'dikirim')
                    <option value="dikirim" selected>Dikirim</option>
                  @endif
                </select>

                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                  Simpan
                </button>
              </form>
            @endif
          </div>

          {{-- Timestamp per status (lebih rapat) --}}
          <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
            <div class="flex justify-between border rounded-md px-3 py-2">
              <span class="text-gray-600">Menunggu</span>
              <span class="font-medium text-gray-800">{{ $tsMenunggu ? $tsMenunggu->format('d M Y, H:i') : '—' }}</span>
            </div>
            <div class="flex justify-between border rounded-md px-3 py-2">
              <span class="text-gray-600">Dikemas</span>
              <span class="font-medium text-gray-800">{{ $tsDikemas ? $tsDikemas->format('d M Y, H:i') : '—' }}</span>
            </div>
            <div class="flex justify-between border rounded-md px-3 py-2">
              <span class="text-gray-600">Dikirim</span>
              <span class="font-medium text-gray-800">{{ $tsDikirim ? $tsDikirim->format('d M Y, H:i') : '—' }}</span>
            </div>
            <div class="flex justify-between border rounded-md px-3 py-2">
              <span class="text-gray-600">Selesai</span>
              <span class="font-medium text-gray-800">{{ $tsSelesai ? $tsSelesai->format('d M Y, H:i') : '—' }}</span>
            </div>
            <div class="flex justify-between border rounded-md px-3 py-2 sm:col-span-2">
              <span class="text-gray-600">Ditolak</span>
              <span class="font-medium text-gray-800">{{ $tsDitolak ? $tsDitolak->format('d M Y, H:i') : '—' }}</span>
            </div>
          </div>

          {{-- Timeline (dibatasi tinggi biar ga memanjang) --}}
          <div class="mt-4">
            <div class="text-sm font-semibold text-gray-800 mb-2">Timeline</div>

            @if($logs->isEmpty())
              <div class="text-sm text-gray-500">Belum ada riwayat status.</div>
            @else
              <ol class="space-y-2 max-h-40 overflow-auto pr-1">
                @foreach($logs as $log)
                  <li class="flex items-start gap-3">
                    <div class="mt-1 w-2.5 h-2.5 rounded-full bg-gray-400"></div>
                    <div class="text-sm">
                      <div class="font-medium text-gray-800">
                        {{ ucfirst($log->status) }}
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

        {{-- ADUAN --}}
        <div class="bg-white rounded-lg shadow p-4">
          <div class="flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Aduan Pesanan</h2>

            @if(!empty($aduan))
              <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                Status: {{ $aduan->status_aduan ?? 'baru' }}
              </span>
            @endif
          </div>

          @if(empty($aduan))
            <p class="text-sm text-gray-600 mt-2">
              Belum ada aduan dari pembeli untuk pesanan ini.
            </p>
          @else
            <div class="mt-4 space-y-4">

              {{-- PESAN PEMBELI (rata kiri) --}}
              <div class="text-left">
                <div class="max-w-full bg-gray-100 rounded-2xl px-4 py-3 text-sm text-gray-800">
                  <div class="text-xs text-gray-500 mb-1">
                    PEMBELI • {{ optional($aduan->created_at)->format('d M Y, H:i') }}
                  </div>
                  <div class="font-semibold">{{ $aduan->judul }}</div>
                  <div class="mt-1 whitespace-pre-line">{{ $aduan->deskripsi }}</div>

                  {{-- Bukti --}}
                  @if(!empty($aduan->bukti))
                    @php
                      $imgsAduan = is_array($aduan->bukti) ? $aduan->bukti : json_decode($aduan->bukti, true);
                    @endphp

                    @if(!empty($imgsAduan))
                      <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($imgsAduan as $img)
                          <a href="{{ asset('storage/'.$img) }}" target="_blank">
                            <img src="{{ asset('storage/'.$img) }}"
                                 class="w-16 h-16 rounded-xl object-cover border" alt="">
                          </a>
                        @endforeach
                      </div>
                    @endif
                  @endif
                </div>
              </div>

              {{-- BALASAN ADMIN --}}
              @if(!empty($aduan->catatan_admin))
                <div class="text-left">
                  <div class="max-w-full bg-white border rounded-2xl px-4 py-3 text-sm text-gray-800">
                    <div class="text-xs text-gray-500 mb-1">
                      ADMIN • {{ optional($aduan->tgl_catatan_admin)->format('d M Y, H:i') }}
                    </div>
                    <div class="whitespace-pre-line">{{ $aduan->catatan_admin }}</div>
                  </div>
                </div>
              @endif

              {{-- BALASAN PENJUAL (yang sudah ada) --}}
              @if(isset($aduan->catatan_penjual) && trim($aduan->catatan_penjual) !== '')
                <div class="text-left">
                  <div class="max-w-full bg-green-50 border border-green-100 rounded-2xl px-4 py-3 text-sm text-gray-800">
                    <div class="text-xs text-gray-500 mb-1">
                      PENJUAL • {{ optional($aduan->tgl_catatan_penjual)->format('d M Y, H:i') }}
                    </div>
                    <div class="whitespace-pre-line">{{ $aduan->catatan_penjual }}</div>
                  </div>
                </div>
              @endif

              {{-- FORM BALAS (PENJUAL) --}}
              @if(!isset($aduan->catatan_penjual) || trim($aduan->catatan_penjual) === '')
                <div class="border-t pt-3">
                  <form method="POST" action="{{ route('penjual.orders.aduan.balas', $order->id) }}" class="space-y-2">
                    @csrf

                    <label class="text-sm font-medium text-gray-700">Balas Aduan</label>
                    <textarea name="catatan_penjual" rows="3"
                              class="w-full border rounded-xl px-3 py-2 text-sm"
                              placeholder="Tulis balasan untuk pembeli...">{{ old('catatan_penjual') }}</textarea>

                    @error('catatan_penjual')
                      <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end">
                      <button type="submit"
                              class="px-4 py-2 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700">
                        Kirim Balasan
                      </button>
                    </div>
                  </form>
                </div>
              @else
                <div class="border-t pt-3">
                  <div class="text-xs text-gray-500">
                    Kamu sudah membalas aduan ini. Balasan di atas sudah ditampilkan.
                  </div>
                </div>
              @endif

            </div>
          @endif
        </div>

      </div>
    </div>

  </div>

  {{-- KEMBALI --}}
  <div class="mt-4">
    <a href="{{ route('penjual.orders.masuk') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-800">
      ← Kembali ke Pesanan Masuk
    </a>
  </div>

</div>
@endsection
