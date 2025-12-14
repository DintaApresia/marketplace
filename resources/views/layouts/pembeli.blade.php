<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','SecondLife — Pembeli')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])

  {{-- Bootstrap Icons --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-white text-gray-900">

  {{-- NAVBAR --}}
  <header class="sticky top-0 z-30 bg-white border-b">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">

      {{-- BRAND --}}
      <a href="{{ route('pembeli.dashboard') }}" class="text-lg font-semibold text-green-700">
        SecondLife
      </a>

      {{-- MENU --}}
      <nav class="hidden md:flex items-center gap-6 text-sm">
        <a href="{{ route('pembeli.dashboard') }}"
           class="{{ request()->routeIs('pembeli.dashboard') ? 'text-green-700 font-semibold border-b-2 border-green-700 pb-1' : 'text-gray-700 hover:text-green-700' }}">
          Home
        </a>

        <a href="{{ route('pembeli.keranjang') }}"
           class="{{ request()->routeIs('pembeli.keranjang') ? 'text-green-700 font-semibold border-b-2 border-green-700 pb-1' : 'text-gray-700 hover:text-green-700' }}">
          Keranjang
        </a>

        <a href="{{ route('pembeli.profile') }}"
           class="{{ request()->routeIs('pembeli.profile') ? 'text-green-700 font-semibold border-b-2 border-green-700 pb-1' : 'text-gray-700 hover:text-green-700' }}">
          Profile
        </a>
      </nav>

      {{-- SEARCH hanya di dashboard --}}
      @if (request()->routeIs('pembeli.dashboard'))
      <form
          action="{{ route('pembeli.hasilpencarian') }}"  {{-- ⬅️ pindah ke halaman hasil --}}
          method="GET"
          class="relative flex items-center w-64 mx-4"
          role="search"
      >
          <input 
            name="q"
            type="search"
            placeholder="Cari produk..."
            class="w-full rounded-full bg-gray-100 py-1.5 pl-4 pr-10 text-sm shadow-sm 
                  focus:ring-2 focus:ring-green-500 outline-none"
          >

          <button type="submit" class="absolute right-3 text-gray-500 hover:text-green-700">
            <i class="bi bi-search text-lg"></i>
          </button>
      </form>
      @endif

      {{-- AUTH --}}
      <div class="flex items-center gap-3">
        @auth
        <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Yakin ingin keluar?')">
          @csrf
          <button class="rounded-md bg-red-700 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-800">
            Logout
          </button>
        </form>
        @endauth

        @guest
        <a href="{{ route('login') }}"
           class="rounded-md bg-green-700 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-800">
          Sign In
        </a>
        @endguest
      </div>

    </div>
  </header>

  {{-- KONTEN --}}
  <main class="mx-auto @yield('maxwidth','max-w-7xl') px-4 sm:px-6 lg:px-8 py-6">
    @yield('content')
  </main>
  
  {{-- FOOTER --}}
  <footer class="mt-10 border-t py-6 text-center text-sm text-gray-500">
    © {{ now()->year }} SecondLife. All rights reserved.
  </footer>

</body>
</html>
