<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\PenjualController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PencarianController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderMasukController;
use App\Http\Controllers\PublicDashboardController;
use Barryvdh\DomPDF\Facade\Pdf;

/*
|--------------------------------------------------------------------------
| Landing
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('dashboard.public');
});

// Route::get('/', function () {
//     return redirect()->route('login');
// });



/*
|--------------------------------------------------------------------------
| Auth routes (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Redirect /dashboard sesuai role
|--------------------------------------------------------------------------
*/
// Dashboard publik
Route::get('/dashboard', [PublicDashboardController::class, 'index'])
    ->name('dashboard.public');

Route::middleware('auth')->get('/dashboard/auth', function () {
    return match (auth()->user()->role) {
        'admin'   => redirect()->route('admin.dashboard'),
        'penjual' => redirect()->route('penjual.dashboard'),
        'pembeli' => redirect()->route('pembeli.dashboard'),
        default   => abort(403),
    };
})->name('dashboard.auth');



/*
|--------------------------------------------------------------------------
| Profile umum (Breeze: update & delete akun)
|--------------------------------------------------------------------------
*/
// Route::middleware('auth')->group(function () {

//     // GET /profile -> redirect ke profile sesuai role
//     Route::get('/profile', function () {
//         $user = auth()->user();

//         return match ($user->role) {
//             'penjual' => redirect()->route('penjual.profile'),
//             'pembeli' => redirect()->route('pembeli.profile'),
//             default   => redirect()->route('dashboard'),
//         };
//     })->name('profile');

//     Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

/*
|--------------------------------------------------------------------------
| Area Pembeli
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:pembeli'])
    ->prefix('pembeli')
    ->name('pembeli.')
    ->group(function () {

        // Dashboard & detail produk
        Route::get('/dashboard', [PembeliController::class, 'index'])->name('dashboard');
        Route::get('/produk/{id}', [PembeliController::class, 'detailProduk'])->name('produk.detail');

        // Profil pembeli + preferensi lokasi
        Route::get('/profile', [PembeliController::class, 'profile'])->name('profile');
        Route::post('/profile/alamat', [PembeliController::class, 'simpanAlamat'])->name('alamat');
        Route::post('/profile/preferensi', [PembeliController::class, 'simpanPreferensi'])->name('preferensi');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

        // checkout page
        Route::get('/checkout', [OrderController::class, 'checkout']) ->name('checkout');

        // simpan order
        Route::post('/orders/simpan', [OrderController::class, 'simpan']) ->name('orders.simpan');

        // sukses
        Route::get('/orders/{orderId}/sukses', [OrderController::class, 'sukses']) ->name('orders.sukses');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        // Aksi terima barang (tanpa lihat detail)
        Route::patch('/orders/{order}/selesai',[OrderController::class, 'selesai'])->name('orders.selesai');

    // Detail order (HANYA setelah selesai)
        // Route::get('/orders/{order}',[OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/rating', [OrderController::class, 'storeRating'])->name('orders.rating.store');
        Route::put('/orders/{order}/rating',[OrderController::class, 'updateRating'])->name('orders.rating.update');
        // Hasil pencarian
        // Route::get('/hasilpencarian', [PencarianController::class, 'produk'])->name('hasilpencarian');
        Route::get('/search', [PencarianController::class, 'searchNearby'])->name('search');

        // Static pages (kalau nanti perlu controller, tinggal ganti)
        Route::post('/keranjang/tambah/{produk}', [KeranjangController::class, 'tambah'])->name('keranjang.tambah');

        Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang');

        Route::patch('/keranjang/{produk}', [KeranjangController::class, 'ubah'])->name('keranjang.ubah');

        Route::delete('/keranjang/{produk}', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');
        Route::patch('/keranjang/{id}/ajax', [KeranjangController::class, 'ubahKeranjangAjax'])->name('keranjang.ubah.ajax');

    });

/*
|--------------------------------------------------------------------------
| Area Penjual
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('penjual')
    ->name('penjual.')
    ->group(function () {

        // Daftar penjual (untuk user yang belum role penjual juga boleh)
        Route::get('/daftar',  [PenjualController::class, 'showDaftar'])->name('daftar');
        Route::post('/daftar', [PenjualController::class, 'submitDaftar'])->name('daftar.submit');

        // (kalau ini memang halaman pengajuan, biasanya beda method/view)
        Route::get('/pengajuan-saya', [PenjualController::class, 'showDaftar'])->name('pengajuan-saya');

        // Khusus yang sudah role:penjual
        Route::middleware('role:penjual')->group(function () {
            Route::get('/dashboard', [PenjualController::class, 'dashboard'])->name('dashboard');
            Route::get('/download/laporan', [PenjualController::class, 'downloadLaporan'])->name('download.laporan');
            Route::get('/laporan', [PenjualController::class, 'laporan'])->name('laporan');

            Route::get('/profile', [PenjualController::class, 'profile'])->name('profile');
            Route::patch('/profile', [PenjualController::class, 'updateProfile'])->name('profile.update');
            Route::get('/pesanan-masuk', [OrderMasukController::class, 'index'])->name('orders.masuk');

            Route::get('/pesanan-masuk/{order}', [OrderMasukController::class, 'show'])->name('orders.masuk.show');

            // Opsional: ubah status (accept/proses/kirim/selesai)
            Route::patch('/pesanan-masuk/{order}/status',[OrderMasukController::class, 'updateStatus'])->name('orders.masuk.status');});
    });

/*
|--------------------------------------------------------------------------
| Area Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/toko', [AdminController::class, 'show'])->name('toko.show');

    // halaman kelola user (file view: resources/views/admin/user.blade.php)
    Route::get('/user', [AdminController::class, 'users'])->name('user');

    Route::get('/user/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::patch('/user/{user}', [AdminController::class, 'updateUser'])->name('users.update');

    // tombol hapus
    Route::delete('/user/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');

    // yang sudah kamu punya
    Route::get('/penjual', [AdminController::class, 'penjuals'])->name('penjual');
    Route::post('/penjual/{id}/verify', [AdminController::class, 'verifyPenjual'])->name('penjual.verify');

    Route::get('/toko/{user}/barang', [AdminController::class, 'barangIndex'])->name('toko.barang');
    Route::get('/barang/{produk}/edit', [AdminController::class, 'barangEdit'])->name('barang.edit');
    Route::patch('/barang/{produk}', [AdminController::class, 'barangUpdate'])->name('barang.update');
    Route::delete('/produk/{id}/hapus', [AdminController::class, 'hapusBarang'])->name('produk.hapus');
});

/*
|--------------------------------------------------------------------------
| Produk (umum, butuh login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{produk}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    Route::post('/produk/{produk}/tambah-stok', [ProdukController::class, 'tambahStok'])->name('produk.tambahStok');

});
