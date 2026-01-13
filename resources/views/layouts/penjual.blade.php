<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Penjual - @yield('title')</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen">

    {{-- HEADER --}}
    <header class="bg-white shadow fixed top-0 left-0 right-0 z-20">
        <div class="flex items-center justify-between px-6 py-4">

            {{-- Branding --}}
            <div>
                <h1 class="text-xl font-bold text-green-700">Panel Penjual</h1>
                <p class="text-xs text-gray-500">SecondLife Marketplace</p>
            </div>

                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <span class="font-medium">
                            {{ auth()->user()->name }}
                        </span>
                    <div class="flex items-center gap-2">
                        <img
                            src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=22c55e&color=ffffff"
                            alt="Avatar"
                            class="w-9 h-9 rounded-full border"
                        >
                    </div>
                </div>
        </div>
    </header>

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white shadow-lg fixed top-[72px] left-0 bottom-0 z-10">

        <nav class="p-4 space-y-1 text-sm">

            <a href="{{ route('penjual.dashboard') }}"
               class="block px-3 py-2 rounded-md transition hover:bg-green-50
               {{ request()->routeIs('penjual.dashboard')
                    ? 'bg-green-100 text-green-700 font-semibold'
                    : 'text-gray-700' }}">
                Dashboard
            </a>

            <a href="{{ route('penjual.profile') }}"
               class="block px-3 py-2 rounded-md transition hover:bg-green-50
               {{ request()->routeIs('penjual.profile')
                    ? 'bg-green-100 text-green-700 font-semibold'
                    : 'text-gray-700' }}">
                Profile
            </a>

            <a href="{{ route('produk.index') }}"
               class="block px-3 py-2 rounded-md transition hover:bg-green-50
               {{ request()->routeIs('produk.*')
                    ? 'bg-green-100 text-green-700 font-semibold'
                    : 'text-gray-700' }}">
                Produk Saya
            </a>

            <a href="{{ route('penjual.orders.masuk') }}"
               class="block px-3 py-2 rounded-md transition hover:bg-green-50
               {{ request()->routeIs('penjual.orders.masuk')
                    ? 'bg-green-100 text-green-700 font-semibold'
                    : 'text-gray-700' }}">
                Pesanan Masuk
            </a>
            <a href="{{ route('penjual.laporan') }}"
               class="block px-3 py-2 rounded-md transition hover:bg-green-50
               {{ request()->routeIs('penjual.laporan')
                    ? 'bg-green-100 text-green-700 font-semibold'
                    : 'text-gray-700' }}">
                Laporan Penjualan
            </a>

            <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Yakin ingin keluar?')">
                @csrf
                <button
                    type="submit"
                    class="w-full text-left px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 mt-4">
                    Logout
                </button>
            </form>
        </nav>
    </aside>

    {{-- CONTENT --}}
    <main class="ml-64 pt-[96px] p-6">

        {{-- Judul Halaman --}}
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            @yield('title')
        </h2>

        {{-- Isi --}}
        @yield('content')
    </main>
@stack('scripts')
</body>
</html>
