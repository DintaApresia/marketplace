<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Penjual - @yield('title')</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen">

    {{-- HEADER (FULL WIDTH) --}}
    <header class="bg-white shadow px-6 py-4 flex justify-between items-center fixed top-0 left-0 right-0 z-20">
        <h2 class="text-lg font-semibold text-gray-800">
            @yield('title')
        </h2>

        <div class="flex items-center gap-4 text-sm text-gray-600">
            <span class="font-medium">{{ auth()->user()->name }}</span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button
                    type="submit"
                    class="px-3 py-1.5 bg-red-500 text-white rounded-md text-xs hover:bg-red-600">
                    Logout
                </button>
            </form>
        </div>
    </header>

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white h-screen shadow-lg fixed top-0 left-0 pt-[64px] z-10">

        <div class="p-4 border-b">
            <h1 class="text-xl font-bold text-green-700">Panel Penjual</h1>
            <p class="text-xs text-gray-500">SecondLife Marketplace</p>
        </div>

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

            <a href="#"
               class="block px-3 py-2 rounded-md transition hover:bg-green-50 text-gray-700">
                Pesanan Masuk
            </a>

        </nav>
    </aside>

    {{-- CONTENT --}}
    <main class="ml-64 pt-[80px] p-6">
        @yield('content')
    </main>

</body>
</html>
