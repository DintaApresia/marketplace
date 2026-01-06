@extends('layouts.admin')
@section('title', 'Edit User')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-4">
        <h1 class="text-2xl font-semibold text-gray-800">Edit User</h1>
        <p class="text-sm text-gray-500">Ubah data user: nama, email, role, dan status penjual (jika ada).</p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow border border-gray-100 p-6">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-5">
        @csrf
        @method('PATCH')

        {{-- Nama --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Nama</label>
            <input
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                required
                maxlength="255"
                class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
                placeholder="Nama user"
            >
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input
                type="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                required
                maxlength="255"
                class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
                placeholder="email@contoh.com"
            >
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password (opsional) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Ganti Password (opsional)</label>

            <div class="relative mt-1">
                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="8"
                    class="w-full rounded-md border px-3 py-2 pr-11 focus:ring-2 focus:ring-green-600"
                    placeholder="Kosongkan jika tidak ingin mengganti"
                    autocomplete="new-password"
                >

                <button type="button"
                    id="togglePassword"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
                    aria-label="Lihat password">
                    {{-- eye --}}
                    <svg id="eyeOpenPw" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7S2 12 2 12z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    {{-- eye-off --}}
                    <svg id="eyeClosedPw" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3l18 18"/>
                        <path d="M10.58 10.58A3 3 0 0 0 12 15a3 3 0 0 0 2.42-4.42"/>
                        <path d="M9.88 5.1A10.94 10.94 0 0 1 12 5c7 0 10 7 10 7a18.3 18.3 0 0 1-3.17 4.54"/>
                        <path d="M6.11 6.11A18.3 18.3 0 0 0 2 12s3 7 10 7a10.94 10.94 0 0 0 2.12-.2"/>
                    </svg>
                </button>
            </div>

            <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter.</p>
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>

            <div class="relative mt-1">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    minlength="8"
                    class="w-full rounded-md border px-3 py-2 pr-11 focus:ring-2 focus:ring-green-600"
                    placeholder="Ulangi password baru"
                    autocomplete="new-password"
                >

                <button type="button"
                    id="togglePasswordConfirm"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
                    aria-label="Lihat konfirmasi password">
                    {{-- eye --}}
                    <svg id="eyeOpenPwc" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7S2 12 2 12z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    {{-- eye-off --}}
                    <svg id="eyeClosedPwc" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3l18 18"/>
                        <path d="M10.58 10.58A3 3 0 0 0 12 15a3 3 0 0 0 2.42-4.42"/>
                        <path d="M9.88 5.1A10.94 10.94 0 0 1 12 5c7 0 10 7 10 7a18.3 18.3 0 0 1-3.17 4.54"/>
                        <path d="M6.11 6.11A18.3 18.3 0 0 0 2 12s3 7 10 7a10.94 10.94 0 0 0 2.12-.2"/>
                    </svg>
                </button>
            </div>

            @error('password_confirmation')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Role --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Role</label>
            @php $role = old('role', $user->role); @endphp
            <select
                name="role"
                required
                class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
            >
                <option value="" disabled {{ empty($role) ? 'selected' : '' }}>Pilih role...</option>
                <option value="pembeli" {{ $role === 'pembeli' ? 'selected' : '' }}>Pembeli</option>
                <option value="penjual" {{ $role === 'penjual' ? 'selected' : '' }}>Penjual</option>
                <option value="admin"  {{ $role === 'admin'  ? 'selected' : '' }}>Admin</option>
            </select>
            @error('role')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Seller Status --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Seller Status</label>
            @php $ss = old('seller_status', $user->seller_status); @endphp
            <select
                name="seller_status"
                required
                class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
            >
                <option value="" disabled {{ empty($ss) ? 'selected' : '' }}>Pilih status...</option>
                <option value="none"     {{ $ss === 'none' ? 'selected' : '' }}>None</option>
                <option value="pending"  {{ $ss === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="verified" {{ $ss === 'verified' ? 'selected' : '' }}>Verified</option>
                <option value="rejected" {{ $ss === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            @error('seller_status')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-2 pt-2">
            <a href="{{ route('admin.user') }}"
            class="px-4 py-2 rounded-md border text-sm hover:bg-gray-50">
                ← Kembali ke halaman user
            </a>
            <button type="submit"
                class="px-4 py-2 rounded-md bg-green-600 text-white text-sm hover:bg-green-700">
                Simpan
            </button>
        </div>
    </form>

    </div>
</div>

@endsection
{{-- Script: toggle + confirm required kalau password diisi --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pw  = document.getElementById('password');
    const pwc = document.getElementById('password_confirmation');

    function toggle(input, openIcon, closedIcon) {
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        openIcon.classList.toggle('hidden');
        closedIcon.classList.toggle('hidden');
    }

    // Toggle password
    document.getElementById('togglePassword').addEventListener('click', function () {
        toggle(pw, document.getElementById('eyeOpenPw'), document.getElementById('eyeClosedPw'));
    });

    // Toggle confirm
    document.getElementById('togglePasswordConfirm').addEventListener('click', function () {
        toggle(pwc, document.getElementById('eyeOpenPwc'), document.getElementById('eyeClosedPwc'));
    });

    // Kalau password diisi, confirm jadi required (biar tooltip HTML5 muncul)
    function syncRequired() {
        if (pw.value.trim().length > 0) {
            pwc.setAttribute('required', 'required');
        } else {
            pwc.removeAttribute('required');
            pwc.value = '';
        }
    }
    pw.addEventListener('input', syncRequired);
    syncRequired();
});
</script>