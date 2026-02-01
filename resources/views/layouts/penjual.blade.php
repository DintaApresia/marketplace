<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Penjual - @yield('title')</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 text-gray-800 overflow-x-hidden">

{{-- ================= SIDEBAR ================= --}}
<aside
    id="sidebar"
    class="fixed top-0 left-0 z-50
           h-full w-64
           bg-slate-900 text-slate-200
           transform -translate-x-full
           transition-transform duration-300
           md:translate-x-0">

    <nav class="p-4 text-sm h-full flex flex-col">

        {{-- BRAND --}}
        <div class="mb-6">
            <h1 class="text-lg font-bold text-white">Panel Penjual</h1>
            <p class="text-xs text-slate-400">SecondLife Marketplace</p>
        </div>

        {{-- MOBILE CLOSE --}}
        <button
            onclick="closeSidebar()"
            class="md:hidden mb-4 text-gray-400 hover:text-white text-sm">
            ✕ Tutup
        </button>

        @php
            $active = 'bg-slate-700 text-white font-semibold';
            $normal = 'text-slate-300 hover:bg-slate-800 hover:text-white';
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

            <a href="{{ route('penjual.orders.masuk') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md {{ request()->routeIs('penjual.orders.masuk') ? $active : $normal }}">
                🧾 <span>Pesanan Masuk</span>
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

{{-- ================= HEADER ================= --}}
<header
    class="fixed top-0 left-0 right-0 z-40
           bg-white border-b border-gray-200 shadow-sm
           md:ml-64">
    <div class="flex items-center justify-between px-4 md:px-6 py-4">

        {{-- LEFT --}}
        <button
            onclick="openSidebar()"
            class="md:hidden text-2xl text-gray-700 focus:outline-none">
            ☰
        </button>

        {{-- PENJUAL --}}
        <div class="flex items-center gap-3 ml-auto">
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

{{-- ================= BACKDROP ================= --}}
<div
    id="sidebar-backdrop"
    onclick="closeSidebar()"
    class="fixed inset-0 bg-black/50 z-40 hidden md:hidden">
</div>

{{-- ================= MAIN CONTENT ================= --}}
<main class="pt-[72px] md:ml-64 p-4 md:p-6">
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
