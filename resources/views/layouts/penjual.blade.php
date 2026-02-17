<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Penjual - @yield('title')</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 text-gray-800 overflow-x-hidden">

{{-- ================= HEADER ================= --}}
<header
    class="fixed top-0 left-0 right-0 z-50
           bg-white border-b border-gray-200 shadow-sm">

    <div class="flex items-center justify-between px-4 md:px-6 h-[72px]">

        {{-- LEFT --}}
        <div class="flex items-center gap-4">
            <button
                onclick="openSidebar()"
                class="md:hidden text-2xl text-gray-700 focus:outline-none">
                ☰
            </button>

            <div>
                <h1 class="text-lg font-bold text-gray-900">
                    Panel Penjual
                </h1>
                <p class="text-xs text-gray-500">
                    SecondLife Marketplace
                </p>
            </div>
        </div>

        {{-- USER --}}
        <div class="flex items-center gap-3">
            <span class="hidden sm:block text-sm font-medium text-gray-700">
                {{ auth()->user()->penjual->nama_penjual ?? auth()->user()->name }}
            </span>

            <img
                src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->penjual->nama_penjual ?? auth()->user()->name) }}&background=334155&color=ffffff"
                class="w-9 h-9 rounded-full border border-gray-300"
                alt="Avatar">
        </div>

    </div>
</header>

{{-- ================= SIDEBAR ================= --}}
<aside
    id="sidebar"
    class="fixed left-0 z-40
           top-[72px]
           h-[calc(100vh-72px)]
           w-64
           bg-slate-900 text-slate-200
           transform -translate-x-full
           transition-transform duration-300
           md:translate-x-0">

    <nav class="p-4 text-sm h-full flex flex-col">

        {{-- MOBILE CLOSE --}}
        <button
            onclick="closeSidebar()"
            class="md:hidden mb-4 text-gray-400 hover:text-white text-sm">
            ✕ Tutup
        </button>

        @php
            $active = 'bg-slate-700 text-white font-semibold';
            $normal = 'text-slate-300 hover:bg-slate-800 hover:text-white';

            // =========================
            // BADGE PESANAN MASUK (ONGOING)
            // menunggu / dikemas / dikirim (selesai tidak dihitung)
            // =========================
            $pesananMasukCount = 0;

            try {
                $userIdPenjual = auth()->id();

                // cari penjual_id yang dipakai di orders:
                // - bisa user_id (auth()->id())
                // - atau penjuals.id (kalau sistem lama nyimpan begitu)
                $penjualModel = auth()->user()->penjual ?? null;
                $penjualIds = collect([$userIdPenjual]);

                if ($penjualModel && !empty($penjualModel->id)) {
                    $penjualIds->push((int)$penjualModel->id);
                }
                $penjualIds = $penjualIds->filter()->unique()->values();

                // tentukan kolom status yang dipakai
                $statusCol = \Illuminate\Support\Facades\Schema::hasColumn('orders', 'status_pesanan')
                    ? 'status_pesanan'
                    : (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'status') ? 'status' : null);

                if ($statusCol) {
                    $pesananMasukCount = \Illuminate\Support\Facades\DB::table('orders')
                        ->whereIn('penjual_id', $penjualIds->all())
                        ->whereIn($statusCol, ['menunggu', 'dikemas', 'dikirim'])
                        ->count();
                }
            } catch (\Throwable $e) {
                $pesananMasukCount = 0;
            }
        @endphp

        {{-- MENU --}}
        <div class="space-y-1">
            <a href="{{ route('penjual.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('penjual.dashboard') ? $active : $normal }}">
                📊 <span>Dashboard</span>
            </a>

            <a href="{{ route('penjual.profile') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('penjual.profile') ? $active : $normal }}">
                👤 <span>Profil Penjual</span>
            </a>

            <a href="{{ route('penjual.produk.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('produk.*') ? $active : $normal }}">
                📦 <span>Produk Saya</span>
            </a>

            {{-- ✅ PESANAN MASUK + BADGE --}}
            <a href="{{ route('penjual.orders.masuk') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('penjual.orders.masuk') ? $active : $normal }}">
                <span class="relative">
                    🧾
                    @if(($pesananMasukCount ?? 0) > 0)
                        <span class="absolute -top-2 -right-2 min-w-[18px] h-[18px]
                                     px-1 rounded-full bg-red-500 text-white text-[10px]
                                     leading-[18px] text-center font-semibold">
                            {{ $pesananMasukCount }}
                        </span>
                    @endif
                </span>
                <span>Pesanan Masuk</span>
            </a>

            <a href="{{ route('penjual.laporan') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('penjual.laporan') ? $active : $normal }}">
                📈 <span>Laporan Penjualan</span>
            </a>
        </div>

        {{-- LOGOUT --}}
        <div class="mt-auto pt-4 border-t border-slate-700">
            <form action="{{ route('logout') }}" method="POST"
                  onsubmit="return confirm('Yakin ingin keluar?')">
                @csrf
                <button
                    type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-md
                           text-red-400 hover:bg-slate-800 hover:text-red-300 transition">
                    🚪 Logout
                </button>
            </form>
        </div>

    </nav>
</aside>

{{-- ================= BACKDROP ================= --}}
<div
    id="sidebar-backdrop"
    onclick="closeSidebar()"
    class="fixed inset-0 bg-black/50 z-30 hidden md:hidden">
</div>

{{-- ================= MAIN CONTENT ================= --}}
<main class="pt-[88px] md:pl-[17rem] px-4 md:px-6 pb-6">
    {{-- 17rem = 256px sidebar + 16px gap --}}

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">
            @yield('title')
        </h2>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-4 md:p-6">
        @yield('content')
    </div>

</main>

{{-- ================= SCRIPT ================= --}}
<script>
function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebar-backdrop').classList.remove('hidden');
}

function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebar-backdrop').classList.add('hidden');
}
</script>

@stack('scripts')
</body>
</html>
