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

            // Jika user pembeli tapi belum diverifikasi sebagai penjual
            if ($user->role === 'pembeli' && !$user->is_verified_seller) {
                return redirect('/dashboard/pembeli');
            }

            // Jika user penjual
            if ($user->role === 'penjual' && $user->is_verified_seller) {
                return redirect('/dashboard/penjual');
            }

            // default fallback
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
        $user->update(['is_verified_seller' => true, 'role' => 'penjual']);
        return back()->with('success', 'Penjual berhasil diverifikasi.');
    }
}
