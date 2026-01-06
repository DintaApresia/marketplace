<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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

            // email opsional, tapi kalau diisi harus valid & unik (kecuali email user sendiri)
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            // password opsional: kalau diisi wajib konfirmasi & minimal 8
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // ✅ Update USER (email & password) kalau diisi
        if (!empty($validated['email'])) {
            $user->email = $validated['email'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // simpan perubahan user hanya jika ada yang berubah
        if ($user->isDirty(['email', 'password'])) {
            $user->save();
        }

        // ✅ Update/insert PEMBELI
        $pembeli = Pembeli::firstOrNew(['idUser' => $user->id]);
        $pembeli->nama_pembeli = $validated['receiver_name'];
        $pembeli->no_telp      = $validated['phone'];
        $pembeli->save();

        return redirect()
            ->route('pembeli.profile')
            ->with('success', 'Akun pembeli berhasil disimpan.');
    }

    public function simpanAlamat(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'alamat'    => 'nullable|string',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $pembeli = Pembeli::firstOrNew(['idUser' => $user->id]);

        // jangan ganggu nama/no_telp di sini
        $pembeli->alamat    = $validated['alamat'] ?? '';
        $pembeli->latitude  = $validated['latitude'] ?? null;
        $pembeli->longitude = $validated['longitude'] ?? null;

        $pembeli->save();

        return redirect()->route('pembeli.profile')->with('success', 'Alamat berhasil disimpan.');
    }


    public function index()
    {
            $produk = Produk::with('penjual')
            ->where('stok', '>', 0)
            ->latest()
            ->get(); // atau paginate

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
