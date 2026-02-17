@extends('layouts.admin')

@section('title')
    @if($tab==='monitoring') Monitoring Transaksi
    @elseif($tab==='aduan') Manajemen Aduan
    @else Riwayat Transaksi
    @endif
@endsection

@section('content')
@php
    $statusOrderOptions = ['menunggu','diproses','dikemas','dikirim','selesai','dibatalkan'];
    $statusAduanOptions = ['menunggu','diproses','selesai','dibatalkan'];

    $tabActive = 'bg-gray-800 text-white border-gray-700';
    $tabNormal = 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50';

    $tabNow = $tab ?? request('tab','monitoring');

    // =======================
    // BADGE STATUS (ORDER)
    // =======================
    $badgeOrder = function ($s) {
        $s = strtolower((string) $s);

        return match ($s) {
            'menunggu', 'belum_bayar', 'belum bayar' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            'diproses'  => 'bg-blue-100 text-blue-800 border border-blue-200',
            'dikemas'   => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
            'dikirim'   => 'bg-sky-100 text-sky-800 border border-sky-200',
            'selesai'   => 'bg-green-100 text-green-800 border border-green-200',
            'dibatalkan', 'batal' => 'bg-red-100 text-red-800 border border-red-200',
            default     => 'bg-gray-100 text-gray-700 border border-gray-200',
        };
    };

    // =======================
    // BADGE STATUS (ADUAN)
    // =======================
    $badgeAduan = function ($s) {
        $s = strtolower((string) $s);

        return match ($s) {
            'menunggu', 'baru' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            'diproses'  => 'bg-blue-100 text-blue-800 border border-blue-200',
            'selesai'   => 'bg-green-100 text-green-800 border border-green-200',
            'dibatalkan', 'batal' => 'bg-red-100 text-red-800 border border-red-200',
            default     => 'bg-gray-100 text-gray-700 border border-gray-200',
        };
    };

    // =======================
    // ✅ HITUNG ANGKA TAB (UI SAJA)
    // =======================
    // status order (anti typo)
    if (\Schema::hasColumn('orders', 'status_pesanan')) $orderStatusCol = 'status_pesanan';
    elseif (\Schema::hasColumn('orders', 'status_pesanna')) $orderStatusCol = 'status_pesanna';
    else $orderStatusCol = 'status_pesanan';

    // status aduan
    $aduanStatusCol = \Schema::hasColumn('aduans', 'status_aduan') ? 'status_aduan'
        : (\Schema::hasColumn('aduans', 'status') ? 'status' : null);

    // MONITORING: belum selesai (selain selesai)
    $countMonitoring = \DB::table('orders')
        ->whereIn($orderStatusCol, ['menunggu','diproses','dikemas','dikirim'])
        ->count();

    // ADUAN: belum ditanggapi admin => status masih menunggu/baru/null (sesuai ketentuanmu)
    $countAduanPending = 0;
    if (\Schema::hasTable('aduans')) {
        $qAdu = \DB::table('aduans');
        if ($aduanStatusCol) {
            $qAdu->where(function($w) use ($aduanStatusCol){
                $w->whereNull($aduanStatusCol)
                  ->orWhere($aduanStatusCol, '')
                  ->orWhereIn($aduanStatusCol, ['menunggu','baru']);
            });
        } else {
            // kalau gak ada kolom status_aduan, fallback: dianggap pending semua
            // (biar gak error)
        }
        $countAduanPending = $qAdu->count();
    }
@endphp

<div class="space-y-4">

    {{-- HEADER BAR --}}
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-800">
            @if($tabNow==='monitoring') Monitoring Transaksi
            @elseif($tabNow==='aduan') Manajemen Aduan
            @else Riwayat Transaksi
            @endif
        </h1>

        <div class="flex items-center gap-2 text-gray-500">
            <button class="p-2 rounded-lg hover:bg-white/60" title="Cari">🔍</button>
            <button class="p-2 rounded-lg hover:bg-white/60" title="Profil">👤</button>
            <button class="p-2 rounded-lg hover:bg-white/60" title="Notifikasi">🔔</button>
        </div>
    </div>

    {{-- TAB + FILTER BAR --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 space-y-3">

        {{-- Tabs --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.transaksi.index', array_filter(['tab'=>'monitoring','q'=>$q,'penjual_id'=>$penjualId,'status'=>$status,'start'=>$start,'end'=>$end])) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium border transition inline-flex items-center gap-2
               {{ $tabNow==='monitoring' ? $tabActive : $tabNormal }}">
                <span>Monitoring Transaksi</span>

                @if(($countMonitoring ?? 0) > 0)
                    <span class="inline-flex items-center justify-center min-w-[24px] h-5 px-2 rounded-full text-xs font-bold bg-red-600 text-white">
                        {{ $countMonitoring }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.transaksi.index', array_filter(['tab'=>'aduan','q'=>$q,'penjual_id'=>$penjualId,'status'=>$status,'start'=>$start,'end'=>$end])) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium border transition inline-flex items-center gap-2
               {{ $tabNow==='aduan' ? $tabActive : $tabNormal }}">
                <span>Manajemen Aduan</span>

                @if(($countAduanPending ?? 0) > 0)
                    <span class="inline-flex items-center justify-center min-w-[24px] h-5 px-2 rounded-full text-xs font-bold bg-red-600 text-white">
                        {{ $countAduanPending }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.transaksi.index', array_filter(['tab'=>'riwayat','q'=>$q,'penjual_id'=>$penjualId,'status'=>$status,'start'=>$start,'end'=>$end])) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium border transition
               {{ $tabNow==='riwayat' ? $tabActive : $tabNormal }}">
                Riwayat Transaksi
            </a>
        </div>

        {{-- Filter row --}}
        <form method="GET" action="{{ route('admin.transaksi.index') }}"
              class="flex flex-col gap-3 lg:flex-row lg:items-center">

            <input type="hidden" name="tab" value="{{ $tabNow }}">

            {{-- Filter Penjual (nama_toko) --}}
            <select name="penjual_id" class="border rounded-lg px-3 py-2 text-sm w-full lg:w-64">
                <option value="">Filter Penjual</option>
                @foreach($penjualList as $p)
                    <option value="{{ $p->id }}" @selected((string)$penjualId === (string)$p->id)>
                        {{ $p->nama_toko ?? ('Penjual #'.$p->id) }}
                    </option>
                @endforeach
            </select>

            {{-- Status --}}
            <select name="status" class="border rounded-lg px-3 py-2 text-sm w-full lg:w-56">
                <option value="">Status: Semua</option>
                @if($tabNow === 'aduan')
                    @foreach($statusAduanOptions as $s)
                        <option value="{{ $s }}" @selected($status===$s)>{{ ucfirst($s) }}</option>
                    @endforeach
                @else
                    @foreach($statusOrderOptions as $s)
                        <option value="{{ $s }}" @selected($status===$s)>{{ ucfirst($s) }}</option>
                    @endforeach
                @endif
            </select>

            {{-- Tanggal (range) --}}
            <div class="flex gap-2 w-full lg:w-auto">
                <input type="date" name="start" value="{{ $start }}"
                       class="border rounded-lg px-3 py-2 text-sm w-full lg:w-48">
                <input type="date" name="end" value="{{ $end }}"
                       class="border rounded-lg px-3 py-2 text-sm w-full lg:w-48">
            </div>

            {{-- Search --}}
            <div class="flex gap-2 w-full lg:w-auto lg:ml-auto">
                <input name="q" value="{{ $q }}"
                       class="border rounded-lg px-3 py-2 text-sm w-full lg:w-72"
                       placeholder="Cari...">
                <button class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                    Terapkan
                </button>
            </div>
        </form>
    </div>

    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-900 border-b">
                    @if($tabNow==='monitoring')
                        <tr class="text-left">
                            <th class="px-4 py-3 text-white">ID Pesanan</th>
                            <th class="px-4 py-3 text-white">Tanggal</th>
                            <th class="px-4 py-3 text-white">Pembeli</th>
                            <th class="px-4 py-3 text-white">Penjual (Nama Toko)</th>
                            <th class="px-4 py-3 text-white">Status</th>
                            <th class="px-4 py-3 text-white">Aksi</th>
                        </tr>
                    @elseif($tabNow==='aduan')
                        <tr class="text-left">
                            <th class="px-4 py-3 text-white">ID Aduan</th>
                            <th class="px-4 py-3 text-white">ID Pesanan</th>
                            <th class="px-4 py-3 text-white">Pembeli</th>
                            <th class="px-4 py-3 text-white">Penjual (Nama Toko)</th>
                            <th class="px-4 py-3 text-white">Judul</th>
                            <th class="px-4 py-3 text-white">Status</th>
                            <th class="px-4 py-3 text-white">Aksi</th>
                        </tr>
                    @else
                        <tr class="text-left">
                            <th class="px-4 py-3 text-white">ID Pesanan</th>
                            <th class="px-4 py-3 text-white">Penjual (Nama Toko)</th>
                            <th class="px-4 py-3 text-white">Pembeli</th>
                            <th class="px-4 py-3 text-white">Tanggal Selesai</th>
                            @if($hasOrderTotal)<th class="px-4 py-3 text-white">Total</th>@endif
                            <th class="px-4 py-3 text-white">Status</th>
                            <th class="px-4 py-3 text-white">Aksi</th>
                        </tr>
                    @endif
                </thead>

                <tbody class="divide-y">
                    {{-- =======================
                         MONITORING
                         ======================= --}}
                    @if($tabNow==='monitoring')
                        @forelse($orders as $o)
                            <tr>
                                <td class="px-4 py-3 font-medium">
                                    {{ $hasOrderKode ? ($o->kode_order ?? '#'.$o->id) : '#'.$o->id }}
                                </td>
                                <td class="px-4 py-3">{{ $o->created_at ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $o->pembeli_nama ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $o->nama_toko ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-md text-xs {{ $badgeOrder($o->status_pesanan ?? '') }}">
                                        {{ $o->status_pesanan ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.transaksi.show', ['id' => $o->id]) }}"
                                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-blue-600 hover:bg-blue-50">
                                        ▶ Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td></tr>
                        @endforelse
                    @endif

                    {{-- =======================
                         ADUAN
                         ======================= --}}
                    @if($tabNow==='aduan')
                        @forelse($aduans as $a)
                            @php
                                $stAduan = $hasAduanStatus ? ($a->status_aduan ?? '') : ($a->status_pesanan ?? '');
                            @endphp
                            <tr class="align-top">
                                <td class="px-4 py-3 font-medium">
                                    AD{{ str_pad($a->id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $hasOrderKode ? ($a->kode_order ?? '#'.$a->order_id) : '#'.$a->order_id }}
                                </td>
                                <td class="px-4 py-3">{{ $a->pembeli_nama ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $a->nama_toko ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $a->judul }}</div>
                                    <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $a->deskripsi }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-md text-xs {{ $badgeAduan($stAduan) }}">
                                        {{ $stAduan ?: '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.aduan.show', ['id' => $a->id]) }}"
                                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-blue-600 hover:bg-blue-50">
                                        ▶ Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Tidak ada aduan</td></tr>
                        @endforelse
                    @endif

                    {{-- =======================
                         RIWAYAT
                         ======================= --}}
                    @if($tabNow==='riwayat')
                        @forelse($orders as $o)
                            @php
                                $tglSelesai = $o->last_status_at ?? ($o->updated_at ?? null);
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium">
                                    {{ $hasOrderKode ? ($o->kode_order ?? '#'.$o->id) : '#'.$o->id }}
                                </td>
                                <td class="px-4 py-3">{{ $o->nama_toko ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $o->pembeli_nama ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $tglSelesai ?? '-' }}</td>
                                @if($hasOrderTotal)
                                    <td class="px-4 py-3">{{ $o->total ?? '-' }}</td>
                                @endif
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-md text-xs {{ $badgeOrder($o->status_pesanan ?? '') }}">
                                        {{ $o->status_pesanan ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.transaksi.show', ['id' => $o->id, 'from' => 'riwayat']) }}"
                                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-blue-600 hover:bg-blue-50">
                                        ▶ Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $hasOrderTotal ? 7 : 6 }}" class="px-4 py-8 text-center text-gray-500">Tidak ada riwayat</td></tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4">
            @if($tabNow==='aduan')
                {{ $aduans->links() }}
            @else
                {{ $orders->links() }}
            @endif
        </div>
    </div>

</div>
@endsection
