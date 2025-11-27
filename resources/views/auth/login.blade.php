<x-guest-layout>
  <!-- Card: 2 kolom, tanpa margin atas/bawah -->
  <div class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-black/5">
    <div class="grid md:grid-cols-5">
      <!-- Kiri: gambar -->
      <div class="relative md:col-span-2 h-40 md:h-auto">
        <img src="{{ asset('images/auth-side.jpg') }}"
             onerror="this.src='https://images.unsplash.com/photo-1512436991641-6745cdb1723f?q=80&w=1200&auto=format&fit=crop'"
             class="absolute inset-0 h-full w-full object-cover" alt="Secondlife">
      </div>

      <!-- Kanan: form -->
      <div class="md:col-span-3 p-8 md:p-10">
        <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center md:text-left">Secondlife</h1>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
          @csrf

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2
                          focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600">
            @error('email') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" name="password" type="password" required
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2
                          focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600">
            @error('password') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div class="flex items-center justify-between text-sm">
            <label class="inline-flex items-center gap-2 text-gray-700">
              <input type="checkbox" name="remember"
                     class="rounded border-gray-300 text-green-600 focus:ring-green-600">
              Ingat saya
            </label>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="text-green-700 hover:underline">
                Lupa sandi?
              </a>
            @endif
          </div>

          <button type="submit"
            class="w-full rounded-lg bg-green-700 hover:bg-green-800 text-yellow-300 font-semibold py-2.5
                  focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">
            Masuk
          </button>


          <p class="text-center text-sm text-gray-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-green-700 hover:underline">Daftar</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</x-guest-layout>
