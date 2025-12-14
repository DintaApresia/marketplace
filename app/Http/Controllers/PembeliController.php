<?php

namespace App\Http\Controllers;

use App\Models\Pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Produk;

class PembeliController extends Controller
{
    // TAMPIL HALAMAN PROFIL
    public function profile(Request $request)
    {
        $user    = $request->user();
        $pembeli = Pembeli::where('idUser', $user->id)->first();

        return view('pembeli.profile', compact('user', 'pembeli'));
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
            ->route('pembeli.profile')
            ->with('success', 'Preferensi pembeli berhasil disimpan.');
    }

    public function index()
    {
        $produk = Produk::with('penjual')   // <<-- WAJIB supaya alamat toko muncul
                        ->where('is_active', 1)
                        ->latest()
                        ->get();

        return view('pembeli.dashboard', compact('produk'));
    }

    public function detailProduk($id)
    {
         $produk = Produk::with(['user.penjual'])->findOrFail($id);

        // Ambil data penjual dari relasi user → penjual
        $penjual = $produk->user->penjual;

        return view('pembeli.detailproduk', compact('produk', 'penjual'));
    }

    public function hasilPencarian(Request $request)
    {
        $data = json_decode($request->data, true);

        return view('pembeli.hasilpencarian', [
            'produk' => $data
        ]);
    }

}
