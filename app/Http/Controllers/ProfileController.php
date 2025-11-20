<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;            // ✅ tambah/benarkan ini
use Illuminate\Support\Facades\Hash;
use App\Models\Pembeli;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user     = $request->user();
        $isSeller = ($user->role === 'penjual');
        $layout   = $isSeller ? 'layouts.penjual' : 'layouts.pembeli';

        return view('profile.index', compact('user','layout','isSeller'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        // validasi dasar
        $data = $request->validate([
            'name'  => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            // password opsional: hanya update jika diisi
            'password' => ['nullable','confirmed','min:8'],
        ]);

        // update field dasar
        $user->fill([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        // update password jika diisi
        if (!empty($data['password'])) {
            $user->password = \Hash::make($data['password']);
        }

        $user->save();

        return back()->with('status','Profil diperbarui.');
    }

}
