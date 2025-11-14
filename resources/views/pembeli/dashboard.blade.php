@extends('layouts.pembeli')
@section('title','Dashboard — SecondLife')
@section('content')
  {{-- Hero khusus home (boleh beda di halaman lain) --}}
  <section class="bg-gradient-to-b from-green-700 to-green-600 text-white rounded-xl mt-6">
    <div class="px-6 py-10 text-center">
      <h1 class="text-3xl sm:text-4xl font-bold">Give Items a Second Life</h1>
      <p class="mt-2 text-white/90">Buy & sell pre-loved items sustainably.</p>
      <a href="#featured" class="mt-5 inline-block rounded-md bg-white text-green-800 px-4 py-2 font-medium hover:bg-gray-100">
        Start Shopping
      </a>
    </div>
  </section>

  <section id="featured" class="py-8">
    <h2 class="text-xl font-semibold">Featured Items</h2>
    <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
      @for($i=1;$i<=4;$i++)
        <article class="rounded-xl overflow-hidden border bg-white">
          <div class="h-44 sm:h-48 bg-gray-100"></div>
          <div class="p-4">
            <h3 class="font-medium">Item {{ $i }}</h3>
            <div class="mt-2 flex items-center justify-between">
              <span class="text-green-700 font-semibold">$99</span>
              <span class="text-sm text-gray-500">⭐ 4.8</span>
            </div>
          </div>
        </article>
      @endfor
    </div>
  </section>
@endsection
