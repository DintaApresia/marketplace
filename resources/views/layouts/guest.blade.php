<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="font-sans antialiased">
    <!-- FULL SCREEN GREEN BACKGROUND -->
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-green-600 to-emerald-600 px-4">
      <div class="w-full max-w-md">
        <!-- Logo (opsional) -->
        <div class="flex justify-center mb-6">
          <a href="/">
            <x-application-logo class="w-16 h-16 fill-current text-white" />
          </a>
        </div>

        <!-- Tampilkan konten halaman (login/register) -->
        {{ $slot }}
      </div>
    </div>
  </body>
</html>
