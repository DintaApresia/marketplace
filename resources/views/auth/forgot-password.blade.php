<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-md p-6">

            {{-- Title --}}
            <h1 class="text-xl font-semibold text-gray-800 mb-1">
                Lupa Password?
            </h1>
            <p class="text-sm text-gray-600 mb-4">
                Masukkan email yang terdaftar. Kami akan mengirimkan link untuk mengatur ulang password Anda.
            </p>

            {{-- Session Status --}}
            <x-auth-session-status
                class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-700"
                :status="session('status')"
            />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <x-input-label
                        for="email"
                        value="Email"
                        class="text-sm text-gray-700"
                    />
                    <x-text-input
                        id="email"
                        class="mt-1 block w-full rounded-xl border-gray-300 focus:border-green-500 focus:ring-green-500"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                        placeholder="contoh@email.com"
                    />
                    <x-input-error
                        :messages="$errors->get('email')"
                        class="mt-1 text-xs"
                    />
                </div>

                {{-- Submit --}}
                <div>
                    <x-primary-button
                        class="w-full justify-center bg-green-600 hover:bg-green-700 focus:ring-green-500"
                    >
                        Kirim Link Reset Password
                    </x-primary-button>
                </div>
            </form>

            {{-- Back to login --}}
            <div class="mt-4 text-center text-sm">
                <a href="{{ route('login') }}"
                   class="text-green-600 hover:text-green-700 hover:underline">
                    Kembali ke Login
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>
