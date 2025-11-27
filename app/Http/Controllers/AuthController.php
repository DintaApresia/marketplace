<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Tampilkan form register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return redirect('/login')->with('success', 'Akun berhasil dibuat, silakan login.');
    }

    // Tampilkan form login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Kalau admin (kalau kamu pakai role admin)
            if ($user->role === 'admin') {
                return redirect('/dashboard/admin');
            }

            // Kalau pembeli biasa
            if ($user->role === 'pembeli') {
                return redirect('/dashboard/pembeli');
            }

            // Kalau penjual & SUDAH disetujui admin
            if ($user->role === 'penjual' && $user->seller_status === 'approved') {
                return redirect('/dashboard/penjual');
            }

            // Kalau penjual tapi statusnya belum approved (pending / rejected)
            if ($user->role === 'penjual' && $user->seller_status !== 'approved') {
                return redirect('/profile')
                    ->with('warning', 'Akun penjualmu belum disetujui admin.');
            }

            // fallback
            return redirect('/dashboard/pembeli');
        }

        return back()->with('error', 'Email atau password salah.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    // Hanya admin (tanpa login)
    public function adminPanel()
    {
        $users = User::all();
        return view('admin.dashboard', compact('users'));
    }

    // Verifikasi penjual oleh admin
    public function verifySeller($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'role'          => 'penjual',
            'seller_status' => 'approved',
        ]);

        return back()->with('success', 'Penjual berhasil diverifikasi.');
    }

    public function rejectSeller($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'seller_status' => 'rejected',
        ]);

        return back()->with('success', 'Pengajuan penjual ditolak.');
    }
}
