<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Penjual - @yield('title')</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen text-gray-800 overflow-x-hidden">

{{-- ================= HEADER ================= --}}
<header class="bg-white shadow-sm fixed top-0 left-0 right-0 z-40 w-full">
    <div class="flex items-center justify-between px-4 md:px-6 py-4">

        {{-- Left --}}
        <div class="flex items-center gap-3">
            {{-- Hamburger (mobile only) --}}
            <button
                onclick="toggleSidebar()"
                class="md:hidden text-2xl text-gray-700 focus:outline-none">
                ☰
            </button>

            <div>
                <h1 class="text-lg md:text-xl font-bold text-green-700">
                    Panel Penjual
                </h1>
                <p class="hidden md:block text-xs text-gray-500">
                    SecondLife Marketplace
                </p>
            </div>
        </div>

        {{-- User --}}
        <div class="flex items-center gap-3">
            <span class="hidden sm:block text-sm font-medium text-gray-700">
                {{ auth()->user()->name }}
            </span>
            <img
                src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=22c55e&color=ffffff"
                alt="Avatar"
                class="w-9 h-9 rounded-full border shadow-sm"
            >
        </div>

    </div>
</header>

{{-- ================= WRAPPER (PENTING) ================= --}}
<div class="flex pt-[72px]">

{{-- ================= SIDEBAR ================= --}}
<aside
  id="sidebar"
  class="fixed md:sticky md:top-[72px] left-0
         h-[calc(100vh-72px)] md:h-[calc(100vh-72px)]
         w-64 bg-white shadow-lg border-r
         transform -translate-x-full md:translate-x-0
         transition-transform duration-300
         z-30 overflow-y-auto">

    <nav class="p-4 space-y-1 text-sm">

        @php
            $active = 'bg-green-50 text-green-700 font-semibold border-l-4 border-green-600';
            $normal = 'text-gray-700 hover:bg-green-50';
        @endphp

        <a href="{{ route('penjual.dashboard') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-md transition {{ request()->routeIs('penjual.dashboard') ? $active : $normal }}">
            📊 <span>Dashboard</span>
        </a>

        <a href="{{ route('penjual.profile') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-md transition {{ request()->routeIs('penjual.profile') ? $active : $normal }}">
            👤 <span>Profil Toko</span>
        </a>

        <a href="{{ route('produk.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-md transition {{ request()->routeIs('produk.*') ? $active : $normal }}">
            📦 <span>Produk Saya</span>
        </a>

        <a href="{{ route('penjual.orders.masuk') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-md transition {{ request()->routeIs('penjual.orders.masuk') ? $active : $normal }}">
            🧾 <span>Pesanan Masuk</span>
        </a>

        <a href="{{ route('penjual.laporan') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-md transition {{ request()->routeIs('penjual.laporan') ? $active : $normal }}">
            📈 <span>Laporan Penjualan</span>
        </a>

        {{-- Divider --}}
        <div class="my-4 border-t"></div>

        <form action="{{ route('logout') }}" method="POST"
              onsubmit="return confirm('Yakin ingin keluar?')">
            @csrf
            <button
                type="submit"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-md
                       text-red-600 hover:bg-red-50 transition">
                🚪 Logout
            </button>
        </form>

    </nav>
</aside>

{{-- ================= MAIN CONTENT ================= --}}
<main class="flex-1 p-4 md:p-6 bg-gray-100 overflow-x-hidden">

    {{-- Page Title --}}
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">
            @yield('title')
        </h2>
        <p class="text-sm text-gray-500">
            Kelola toko dan penjualan Anda
        </p>
    </div>

    {{-- Page Content --}}
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        @yield('content')
    </div>

</main>

</div>

{{-- ================= SCRIPT ================= --}}
<script>
function toggleSidebar() {
    document.getElementById('sidebar')
        .classList.toggle('-translate-x-full');
}
</script>

@stack('scripts')
</body>
</html>
