@extends('layouts.admin')
@section('title', 'Kelola User')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

  {{-- Alert --}}
  @if (session('success'))
    <div class="mb-4 p-3 rounded bg-green-100 text-green-800 border border-green-200">
      {{ session('success') }}
    </div>
  @endif
  @if (session('error'))
    <div class="mb-4 p-3 rounded bg-red-100 text-red-800 border border-red-200">
      {{ session('error') }}
    </div>
  @endif

  <div class="bg-white rounded-lg shadow border border-gray-100 p-6">
    <div class="flex items-start justify-between gap-4 mb-5">
      <div>
        <h1 class="text-xl font-semibold text-gray-800">Kelola Akun User</h1>
        <p class="text-sm text-gray-500">Cari user, filter role/status penjual, dan kelola akun.</p>
      </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-5">
      <input
        type="text"
        name="q"
        value="{{ request('q') }}"
        placeholder="Cari nama / email..."
        class="rounded-md border px-3 py-2 text-sm focus:ring-2 focus:ring-green-600"
      >

      <select
        name="role"
        class="rounded-md border px-3 py-2 text-sm focus:ring-2 focus:ring-green-600"
      >
        <option value="">Semua Role</option>
        <option value="admin"   @selected(request('role')==='admin')>Admin</option>
        <option value="penjual" @selected(request('role')==='penjual')>Penjual</option>
        <option value="pembeli" @selected(request('role')==='pembeli')>Pembeli</option>
      </select>

      <select
        name="seller_status"
        class="rounded-md border px-3 py-2 text-sm focus:ring-2 focus:ring-green-600"
      >
        <option value="">Semua Seller Status</option>
        <option value="none"     @selected(request('seller_status')==='none')>none</option>
        <option value="pending"  @selected(request('seller_status')==='pending')>pending</option>
        <option value="verified" @selected(request('seller_status')==='verified')>verified</option>
        <option value="rejected" @selected(request('seller_status')==='rejected')>rejected</option>
      </select>

      <div class="flex gap-2">
        <button class="w-full rounded-md bg-green-600 text-white px-3 py-2 text-sm hover:bg-green-700">
          Terapkan
        </button>
        <a
          href="{{ url()->current() }}"
          class="w-full text-center rounded-md border px-3 py-2 text-sm hover:bg-gray-50"
        >
          Reset
        </a>
      </div>
    </form>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-lg border border-gray-100">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="text-left px-4 py-3">User</th>
            <th class="text-left px-4 py-3">Role</th>
            <th class="text-left px-4 py-3">Seller Status</th>
            <th class="text-left px-4 py-3">Terdaftar</th>
            <th class="text-right px-4 py-3">Aksi</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @forelse ($users as $u)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <div class="font-medium text-gray-800">{{ $u->name }}</div>
                <div class="text-xs text-gray-500">{{ $u->email }}</div>
              </td>

              <td class="px-4 py-3">
                <span class="inline-flex items-center px-2 py-1 rounded text-xs
                  {{ $u->role==='admin' ? 'bg-purple-100 text-purple-700' : '' }}
                  {{ $u->role==='penjual' ? 'bg-blue-100 text-blue-700' : '' }}
                  {{ $u->role==='pembeli' ? 'bg-gray-100 text-gray-700' : '' }}
                ">
                  {{ ucfirst($u->role) }}
                </span>
              </td>

              <td class="px-4 py-3">
                <span class="inline-flex items-center px-2 py-1 rounded text-xs
                  {{ $u->seller_status==='verified' ? 'bg-green-100 text-green-700' : '' }}
                  {{ $u->seller_status==='pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                  {{ $u->seller_status==='rejected' ? 'bg-red-100 text-red-700' : '' }}
                  {{ $u->seller_status==='none' ? 'bg-gray-100 text-gray-700' : '' }}
                ">
                  {{ $u->seller_status }}
                </span>
              </td>

              <td class="px-4 py-3 text-gray-600">
                {{ optional($u->created_at)->format('d M Y') }}
              </td>

              <td class="px-4 py-3">
                <div class="flex justify-end gap-2">
                  {{-- Edit (opsional) --}}
                  @if (Route::has('admin.users.edit'))
                    <a href="{{ route('admin.users.edit', $u->id) }}"
                      class="px-3 py-1.5 rounded border text-xs hover:bg-white">
                      Edit
                    </a>
                  @endif

                  {{-- Quick Update Role + Seller Status (butuh route admin.users.update) --}}
                  @if (Route::has('admin.users.update'))
                    <form method="POST" action="{{ route('admin.users.update', $u->id) }}" class="flex gap-2 items-center">
                      @csrf
                      @method('PATCH')

                      <select name="role" class="rounded-md border px-2 py-1 text-xs">
                        <option value="admin"   @selected($u->role==='admin')>admin</option>
                        <option value="penjual" @selected($u->role==='penjual')>penjual</option>
                        <option value="pembeli" @selected($u->role==='pembeli')>pembeli</option>
                      </select>

                      <select name="seller_status" class="rounded-md border px-2 py-1 text-xs">
                        <option value="none"     @selected($u->seller_status==='none')>none</option>
                        <option value="pending"  @selected($u->seller_status==='pending')>pending</option>
                        <option value="verified" @selected($u->seller_status==='verified')>verified</option>
                        <option value="rejected" @selected($u->seller_status==='rejected')>rejected</option>
                      </select>

                      <button class="px-3 py-1.5 rounded bg-green-600 text-white text-xs hover:bg-green-700">
                        Simpan
                      </button>
                    </form>
                  @endif

                  {{-- Hapus (opsional) --}}
                  @if (Route::has('admin.users.destroy'))
                    <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}"
                          onsubmit="return confirm('Yakin hapus user ini?');">
                      @csrf
                      @method('DELETE')
                      <button class="px-3 py-1.5 rounded bg-red-600 text-white text-xs hover:bg-red-700">
                        Hapus
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                Tidak ada data user.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if (method_exists($users, 'links'))
      <div class="mt-4">
        {{ $users->links() }}
      </div>
    @endif

  </div>
</div>
@endsection
