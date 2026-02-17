<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800 overflow-hidden">

@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;

    // =========================
    // HITUNG BADGE ANGKA (UI SAJA)
    // =========================

    // kolom status_pesanan bisa beda2 (jaga-jaga)
    $orderStatusCol = Schema::hasColumn('orders', 'status_pesanan') ? 'status_pesanan'
        : (Schema::hasColumn('orders', 'status_pesanna') ? 'status_pesanna'
        : (Schema::hasColumn('orders', 'status') ? 'status' : null));

    // monitoring transaksi = order yang BELUM SELESAI
    $countMonitoring = 0;
    if ($orderStatusCol) {
        $countMonitoring = DB::table('orders')
            ->where(function ($q) use ($orderStatusCol) {
                $q->whereNull($orderStatusCol)
                  ->orWhereNotIn($orderStatusCol, ['selesai']);
            })
            ->count();
    }

    // manajemen aduan = aduan yang BELUM ditanggapi (status_aduan belum "selesai")
    $countAduan = 0;
    if (Schema::hasTable('aduans')) {
        $aduanStatusCol = Schema::hasColumn('aduans', 'status_aduan') ? 'status_aduan' : null;

        if ($aduanStatusCol) {
            // tampilkan badge hanya jika belum selesai (kamu bilang kalau admin sudah ubah status_aduan, badge hilang)
            $countAduan = DB::table('aduans')
                ->where(function ($q) use ($aduanStatusCol) {
                    $q->whereNull($aduanStatusCol)
                      ->orWhereNotIn($aduanStatusCol, ['selesai']);
                })
                ->count();
        }
    }

    // helper badge (angka merah)
    $badge = function ($n) {
        if (!$n || (int)$n <= 0) return '';
        return '<span class="ml-2 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold bg-red-600 text-white">'
            . ((int)$n > 99 ? '99+' : (int)$n)
            . '</span>';
    };
@endphp

{{-- ================= SIDEBAR DESKTOP (MANDEK) ================= --}}
<aside class="hidden md:flex md:flex-col fixed inset-y-0 left-0 w-64
              bg-gradient-to-b from-gray-900 to-gray-800
              text-gray-100 shadow-lg z-40">

    {{-- Logo --}}
    <div class="px-6 py-5 border-b border-gray-700">
        <h1 class="text-xl font-bold tracking-wide">Admin Panel</h1>
        <p class="text-xs text-gray-400">SecondLife Marketplace</p>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 px-4 py-4 space-y-2 text-sm overflow-y-auto">
        @php
            $active = 'bg-gray-700 text-white font-semibold';
            $normal = 'text-gray-300 hover:bg-gray-700 hover:text-white';

            // submenu style: tetap satu tema (ga bikin warna baru)
            $subActive = 'text-white font-semibold';
            $subNormal = 'text-gray-300 hover:text-white';

            $isTransaksi = request()->routeIs('admin.transaksi.index'); // route kamu
            $tab = request('tab', 'monitoring');
        @endphp

        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-4 py-2 rounded-lg transition
           {{ request()->routeIs('admin.dashboard') ? $active : $normal }}">
            📊 <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.user') }}"
           class="flex items-center gap-3 px-4 py-2 rounded-lg transition
           {{ request()->routeIs('admin.user') ? $active : $normal }}">
            👤 <span>Kelola User</span>
        </a>

        <a href="{{ route('admin.penjual') }}"
           class="flex items-center gap-3 px-4 py-2 rounded-lg transition
           {{ request()->routeIs('admin.penjual') ? $active : $normal }}">
            🛒 <span>Verifikasi Penjual</span>
        </a>

        <a href="{{ route('admin.toko.show') }}"
           class="flex items-center gap-3 px-4 py-2 rounded-lg transition
           {{ request()->routeIs('admin.toko.show') ? $active : $normal }}">
            📦 <span>Daftar Penjual</span>
        </a>

        {{-- ✅ Manajemen Transaksi + SUBMENU (mirip mockup, tema tetap) --}}
        <div class="space-y-1">

            {{-- Parent --}}
            <a href="{{ route('admin.transaksi.index', ['tab'=>'monitoring']) }}"
               class="flex items-center gap-3 px-4 py-2 rounded-lg transition
               {{ $isTransaksi ? $active : $normal }}">
                🧾 <span>Manajemen Transaksi</span>
            </a>

            {{-- Submenu: selalu tampil, tapi highlight ikut tab --}}
            <div class="ml-10 space-y-1">

                <a href="{{ route('admin.transaksi.index', ['tab'=>'monitoring']) }}"
                   class="flex items-center justify-between gap-2 px-2 py-1 rounded transition
                   {{ $isTransaksi && $tab==='monitoring' ? $subActive : $subNormal }}">
                    <span>› Monitoring Transaksi</span>
                    {!! $isTransaksi && $tab==='monitoring' ? '' : $badge($countMonitoring) !!}
                </a>

                <a href="{{ route('admin.transaksi.index', ['tab'=>'aduan']) }}"
                   class="flex items-center justify-between gap-2 px-2 py-1 rounded transition
                   {{ $isTransaksi && $tab==='aduan' ? $subActive : $subNormal }}">
                    <span>› Manajemen Aduan</span>
                    {!! $isTransaksi && $tab==='aduan' ? '' : $badge($countAduan) !!}
                </a>

                <a href="{{ route('admin.transaksi.index', ['tab'=>'riwayat']) }}"
                   class="block px-2 py-1 rounded transition
                   {{ $isTransaksi && $tab==='riwayat' ? $subActive : $subNormal }}">
                    › Riwayat Transaksi
                </a>
            </div>

        </div>

    </nav>

    {{-- Logout --}}
    <div class="px-4 py-4 border-t border-gray-700">
        <form action="/logout" method="POST" onsubmit="return confirm('Yakin ingin keluar?')">
            @csrf
            <button class="w-full flex items-center gap-3 px-4 py-2 rounded-lg
                           text-red-400 hover:bg-red-500 hover:text-white transition">
                🚪 Logout
            </button>
        </form>
    </div>
</aside>

{{-- ================= MAIN AREA ================= --}}
<div class="md:ml-64 flex flex-col h-screen overflow-hidden">

    {{-- HEADER (DIAM, TIDAK GESER) --}}
    <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between
                   sticky top-0 z-30 overflow-x-hidden">
        <div class="flex items-center gap-4">
            <button id="openSidebar" class="md:hidden text-xl">☰</button>
            <h2 class="text-lg font-semibold">
                @yield('title', 'Dashboard')
            </h2>
        </div>

        <div class="text-sm text-gray-600">
            Admin
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 md:p-6">
        @yield('content')
    </main>

</div>

{{-- MOBILE SIDEBAR (TIDAK DIUBAH STRUKTUR) --}}
<div id="mobileSidebar"
     class="fixed inset-0 z-50 bg-black/50 hidden md:hidden">
    <aside id="mobileSidebarPanel"
           class="w-64 h-full bg-gray-900 text-gray-100 shadow-lg p-4
                  transform -translate-x-full transition-transform duration-300">
        <button id="closeSidebar" class="text-gray-400 mb-4">✕ Tutup</button>

        @php
            $isTransaksiMobile = request()->routeIs('admin.transaksi.index');
            $tabMobile = request('tab', 'monitoring');
        @endphp

        <nav class="space-y-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Dashboard</a>
            <a href="{{ route('admin.user') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Kelola User</a>
            <a href="{{ route('admin.penjual') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Verifikasi Penjual</a>
            <a href="{{ route('admin.toko.show') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Daftar Penjual</a>

            {{-- Parent --}}
            <a href="{{ route('admin.transaksi.index', ['tab'=>'monitoring']) }}"
               class="block px-4 py-2 rounded hover:bg-gray-700">
                Manajemen Transaksi
            </a>

            {{-- Submenu (selalu tampil) --}}
            <div class="ml-4 space-y-1">

                <a href="{{ route('admin.transaksi.index', ['tab'=>'monitoring']) }}"
                   class="flex items-center justify-between gap-2 px-4 py-1 rounded hover:bg-gray-700
                   {{ $isTransaksiMobile && $tabMobile==='monitoring' ? 'text-white font-semibold' : 'text-gray-300' }}">
                    <span>› Monitoring Transaksi</span>
                    {!! $isTransaksiMobile && $tabMobile==='monitoring' ? '' : $badge($countMonitoring) !!}
                </a>

                <a href="{{ route('admin.transaksi.index', ['tab'=>'aduan']) }}"
                   class="flex items-center justify-between gap-2 px-4 py-1 rounded hover:bg-gray-700
                   {{ $isTransaksiMobile && $tabMobile==='aduan' ? 'text-white font-semibold' : 'text-gray-300' }}">
                    <span>› Manajemen Aduan</span>
                    {!! $isTransaksiMobile && $tabMobile==='aduan' ? '' : $badge($countAduan) !!}
                </a>

                <a href="{{ route('admin.transaksi.index', ['tab'=>'riwayat']) }}"
                   class="block px-4 py-1 rounded hover:bg-gray-700
                   {{ $isTransaksiMobile && $tabMobile==='riwayat' ? 'text-white font-semibold' : 'text-gray-300' }}">
                    › Riwayat Transaksi
                </a>
            </div>
        </nav>

        <div class="mt-6 pt-4 border-t border-gray-700">
            <form action="/logout" method="POST">
                @csrf
                <button class="w-full flex items-center gap-3 px-4 py-2 rounded-lg
                               text-red-400 hover:bg-red-500 hover:text-white transition text-sm">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>
</div>

{{-- SCRIPT (TIDAK DIUBAH) --}}
<script>
    const openSidebar = document.getElementById('openSidebar');
    const closeSidebar = document.getElementById('closeSidebar');
    const mobileSidebar = document.getElementById('mobileSidebar');
    const panel = document.getElementById('mobileSidebarPanel');

    openSidebar?.addEventListener('click', () => {
        mobileSidebar.classList.remove('hidden');
        panel.classList.remove('-translate-x-full');
    });

    closeSidebar?.addEventListener('click', closeMobile);
    mobileSidebar?.addEventListener('click', e => {
        if (e.target === mobileSidebar) closeMobile();
    });

    function closeMobile() {
        panel.classList.add('-translate-x-full');
        setTimeout(() => mobileSidebar.classList.add('hidden'), 300);
    }
</script>

@stack('scripts')
</body>
</html>
