<x-guest-layout>
  <div class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
    <div class="grid md:grid-cols-5">

      {{-- KIRI: GAMBAR --}}
      <div class="relative md:col-span-2 h-40 md:h-auto">
        <img
          src="{{ asset('images/auth-side.jpg') }}"
          onerror="this.src='https://images.unsplash.com/photo-1512436991641-6745cdb1723f?q=80&w=1200&auto=format&fit=crop'"
          class="absolute inset-0 h-full w-full object-cover"
          alt="Secondlife"
        >
        <div class="absolute inset-0 bg-black/10"></div>
      </div>

      {{-- KANAN: FORM --}}
      <div class="md:col-span-3 p-8 md:p-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center md:text-left">
          Welcome back!
        </h1>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
          @csrf

          {{-- EMAIL --}}
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email
            </label>
            <input
              id="email"
              name="email"
              type="email"
              value="{{ old('email') }}"
              required
              autofocus
              autocomplete="username"
              placeholder="Email"
              class="w-full h-12 rounded-full border border-gray-300 bg-white px-5
                     shadow-sm
                     focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600"
            >
            @error('email')
              <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- PASSWORD + EYE TOGGLE --}}
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Password
            </label>

            <div class="relative">
              <input
                id="password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
                placeholder="Password"
                class="w-full h-12 rounded-full border border-gray-300 bg-white
                       px-5 pr-12 shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600"
              >

              <button
                type="button"
                onclick="togglePassword()"
                class="absolute right-4 top-1/2 -translate-y-1/2
                       text-gray-400 hover:text-green-700 transition"
                aria-label="Toggle password"
              >
                {{-- EYE OPEN --}}
                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5
                           c4.478 0 8.268 2.943 9.542 7
                           -1.274 4.057-5.064 7-9.542 7
                           -4.477 0-8.268-2.943-9.542-7z"/>
                </svg>

                {{-- EYE CLOSED --}}
                <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19
                           c-4.478 0-8.268-2.943-9.543-7
                           a9.97 9.97 0 012.642-4.362M6.223 6.223
                           A9.97 9.97 0 0112 5
                           c4.478 0 8.268 2.943 9.543 7
                           a9.97 9.97 0 01-4.132 5.411M15 12
                           a3 3 0 00-4.243-2.828M9.88 9.88
                           a3 3 0 104.243 4.243M3 3l18 18"/>
                </svg>
              </button>
            </div>

            @error('password')
              <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- REMEMBER + FORGOT --}}
          <div class="flex items-center justify-between text-sm">
            <label class="inline-flex items-center gap-2 text-gray-700">
              <input
                type="checkbox"
                name="remember"
                class="rounded border-gray-300 text-green-600 focus:ring-green-600"
              >
              Ingat saya
            </label>

            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}"
                 class="text-green-700 hover:underline">
                Lupa sandi?
              </a>
            @endif
          </div>

          {{-- BUTTON --}}
          <button
            type="submit"
            class="w-full h-12 rounded-full bg-green-700 hover:bg-green-800
                   text-yellow-300 font-semibold
                   focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2"
          >
            Masuk
          </button>

          {{-- REGISTER --}}
          <p class="text-center text-sm text-gray-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-green-700 hover:underline">
              Daftar
            </a>
          </p>
        </form>
      </div>
    </div>
  </div>

  {{-- SCRIPT TOGGLE PASSWORD --}}
  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const eyeOpen = document.getElementById('eyeOpen');
      const eyeClosed = document.getElementById('eyeClosed');

      if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
      } else {
        input.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
      }
    }
  </script>
</x-guest-layout>
