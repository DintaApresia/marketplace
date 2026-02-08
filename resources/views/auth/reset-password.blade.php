<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-md p-6">

            {{-- Title --}}
            <h1 class="text-xl font-semibold text-gray-800 mb-1">
                Reset Password
            </h1>
            <p class="text-sm text-gray-600 mb-4">
                Silakan buat password baru untuk akun Anda.
            </p>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf

                {{-- Token --}}
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Email --}}
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input
                        id="email"
                        type="email"
                        name="email"
                        class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500"
                        :value="old('email', $request->email)"
                        required
                        autofocus
                        autocomplete="username"
                        readonly
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                </div>

                {{-- Password --}}
                <div x-data="{ show: false }">
                    <x-input-label for="password" value="Password Baru" />

                    <div class="relative mt-1">
                        <x-text-input
                            id="password"
                            name="password"
                            x-bind:type="show ? 'text' : 'password'"
                            class="block w-full rounded-md border-gray-300 pr-10
                                focus:border-green-500 focus:ring-green-500"
                            required
                            autocomplete="new-password"
                        />

                        {{-- Toggle eye --}}
                        <button
                            type="button"
                            @click="show = !show"
                            class="absolute inset-y-0 right-3 flex items-center
                                text-gray-500 hover:text-gray-700"
                        >
                            <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                </div>

                {{-- Confirm Password --}}
                <div x-data="{ show: false }">
                    <x-input-label for="password_confirmation" value="Konfirmasi Password" />

                    <div class="relative mt-1">
                        <x-text-input
                            id="password_confirmation"
                            name="password_confirmation"
                            x-bind:type="show ? 'text' : 'password'"
                            class="block w-full rounded-md border-gray-300 pr-10
                                focus:border-green-500 focus:ring-green-500"
                            required
                            autocomplete="new-password"
                        />

                        {{-- Toggle eye --}}
                        <button
                            type="button"
                            @click="show = !show"
                            class="absolute inset-y-0 right-3 flex items-center
                                text-gray-500 hover:text-gray-700"
                        >
                            <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
                </div>


                {{-- Submit --}}
                <div class="pt-2">
                    <x-primary-button
                        class="w-full justify-center bg-green-600 hover:bg-green-700 focus:ring-green-500"
                    >
                        Reset Password
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
