{{-- Profile > Account (umum untuk semua role) --}}
<div class="p-4 sm:p-6">
  <h2 class="text-lg font-semibold text-green-700">Akun</h2>
  <p class="text-sm text-gray-600">Perbarui informasi dasar akunmu.</p>

  <form method="POST" action="{{ route('profile.update') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
    @csrf
    @method('patch')

    {{-- Nama --}}
    <div>
      <label class="block text-sm text-gray-700">Nama</label>
      <input name="name" value="{{ old('name', $user->name ?? '') }}" required
             class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600">
      @error('name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div>
      <label class="block text-sm text-gray-700">Email</label>
      <input name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required
             class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600">
      @error('email') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2 flex items-center justify-between">
      @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}" class="text-sm text-green-700 hover:underline">
          Lupa password?
        </a>
      @endif
      <button class="rounded-md bg-green-700 text-white px-4 py-2 hover:bg-green-800">
        Simpan Perubahan
      </button>
    </div>
  </form>
</div>
