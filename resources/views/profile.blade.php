@extends('layouts.pembeli')
@section('title', 'Profile')

@section('content')
<div class="py-4">

  <h1 class="text-2xl font-bold text-gray-800 mb-2">Profil</h1>
  <p class="text-sm text-gray-600 mb-4">
    Kelola informasi akun dan preferensi pengiriman sebagai pembeli.
  </p>

  @include('profile.partials.buyer', [
      'user'    => $user,
      'pembeli' => $pembeli,
  ])
</div>
@endsection