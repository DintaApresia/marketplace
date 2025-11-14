<x-guest-layout>
    <!-- Heading -->
    <div class="text-center mb-6">
        <h1 class="text-3xl font-semibold text-yellow-300">SecondLife</h1>
        <p class="text-green-100 text-sm">Daftar untuk mulai</p>
    </div>

    <form method="POST" action="{{ route('register') }}"
          class="space-y-5 rounded-xl p-5 bg-white/10 ring-1 ring-white/15">
        @csrf

        <!-- Nama -->
        <div>
            <x-input-label for="name" :value="__('Nama')" class="text-white/90" />
            <x-text-input id="name" name="name" type="text" autocomplete="name" required autofocus
                :value="old('name')"
                class="mt-1 block w-full rounded-md bg-white text-gray-900 placeholder:text-gray-400
                       ring-1 ring-gray-300 focus:ring-2 focus:ring-yellow-300 focus:outline-none px-3 py-2"/>
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-yellow-200" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-white/90" />
            <x-text-input id="email" name="email" type="email" autocomplete="username" required
                :value="old('email')"
                class="mt-1 block w-full rounded-md bg-white text-gray-900 placeholder:text-gray-400
                       ring-1 ring-gray-300 focus:ring-2 focus:ring-yellow-300 focus:outline-none px-3 py-2"/>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-yellow-200" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-white/90" />
            <x-text-input id="password" name="password" type="password" autocomplete="new-password" required
                class="mt-1 block w-full rounded-md bg-white text-gray-900 placeholder:text-gray-400
                       ring-1 ring-gray-300 focus:ring-2 focus:ring-yellow-300 focus:outline-none px-3 py-2"/>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-yellow-200" />
        </div>

        <!-- Konfirmasi Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-white/90" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                autocomplete="new-password" required
                class="mt-1 block w-full rounded-md bg-white text-gray-900 placeholder:text-gray-400
                       ring-1 ring-gray-300 focus:ring-2 focus:ring-yellow-300 focus:outline-none px-3 py-2"/>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-yellow-200" />
        </div>

        <!-- Aksi -->
        <div class="flex items-center justify-between">
            <a href="{{ route('login') }}" class="text-sm text-yellow-200 hover:underline">
                Sudah punya akun? Masuk
            </a>

            <!-- Tombol Kuning -->
            <x-primary-button
                class="bg-yellow-400 hover:bg-yellow-500 text-green-900 font-semibold
                       px-4 py-2.5 rounded-md border-0 focus:ring-2 focus:ring-yellow-300 focus:ring-offset-0">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
