<x-guest-layout>
  <div class="min-h-screen grid place-items-center bg-gradient-to-b from-green-600 to-emerald-600 px-4">
    <div class="w-full max-w-sm text-center">

      {{-- Status (mis. link reset terkirim) --}}
      <x-auth-session-status class="mb-4" :status="session('status')" />

      <h1 class="text-3xl font-bold text-yellow-300 mb-6">SecondLife</h1>

      <form method="POST" action="{{ route('login') }}"
            class="space-y-4 rounded-xl p-5 bg-white/10 ring-2 ring-white/30">
        @csrf

        <!-- Email -->
        <div class="text-left">
          <label for="email" class="block text-sm text-white/90 mb-1">Email</label>
          <input id="email" type="email" name="email" autocomplete="username" value="{{ old('email') }}" required autofocus
                 class="w-full rounded-md bg-white text-gray-900 placeholder:text-gray-400
                        ring-1 ring-gray-300 focus:ring-2 focus:ring-yellow-300 focus:outline-none
                        px-3 py-2">
          @error('email') <p class="text-yellow-200 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Password -->
        <div class="text-left">
          <label for="password" class="block text-sm text-white/90 mb-1">Password</label>
          <input id="password" type="password" name="password" autocomplete="current-password" required
                 class="w-full rounded-md bg-white text-gray-900 placeholder:text-gray-400
                        ring-1 ring-gray-300 focus:ring-2 focus:ring-yellow-300 focus:outline-none
                        px-3 py-2">
          @error('password') <p class="text-yellow-200 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Ingat saya + Lupa sandi -->
        <div class="flex items-center justify-between text-sm">
          <label class="inline-flex items-center gap-2 text-white/90">
            <input type="checkbox" name="remember"
                   class="rounded border-yellow-300 text-yellow-400 focus:ring-yellow-300">
            Ingat saya
          </label>

          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-yellow-200 hover:underline">
              Lupa sandi?
            </a>
          @endif
        </div>

        <!-- Tombol -->
        <button type="submit"
                class="w-full rounded-md bg-yellow-400 hover:bg-yellow-500 text-green-900 font-semibold py-3">
          Masuk
        </button>

        <!-- Daftar -->
        <p class="text-sm text-white/90">
          Belum punya akun?
          <a href="{{ route('register') }}" class="text-yellow-300 font-medium hover:underline">Daftar di sini</a>
        </p>
      </form>
    </div>
  </div>
</x-guest-layout>
