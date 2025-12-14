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
        <form method="POST" action="{{ route('admin.users.update',$user->id) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" name="name"
                    value="{{ old('name', $user->name) }}"
                    class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
                    placeholder="Nama user">
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email"
                    value="{{ old('email', $user->email) }}"
                    class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600"
                    placeholder="email@contoh.com">
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role"
                    class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600">
                    @php $role = old('role', $user->role); @endphp
                    <option value="pembeli" {{ $role === 'pembeli' ? 'selected' : '' }}>Pembeli</option>
                    <option value="penjual" {{ $role === 'penjual' ? 'selected' : '' }}>Penjual</option>
                    <option value="admin"  {{ $role === 'admin'  ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            {{-- Seller Status (opsional, kalau field ada) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Seller Status (opsional)</label>
                <select name="seller_status"
                    class="mt-1 w-full rounded-md border px-3 py-2 focus:ring-2 focus:ring-green-600">
                    @php $ss = old('seller_status', $user->seller_status); @endphp
                    <option value="" {{ empty($ss) ? 'selected' : '' }}>- Tidak diubah -</option>
                    <option value="pending"  {{ $ss === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $ss === 'approved' ? 'selected' : '' }}>Verified</option>
                    <option value="rejected" {{ $ss === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Hapus bagian ini kalau kolomnya tidak ada di tabel users.</p>
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
