<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','SecondLife — Pembeli')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-white text-gray-900">

  {{-- NAVBAR (tetap) --}}
  <header class="sticky top-0 z-30 bg-white border-b">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between gap-4">
      <a href="{{ route('pembeli.dashboard') }}" class="text-lg font-semibold text-green-700">SecondLife</a>

      <nav class="hidden md:flex items-center gap-6 text-sm">
        <a href="{{ route('pembeli.dashboard') }}"
           class="{{ request()->routeIs('pembeli.dashboard') ? 'text-green-700 font-semibold border-b-2 border-green-700 pb-1' : 'text-gray-700 hover:text-green-700' }}">
          Home
        </a>
        <a href="{{ route('pembeli.keranjang') }}"
           class="{{ request()->routeIs('pembeli.keranjang') ? 'text-green-700 font-semibold border-b-2 border-green-700 pb-1' : 'text-gray-700 hover:text-green-700' }}">
          Keranjang
        </a>
        <a href="{{ route('pembeli.orders') }}"
           class="{{ request()->routeIs('pembeli.orders') ? 'text-green-700 font-semibold border-b-2 border-green-700 pb-1' : 'text-gray-700 hover:text-green-700' }}">
          Orders
        </a>
      </nav>

      <div class="flex items-center gap-3">
        @auth
          <form method="POST" action="{{ route('logout') }}" class="inline"
                onsubmit="return confirm('Yakin ingin keluar dari akun?')">
            @csrf
            <button type="submit"
              class="inline-flex items-center rounded-md bg-red-700 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-800">
              Logout
            </button>
          </form>
        @endauth
        @guest
          <a href="{{ route('login') }}"
             class="inline-flex items-center rounded-md bg-green-700 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-800">
            Sign In
          </a>
        @endguest
      </div>
    </div>
  </header>

  {{-- KONTEN HALAMAN --}}
  <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    @yield('content')
  </main>

  {{-- FOOTER (opsional, tetap) --}}
  <footer class="mt-10 border-t py-6 text-center text-sm text-gray-500">
    © {{ now()->year }} SecondLife. All rights reserved.
  </footer>
</body>
</html>
