<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
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
        $totalAdmin = User::where('role', 'admin')->count();
        $totalPenjual = User::where('role', 'penjual')->count();
        $totalPembeli = User::where('role', 'pembeli')->count();

        return view('admin.dashboard', [
            'totalUsers'   => $totalUsers,
            'totalAdmin' => $totalAdmin,
            'totalPenjual' => $totalPenjual,
            'totalPembeli' => $totalPembeli,
            // 'totalBarang'  => Barang::count(), // kalau sudah ada tabel barang
        ]);
    }

    public function show()
    {
        $penjuals = User::with('penjual')
            ->where('role', 'penjual')
            ->latest()
            ->get();

        return view('admin.toko', compact('penjuals'));
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
            'role' => ['required', Rule::in(['admin','penjual','pembeli'])],
            'seller_status' => ['required', Rule::in(['none','pending','verified','rejected'])],
            'password' => ['nullable','string','min:8','confirmed'],
        ]);

        // update field WAJIB
        $user->name = $validated['name'];
        $user->role = $validated['role'];
        $user->seller_status = $validated['seller_status'];

        // 🔐 password hanya diupdate kalau DIISI
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.user', $user->id)
            ->with('success', 'User berhasil diperbarui.');
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

    public function barangIndex(Request $request, $user)
    {
        $q = $request->get('q');

        $barangs = Produk::with('user')
            ->where('user_id', $user) // ✅ INI KUNCINYA
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama_barang', 'like', "%{$q}%")
                        ->orWhere('deskripsi', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.barang', compact('barangs', 'q', 'user'));
    }

   
    public function barangEdit(Produk $produk)
    {
        $user = $produk->user_id;
        return view('admin.barang_edit', compact('produk', 'user'));
    }


    public function barangUpdate(Request $request, Produk $produk)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi'   => 'required|string',
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
            ->route('admin.toko.barang', $produk->user_id)
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function hapusBarang($id)
    {
        $produk = Produk::findOrFail($id);

        $pernahDipesan = \DB::table('order_items')
            ->where('produk_id', $produk->id)
            ->exists();

        if ($pernahDipesan) {
            $produk->update([
                'is_active' => 0,
                'stok' => 0,
            ]);

            return back()->with('success', 'Produk sudah pernah dipesan, jadi tidak bisa dihapus. Produk dinonaktifkan.');
        }

        if ($produk->gambar && \Storage::disk('public')->exists($produk->gambar)) {
            \Storage::disk('public')->delete($produk->gambar);
        }

        $userId = $produk->user_id;
        $produk->delete();

        return redirect()
            ->route('admin.toko.barang', $userId)
            ->with('success', 'Produk berhasil dihapus.');
    }

}