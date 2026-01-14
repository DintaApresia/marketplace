<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','SecondLife — Marketplace')</title>

  @vite(['resources/css/app.css','resources/js/app.js'])

  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  @stack('styles')
</head>

<body class="bg-gray-50 text-gray-900 antialiased">

{{-- ================= NAVBAR PUBLIC ================= --}}
<header class="sticky top-0 z-50 bg-white border-b">
  <div class="max-w-7xl mx-auto h-20 px-6
              flex items-center justify-between">

    {{-- LEFT : BRAND --}}
    <a href="{{ route('dashboard.public') }}"
       class="text-2xl font-bold text-green-700 tracking-tight">
      SecondLife
    </a>

    {{-- RIGHT : AUTH --}}
    <div>
      @guest
        <a href="{{ route('login') }}"
           class="rounded-lg bg-green-700 px-4 py-2
                  text-sm font-semibold text-white
                  hover:bg-green-800 transition">
          Sign In
        </a>
      @endguest

      @auth
        <a href="{{ route('dashboard.auth') }}"
           class="rounded-lg bg-green-700 px-4 py-2
                  text-sm font-semibold text-white
                  hover:bg-green-800 transition">
          Dashboard Saya
        </a>
      @endauth
    </div>

  </div>
</header>


{{-- ================= CONTENT ================= --}}
<main class="max-w-7xl mx-auto px-6 py-12">
  @yield('content')
</main>

{{-- ================= FOOTER ================= --}}
<footer class="border-t bg-white">
  <div class="max-w-7xl mx-auto px-6 py-8 text-sm text-gray-500">
    © {{ now()->year }} SecondLife. All rights reserved.
  </div>
</footer>

@stack('scripts')
</body>
</html>
