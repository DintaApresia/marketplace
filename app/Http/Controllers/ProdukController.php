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
            'deskripsi'   => 'required|string',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0', // ✅ boleh 0
            'gambar'      => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // set pemilik produk
        $data['user_id'] = $user->id;

        // ✅ full otomatis: stok 0 => nonaktif, stok > 0 => aktif
        $data['is_active'] = ((int) $data['stok'] > 0);

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
            'deskripsi'   => 'required|string',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ✅ full otomatis: stok 0 => nonaktif, stok > 0 => aktif
        $data['is_active'] = ((int) $data['stok'] > 0);

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

    public function tambahStok(Request $request, Produk $produk)
    {
        // pastikan produk milik user login
        $this->authorizeOwner($produk);

        // default tambah 1, bisa dikirim dari button
        $jumlah = (int) ($request->input('jumlah', 1));

        if ($jumlah < 1) {
            return back()->with('error', 'Jumlah stok tidak valid.');
        }

        $produk->stok += $jumlah;

        // stok-based active
        $produk->is_active = $produk->stok > 0;

        $produk->save();

        return back()->with('success', 'Stok berhasil ditambahkan.');
    }



    /**
     * Hapus produk (nanti diselesaikan).
     */
    public function destroy(Produk $produk)
    {
        if ($produk->user_id !== auth()->id()) {
            abort(403);
        }

        if ($produk->orderItems()->exists()) {
            return back()->with('error', 'Produk tidak bisa dihapus karena sudah pernah dibeli.');
        }

        if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
            Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
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
}
