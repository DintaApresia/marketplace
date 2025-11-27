<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\PenjualController;
use App\Http\Controllers\AdminController;

// Landing -> arahkan ke halaman login (Breeze)
Route::get('/', fn () => redirect()->route('login'));

// Route auth dari Breeze (login, register, password reset, logout-POST, email verify, dll.)
require __DIR__.'/auth.php';

// PROFIL — dipakai semua role, pakai ProfileController (punya Breeze)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/preferensi', [PembeliController::class, 'simpanPreferensi'])->name('pembeli.preferensi');
});

// AREA PEMBELI
Route::middleware(['auth' /*,'verified'*/])
    ->prefix('pembeli')->name('pembeli.')
    ->group(function () {
        Route::view('/dashboard', 'pembeli.dashboard')->name('dashboard');
        Route::view('/keranjang', 'pembeli.keranjang')->name('keranjang');
        Route::view('/orders',    'pembeli.orders')->name('orders');
    });

// AREA PENJUAL
Route::middleware(['auth'])
    ->prefix('penjual')->name('penjual.')
    ->group(function () {
        // 🔹 FORM DAFTAR PENJUAL (cukup auth, TIDAK pakai role:penjual)
        Route::get('/daftar',  [PenjualController::class, 'showDaftar'])->name('daftar');
        Route::post('/daftar', [PenjualController::class, 'submitDaftar'])->name('daftar.submit');
       Route::get('/pengajuan-saya', [PenjualController::class, 'showDaftar'])
            ->name('pengajuan-saya');


        // 🔹 ROUTE KHUSUS PENJUAL YANG SUDAH DIVERIFIKASI
        Route::middleware('role:penjual')->group(function () {
            Route::view('/dashboard', 'penjual.dashboard')->name('dashboard');
            // route penjual lain taruh di sini
        });
    });

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/penjual', [AdminController::class, 'penjuals'])->name('penjual.index');
        Route::patch('/penjual/{id}/verify', [AdminController::class, 'verifyPenjual'])->name('penjual.verify');
    });
