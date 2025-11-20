@extends('layouts.pembeli')

@section('title', 'Profile')

@section('content')
<div class="py-4">

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Profil</h1>
    <p class="text-sm text-gray-600 mb-4">
        Kelola informasi akun dan preferensi pengiriman sebagai pembeli.
    </p>

    {{-- (Opsional) tab navigasi --}}
    <div class="flex gap-4 border-b mb-6 pb-2 text-sm">
        <span class="text-green-700 font-semibold border-b-2 border-green-700">
            Pembeli
        </span>
    </div>

    {{-- include buyer (di dalamnya nanti ada Akun + Preferensi Pembeli) --}}
    @include('profile.partials.buyer', [
        'pembeli' => $pembeli,
        'user'    => $user,
    ])

</div>
@endsection
