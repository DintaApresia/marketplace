@extends($layout)

@section('title','Profil — SecondLife')

@section('content')
  @if (session('status'))
    <div class="mt-6 rounded-md bg-green-50 text-green-800 px-4 py-2 text-sm">
      {{ session('status') }}
    </div>
  @endif

  <div class="mt-6 grid gap-6 lg:grid-cols-3">
    <section class="lg:col-span-2 rounded-xl border bg-white">
      @include('profile.partials.account', ['user' => $user])
    </section>

    <aside class="rounded-xl border bg-white p-4">
      <h3 class="font-semibold text-gray-800">Ringkasan</h3>
      <ul class="mt-2 text-sm text-gray-600 space-y-1">
        <li>Nama: <span class="font-medium">{{ $user->name }}</span></li>
        <li>Email: <span class="font-medium">{{ $user->email }}</span></li>
        <li>Role:  <span class="font-medium">{{ $isSeller ? 'Penjual' : 'Pembeli' }}</span></li>
      </ul>
    </aside>
  </div>

  @if($isSeller)
    <section class="mt-6 rounded-xl border bg-white">
      @include('profile.partials.seller')
    </section>
  @else
    <section class="mt-6 rounded-xl border bg-white">
      @include('profile.partials.buyer')
    </section>
  @endif
@endsection
