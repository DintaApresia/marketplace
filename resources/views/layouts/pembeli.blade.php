<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','SecondLife — Pembeli')</title>

  @vite(['resources/css/app.css','resources/js/app.js'])

  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  <script defer
          src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  @stack('styles')
</head>

<body class="bg-white text-gray-900 overflow-x-hidden">

{{-- ================= FIXED HEADER ================= --}}
<header
  x-data="{ open:false }"
  class="fixed top-0 left-0 right-0 z-50
         bg-white border-b w-full"
>
  <div class="max-w-7xl mx-auto px-8">

    {{-- DESKTOP HEADER --}}
    <div class="hidden md:flex h-14 items-center gap-8">

      {{-- LOGO --}}
      <a href="{{ route('pembeli.dashboard') }}"
         class="text-lg font-bold text-green-700 shrink-0">
        SecondLife
      </a>

      {{-- SPACER HALUS (GESER KE KANAN) --}}
      <div class="flex-[0.4]"></div>

      {{-- SEARCH --}}
      @if (request()->routeIs('pembeli.dashboard','pembeli.search','pembeli.produk.detail'))
        <form action="{{ route('pembeli.search') }}"
              method="GET"
              class="relative w-80 shrink-0">
          <input
            name="q"
            type="search"
            value="{{ request('q') }}"
            placeholder="Cari produk..."
            class="w-full rounded-full bg-gray-100 py-1.5 pl-4 pr-9 text-sm
                   focus:ring-2 focus:ring-green-500 outline-none">
          <button
            type="submit"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
            <i class="bi bi-search"></i>
          </button>
        </form>
      @endif

      {{-- SPACER KANAN --}}
      <div class="flex-[1]"></div>

      {{-- MENU --}}
      <nav class="flex items-center gap-6 text-sm shrink-0">
        <a href="{{ route('pembeli.dashboard') }}"
           class="{{ request()->routeIs('pembeli.dashboard')
              ? 'text-green-700 font-semibold border-b-2 border-green-700 pb-1'
              : 'text-gray-700 hover:text-green-700' }}">
          Home
        </a>

        <a href="{{ route('pembeli.keranjang') }}"
           data-login-required
           class="{{ request()->routeIs('pembeli.keranjang')
              ? 'text-green-700 font-semibold border-b-2 border-green-700 pb-1'
              : 'text-gray-700 hover:text-green-700' }}">
          Keranjang
        </a>

        <a href="{{ route('pembeli.profile') }}"
           data-login-required
           class="{{ request()->routeIs('pembeli.profile')
              ? 'text-green-700 font-semibold border-b-2 border-green-700 pb-1'
              : 'text-gray-700 hover:text-green-700' }}">
          Profile
        </a>
      </nav>

      {{-- AVATAR --}}
      @auth
        <img
          src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=22c55e&color=ffffff"
          class="w-8 h-8 rounded-full border ml-3"
          alt="Avatar">
      @endauth

      <form action="/logout" method="POST" onsubmit="return confirm('Yakin ingin keluar?')">
          @csrf
          <button class="w-full flex items-center gap-3 px-4 py-2 rounded-lg
                          text-red-400 hover:bg-red-500 hover:text-white transition text-sm">
              🚪 Logout
          </button>
      </form>
    </div>

    {{-- MOBILE HEADER (AMAN, TIDAK DIUBAH) --}}
    <div class="flex md:hidden h-14 items-center justify-between">
      <button @click="open = !open" class="text-2xl">
        <i class="bi bi-list"></i>
      </button>

      <a href="{{ route('pembeli.dashboard') }}"
         class="font-bold text-green-700">
        SecondLife
      </a>

      @auth
        <img
          src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}"
          class="w-8 h-8 rounded-full">
      @endauth
    </div>

  </div>

  {{-- MOBILE MENU --}}
  <div x-show="open" x-transition @click.outside="open=false"
       class="md:hidden border-t bg-white">
    <div class="px-4 py-4 space-y-3 text-sm">
      <a href="{{ route('pembeli.dashboard') }}" class="block">Home</a>
      <a href="{{ route('pembeli.keranjang') }}" class="block">Keranjang</a>
      <a href="{{ route('pembeli.profile') }}" class="block">Profile</a>

      {{-- SEARCH MOBILE --}}
      @if (request()->routeIs('pembeli.dashboard','pembeli.search','pembeli.produk.detail'))
      <form action="{{ route('pembeli.search') }}" method="GET" class="pt-3">
        <input
          name="q"
          value="{{ request('q') }}"
          placeholder="Cari produk..."
          class="w-full rounded-md bg-gray-100 py-2 px-3 text-sm
                 focus:ring-2 focus:ring-green-500 outline-none">
      </form>
      @endif

      @auth
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="w-full rounded-md bg-red-700 py-2 text-white">
            Logout
          </button>
        </form>
      @endauth

    </div>
  </div>
</header>

{{-- ===== SPACER (PENGGANTI STICKY) ===== --}}
<div class="h-14"></div>

{{-- ================= CONTENT ================= --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
  @yield('content')
</main>

{{-- ================= FOOTER ================= --}}
<footer class="border-t py-6 text-center text-xs text-gray-500">
  © {{ now()->year }} SecondLife. All rights reserved.
</footer>

<script>
document.addEventListener('click', function (e) {
  const el = e.target.closest('[data-login-required]');
  if (!el) return;

  @if(auth()->guest())
    e.preventDefault();
    alert('Silakan login terlebih dahulu');
    window.location.href = "{{ route('login') }}";
  @endif
});
</script>

@stack('scripts')
</body>
</html>
