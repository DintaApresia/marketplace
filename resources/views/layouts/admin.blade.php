<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800">

<div class="flex h-screen overflow-hidden">

    {{-- ================= SIDEBAR DESKTOP ================= --}}
    <aside class="hidden md:flex md:flex-col w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-gray-100 shadow-lg">

        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-gray-700">
            <h1 class="text-xl font-bold tracking-wide">Admin Panel</h1>
            <p class="text-xs text-gray-400">SecondLife Marketplace</p>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 px-4 py-4 space-y-2 text-sm">

            @php
                $active = 'bg-gray-700 text-white font-semibold';
                $normal = 'text-gray-300 hover:bg-gray-700 hover:text-white';
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
                📦 <span>Produk Penjual</span>
            </a>

        </nav>

        {{-- Logout --}}
        <div class="px-4 py-4 border-t border-gray-700">
            <form action="/logout" method="POST" onsubmit="return confirm('Yakin ingin keluar?')">
                @csrf
                <button class="w-full flex items-center gap-3 px-4 py-2 rounded-lg text-red-400 hover:bg-red-500 hover:text-white transition">
                    🚪 Logout
                </button>
            </form>
        </div>

    </aside>

    {{-- ================= MOBILE SIDEBAR ================= --}}
    <div id="mobileSidebar"
         class="fixed inset-0 z-40 bg-black/50 hidden">
        <aside class="w-64 h-full bg-gray-900 text-gray-100 shadow-lg p-4 animate-slide-in">
            <button id="closeSidebar" class="text-gray-400 mb-4">✕ Tutup</button>

            <nav class="space-y-2 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Dashboard</a>
                <a href="{{ route('admin.user') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Kelola User</a>
                <a href="{{ route('admin.penjual') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Verifikasi Penjual</a>
                <a href="{{ route('admin.toko.show') }}" class="block px-4 py-2 rounded hover:bg-gray-700">Produk Penjual</a>
            </nav>
        </aside>
    </div>

    {{-- ================= MAIN AREA ================= --}}
    <div class="flex-1 flex flex-col">

        {{-- Topbar --}}
        <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button id="openSidebar" class="md:hidden text-xl">☰</button>
                <h2 class="text-lg font-semibold">@yield('title', 'Dashboard')</h2>
            </div>

            <div class="text-sm text-gray-600">
                Admin
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>

    </div>

</div>

{{-- SCRIPT --}}
<script>
    const openSidebar = document.getElementById('openSidebar');
    const closeSidebar = document.getElementById('closeSidebar');
    const mobileSidebar = document.getElementById('mobileSidebar');

    openSidebar?.addEventListener('click', () => {
        mobileSidebar.classList.remove('hidden');
    });

    closeSidebar?.addEventListener('click', () => {
        mobileSidebar.classList.add('hidden');
    });

    mobileSidebar?.addEventListener('click', e => {
        if (e.target === mobileSidebar) {
            mobileSidebar.classList.add('hidden');
        }
    });
</script>

@stack('scripts')
</body>
</html>
