<x-guest-layout>
  <div class="w-full max-w-4xl mx-auto overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
    <div class="grid grid-cols-1 md:grid-cols-5">

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

      {{-- KANAN: FORM LOGIN --}}
      <div class="md:col-span-3 p-6 sm:p-8 md:p-10">
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
              class="w-full h-12 rounded-full border border-gray-300 px-5
                     focus:outline-none focus:ring-2 focus:ring-green-600"
            >
          </div>

          {{-- PASSWORD --}}
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
                class="w-full h-12 rounded-full border border-gray-300
                       px-5 pr-12
                       focus:outline-none focus:ring-2 focus:ring-green-600"
              >

              <button
                type="button"
                onclick="togglePassword()"
                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-700"
              >
                <svg id="eyeOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                    c4.478 0 8.268 2.943 9.542 7
                    -1.274 4.057-5.064 7-9.542 7
                    -4.477 0-8.268-2.943-9.542-7z"/>
                </svg>

                <svg id="eyeClosed" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-width="2" d="M3 3l18 18"/>
                </svg>
              </button>
            </div>
          </div>

          {{-- REMEMBER --}}
          <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2">
              <input type="checkbox" name="remember"
                     class="rounded border-gray-300 text-green-600">
              Ingat saya
            </label>

            <a href="{{ route('password.request') }}" class="text-green-700 hover:underline">
              Lupa sandi?
            </a>
          </div>

          {{-- BUTTON --}}
          <button
            type="submit"
            class="w-full h-12 rounded-full bg-green-700 hover:bg-green-800
                   text-yellow-300 font-semibold"
          >
            Masuk
          </button>

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

  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      document.getElementById('eyeOpen').classList.toggle('hidden');
      document.getElementById('eyeClosed').classList.toggle('hidden');
      input.type = input.type === 'password' ? 'text' : 'password';
    }
  </script>
</x-guest-layout>