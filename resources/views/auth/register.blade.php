<x-guest-layout>
  <!-- Card landscape: image kiri, form kanan (match login) -->
  <div class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
    <div class="grid md:grid-cols-5">
      {{-- KIRI / ATAS: GAMBAR KARTUN --}}
      <div
        class="relative md:col-span-2
               h-56 md:h-auto
               bg-green-50
               flex items-center justify-center"
      >
        <img
          src="{{ asset('images/login-shopping-cart.png') }}"
          alt="Shopping illustration"
          class="
            w-full h-full
            object-contain
            p-6
            md:p-8
          "
        >
      </div>

      {{-- KANAN: form register --}}
      <div class="md:col-span-3 p-8 md:p-10">
        <h1 class="text-2xl font-bold text-gray-900 text-center md:text-left">Secondlife</h1>
        <h2 class="text-xl font-semibold text-gray-900 mt-1 mb-6 text-center md:text-left">Daftar</h2>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
          @csrf

          {{-- Nama --}}
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
            <input id="name" name="name" type="text" autocomplete="name" required autofocus
                   value="{{ old('name') }}"
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2
                          focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600">
            @error('name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
          </div>

          {{-- Email --}}
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email" autocomplete="username" required
                   value="{{ old('email') }}"
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2
                          focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600">
            @error('email') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
          </div>

          {{-- Password --}}
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2
                          focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600">
            @error('password') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
          </div>

          {{-- Konfirmasi Password --}}
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2
                          focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600">
            @error('password_confirmation') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
          </div>

          {{-- Tombol --}}
          <button type="submit"
                  class="w-full rounded-lg bg-yellow-400 hover:bg-yellow-500 text-green-900 font-semibold py-2.5">
            Daftar
          </button>

          {{-- Link ke login --}}
          <p class="text-center text-sm text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-green-700 hover:underline">Masuk</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</x-guest-layout>
