<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index(Request $request)
   {
    $produks = Produk::with(['user.penjual']) // eager load user & penjual
        ->where('user_id', auth()->id())
        ->latest()
        ->paginate(10);

    return view('penjual.produk', compact('produks'));
    }

    /**
     * Form tambah produk.
     */
    public function create()
    {
        return view('produk.create');
    }

    /**
     * Simpan produk baru ke tabel `produk`.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:1',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        // set pemilik produk = user yang login
        $data['user_id'] = $user->id;

        // kalau checkbox/tombol status tidak diisi, default aktif
        $data['is_active'] = $request->has('is_active')
            ? (bool) $request->is_active
            : true;

        // upload gambar kalau ada
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        Produk::create($data);

        return redirect()
            ->route('produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Form edit produk (nanti).
     */
    public function edit(Produk $produk)
    {
        $this->authorizeOwner($produk);

        return view('produk.edit', compact('produk'));
    }

    /**
     * Update produk (nanti diselesaikan).
     */
    public function update(Request $request, Produk $produk)
    {
        $this->authorizeOwner($produk);

        $data = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        // status aktif kalau tidak dicentang pakai nilai lama
        $data['is_active'] = $request->has('is_active')
            ? (bool) $request->is_active
            : $produk->is_active;

        // kalau upload gambar baru
        if ($request->hasFile('gambar')) {
            // hapus gambar lama
            if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                Storage::disk('public')->delete($produk->gambar);
            }

            // simpan gambar baru
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $produk->update($data);

        return redirect()
            ->route('produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Hapus produk (nanti diselesaikan).
     */
    public function destroy(Produk $produk)
    {
        // pastikan produk milik user yg login
        if ($produk->user_id !== auth()->id()) {
            abort(403, 'Kamu tidak berhak menghapus produk ini.');
        }

        // hapus gambar dari storage
        if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
            Storage::disk('public')->delete($produk->gambar);
        }

        // hapus produk dari database
        $produk->delete();

        return redirect()
            ->route('produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Cek apakah produk milik user yang login.
     */
    protected function authorizeOwner(Produk $produk)
    {
        if ($produk->user_id !== auth()->id()) {
            abort(403);
        }
    }

    
    // public function searchNearby(Request $request)
    // {
    //     $keyword = $request->input('keyword');
    //     $latUser = $request->input('latitude');
    //     $lonUser = $request->input('longitude');

    //     if (!$latUser || !$lonUser) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Lokasi pengguna tidak ditemukan'
    //         ], 400);
    //     }

    //     $produk = DB::table('produks')
    //         ->join('penjuals', 'penjuals.user_id', '=', 'produks.user_id')
    //         ->select(
    //             'produks.id',
    //             'produks.nama',
    //             'produks.harga',
    //             'produks.foto',
    //             'penjuals.nama_toko',
    //             'penjuals.latitude',
    //             'penjuals.longitude',
    //             DB::raw("
    //                 (
    //                     6371 * acos(
    //                         cos(radians(?)) *
    //                         cos(radians(penjuals.latitude)) *
    //                         cos(radians(penjuals.longitude) - radians(?)) +
    //                         sin(radians(?)) *
    //                         sin(radians(penjuals.latitude))
    //                     )
    //                 ) AS distance
    //             ")
    //         )
    //         ->setBindings([$latUser, $lonUser, $latUser]) // 🔥 BIND PARAMETER
    //         ->when($keyword, function ($q) use ($keyword) {
    //             $q->where('produks.nama', 'LIKE', "%$keyword%");
    //         })
    //         ->whereNotNull('penjuals.latitude')
    //         ->whereNotNull('penjuals.longitude')
    //         ->havingRaw("distance IS NOT NULL")
    //         ->orderBy('distance', 'ASC')
    //         ->get();

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $produk
    //     ]);
    // }


}
