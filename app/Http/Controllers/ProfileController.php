<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\Pembeli;

class ProfileController extends Controller
{
    // HALAMAN PROFILE (AKUN + PREFERENSI PEMBELI DI SATU HALAMAN)
    public function edit(Request $request)
    {
        $user     = $request->user();
        $isSeller = ($user->role === 'penjual');
        $layout   = $isSeller ? 'layouts.penjual' : 'layouts.pembeli';

        // 🔹 Ambil data pembeli hanya kalau user ini pembeli
        $pembeli = null;
        if (! $isSeller) {
            $pembeli = Pembeli::where('idUser', $user->id)->first();
        }

        // view utama profile kamu: resources/views/profile/index.blade.php
        return view('profile.index', [
            'user'    => $user,
            'layout'  => $layout,
            'isSeller'=> $isSeller,
            'pembeli' => $pembeli, // ⬅️ dipakai untuk form preferensi
        ]);
    }

    // UPDATE DATA AKUN (NAMA, EMAIL, PASSWORD)
    public function update(Request $request)
    {
        $user = $request->user();

        // validasi dasar
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            // password opsional: hanya update jika diisi
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        // update field dasar
        $user->name  = $data['name'];
        $user->email = $data['email'];

        // update password jika diisi
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('status', 'Profil diperbarui.');
    }
}
