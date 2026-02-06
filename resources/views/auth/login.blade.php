<x-guest-layout>
  <div class="w-full max-w-4xl mx-auto overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
    <div class="grid grid-cols-1 md:grid-cols-5">

      {{-- KIRI / ATAS: GAMBAR --}}
      <div
        class="relative md:col-span-2
               h-56 md:h-auto
               bg-green-50
               flex items-center justify-center"
      >
        <img
          src="{{ asset('images/login-shopping-cart.png') }}"
          alt="Shopping illustration"
          class="w-full h-full object-contain p-6 md:p-8"
        >
      </div>

      {{-- KANAN: FORM LOGIN --}}
      <div class="md:col-span-3 p-6 sm:p-8 md:p-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-4 text-center md:text-left">
          Welcome back!
        </h1>

        {{-- ================= ALERT COMPONENT ================= --}}
        @if ($errors->has('email'))
          <div class="mb-4 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <svg class="w-5 h-5 mt-0.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v2m0 4h.01M5.93 19h12.14c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L4.2 16c-.77 1.33.19 3 1.73 3z"/>
            </svg>

            <span>
              {{-- Override pesan Breeze --}}
              @if ($errors->first('email') === __('auth.failed'))
                Email atau password salah.
              @else
                {{ $errors->first('email') }}
              @endif
            </span>
          </div>
        @endif
        {{-- ================= END ALERT ================= --}}

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
              class="w-full h-12 rounded-full px-5
                     border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}
                     focus:outline-none focus:ring-2 focus:ring-green-600"
            >
          </div>

          {{-- PASSWORD --}}
          <div x-data="{ show: false }">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Password
            </label>

            <div class="relative">
              <input
                id="password"
                name="password"
                :type="show ? 'text' : 'password'"
                required
                autocomplete="current-password"
                placeholder="Password"
                class="w-full h-12 rounded-full px-5 pr-12
                       border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}
                       focus:outline-none focus:ring-2 focus:ring-green-600"
              >

              {{-- Toggle eye --}}
              <button
                type="button"
                @click="show = !show"
                class="absolute right-4 top-1/2 -translate-y-1/2
                       z-10 text-gray-500 hover:text-gray-700"
              >
                <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
              </button>
            </div>
          </div>

          {{-- FORGOT --}}
          <div class="flex justify-end text-sm">
            <a href="{{ route('password.request') }}" class="text-green-700 hover:underline">
              Lupa sandi?
            </a>
          </div>

          {{-- BUTTON --}}
          <button
            type="submit"
            class="w-full h-12 rounded-full bg-green-700 hover:bg-green-800
                   text-white font-semibold transition"
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
</x-guest-layout>
