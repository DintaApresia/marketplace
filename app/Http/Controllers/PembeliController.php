<?php

namespace App\Http\Controllers;

use App\Models\Pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembeliController extends Controller
{
    // TAMPIL HALAMAN PROFIL
    public function profile()
    {
        $user = Auth::user();

        // Ambil data pembeli user ini (bisa null kalau belum ada)
        $pembeli = Pembeli::where('idUser', $user->id)->first();

        return view('profile', [
            'user'    => $user,
            'pembeli' => $pembeli,
        ]);
    }

    // SIMPAN PREFERENSI PEMBELI
    public function simpanPreferensi(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'receiver_name' => 'required|string|max:100',
            'phone'         => 'required|string|max:25',
            'address_line'  => 'nullable|string',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
        ]);

        // Cari pembeli berdasarkan idUser. Kalau belum ada, buat baru.
        $pembeli = Pembeli::where('idUser', $user->id)->first();

        if (!$pembeli) {
            $pembeli = new Pembeli();
            $pembeli->idUser = $user->id;
        }

        $pembeli->nama_pembeli = $validated['receiver_name'];
        $pembeli->no_telp      = $validated['phone'];
        $pembeli->alamat       = $validated['address_line'] ?? '';
        $pembeli->latitude     = $validated['latitude'] ?? null;
        $pembeli->longitude    = $validated['longitude'] ?? null;

        $pembeli->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Preferensi pembeli berhasil disimpan.');
    }
}
