<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController; // dipakai utk adminPanel & verifySeller saja
use App\Http\Controllers\PembeliController;

// Landing -> arahkan ke halaman login (Breeze)
Route::get('/', fn () => redirect()->route('login'));

// Route auth dari Breeze (login, register, password reset, logout-POST, email verify, dll.)
require __DIR__.'/auth.php';

// PROFIL — satu route untuk semua role (ikut layout berdasar role di controller)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// AREA PEMBELI
Route::middleware(['auth' /*,'verified'*/])
    ->prefix('pembeli')->name('pembeli.')
    ->group(function () {
        Route::view('/dashboard', 'pembeli.dashboard')->name('dashboard');
        Route::view('/keranjang', 'pembeli.keranjang')->name('keranjang');
        Route::view('/orders',    'pembeli.orders')->name('orders');
        // NOTE: Profile cukup pakai route('profile.edit'), tidak perlu `pembeli/profile`
    });

Route::get('/profile', [PembeliController::class, 'profile'])->name('profile');
Route::post('/profile/preferensi', [PembeliController::class, 'simpanPreferensi'])->name('pembeli.preferensi');

// AREA PENJUAL
Route::middleware(['auth', 'role:penjual'])
    ->prefix('penjual')->name('penjual.')
    ->group(function () {
        Route::view('/dashboard', 'penjual.dashboard')->name('dashboard');
        // Contoh (nanti kalau ada):
        // Route::resource('produk', ProdukController::class);
        // Route::resource('pesanan', PesananController::class)->only(['index','show']);
        // Profile tetap pakai route('profile.edit')
    });

// ADMIN (PERTIMBANGKAN pakai proteksi auth/admin gate)
Route::get('/admin', [AuthController::class, 'adminPanel'])->name('admin.panel');
Route::post('/admin/verify/{id}', [AuthController::class, 'verifySeller'])->name('admin.verify.seller');