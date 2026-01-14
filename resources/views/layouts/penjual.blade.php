<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Penjual - @yield('title')</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen text-gray-800">

{{-- ================= HEADER ================= --}}
<header class="bg-white shadow-sm fixed top-0 left-0 right-0 z-20">
    <div class="flex items-center justify-between px-6 py-4">

        {{-- Branding --}}
        <div>
            <h1 class="text-xl font-bold text-green-700">Panel Penjual</h1>
            <p class="text-xs text-gray-500">SecondLife Marketplace</p>
        </div>

        {{-- User --}}
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-700">
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

{{-- ================= SIDEBAR ================= --}}
<aside class="w-64 bg-white shadow-lg fixed top-[72px] left-0 bottom-0 z-10 border-r">

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

        <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Yakin ingin keluar?')">
            @csrf
            <button
                type="submit"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-red-600 hover:bg-red-50 transition">
                🚪 Logout
            </button>
        </form>

    </nav>
</aside>

{{-- ================= CONTENT ================= --}}
<main class="ml-64 pt-[96px] p-6">

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
    <div class="bg-white rounded-xl shadow-sm p-6">
        @yield('content')
    </div>

</main>

@stack('scripts')
</body>
</html>