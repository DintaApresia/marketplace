<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Penjual;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        
        $user->seller_status = $status; // verified / rejected
        if ($status === 'verified') {
            $user->role = 'penjual';
        }
        $user->save();

        return back()->with('success', "Status seller diubah menjadi {$status}.");
    }

    public function users(Request $request)
    {
        $q = $request->query('q');
        $role = $request->query('role');
        $seller_status = $request->query('seller_status');

        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($role, fn ($query) => $query->where('role', $role))
            ->when($seller_status, fn ($query) => $query->where('seller_status', $seller_status))
            ->latest()
            ->paginate(10)
            ->appends($request->query());


        return view('admin.user', compact('users'));
    }

    public function editUser(User $user)
    {
        return view('admin.user_edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => [
                'required','email','max:255',
                Rule::unique('users','email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['admin','penjual','pembeli'])],
            // sesuai enum di DB kamu:
            'seller_status' => ['required', Rule::in(['none','pending','verified','rejected'])],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.edit',$user->id)->with('success', 'User berhasil diperbarui.');
    }

    public function deleteUser(User $user)
    {
        // proteksi: admin tidak bisa hapus dirinya sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    
    public function barangIndex(Request $request)
    {
        $q = $request->get('q');

        $barangs = Produk::with('user')
            ->when($q, function ($query) use ($q) {
                $query->where('nama_barang', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(10);

        return view('admin.barang', compact('barangs', 'q'));
    }

    
    public function barangEdit(Produk $produk)
    {
        return view('admin.barang_edit', compact('produk'));
    }

    public function barangUpdate(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'is_active'   => 'required|boolean',
            // gambar opsional:
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // kalau upload gambar baru
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('produk', 'public');
            $validated['gambar'] = $path;
        }

        $produk->update($validated);

        return redirect()
            ->route('admin.barang.edit', $produk->id)
            ->with('success', 'Barang berhasil diperbarui.');
    }
}