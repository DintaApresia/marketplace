<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Penjual;
use App\Models\Barang;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // total semua user di tabel users
        $totalUsers = User::count();

        // contoh lain kalau mau:
        $totalPenjual = User::where('role', 'penjual')->count();
        $totalPembeli = User::where('role', 'pembeli')->count();

        return view('admin.dashboard', [
            'totalUsers'   => $totalUsers,
            'totalPenjual' => $totalPenjual,
            'totalPembeli' => $totalPembeli,
            // 'totalBarang'  => Barang::count(), // kalau sudah ada tabel barang
        ]);
    }

    public function penjuals()
    {
        // ambil semua penjual + data user-nya
        $penjuals = Penjual::with('user')->orderBy('id', 'desc')->paginate(10);

        return view('admin.penjual', compact('penjuals'));
    }

    public function verifyPenjual($id, Request $request)
    {
        $penjual = Penjual::with('user')->findOrFail($id);

        $user = $penjual->user; // sekarang pasti ada!

        $status = $request->input('status'); // verified / rejected

        if (!in_array($status, ['verified', 'rejected', 'pending'])) {
            return back()->with('error', 'Status tidak valid.');
        }

        $user->seller_status = $status;
        $user->save();

        return back()->with('success', "Status seller diubah menjadi {$status}.");
    }
}