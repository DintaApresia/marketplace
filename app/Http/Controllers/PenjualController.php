<?php

namespace App\Http\Controllers;
use App\Models\Produk;
use App\Models\Pembeli;
use App\Models\Penjual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenjualController extends Controller
{
    /**
     * Tampilkan form daftar penjual
     * - kalau sudah penjual & approved -> lempar ke dashboard penjual
     * - kalau pending/rejected/belum daftar -> tampilkan view penjual.daftar
     */

    public function profile(Request $request)
    {
        $user = $request->user();

        // Pastikan user memang penjual
        if ($user->seller_status !== 'verified') {
            abort(403, 'Anda bukan penjual.');
        }

        $penjual = Penjual::where('user_id', $user->id)->first();

        return view('penjual.profile', compact('user', 'penjual'));
    }

    public function showDaftar(Request $request)
    {
        $user = $request->user();

        // Kalau sudah penjual dan status sudah approved/verified, langsung ke dashboard penjual
        if (
            $user->role === 'penjual' &&
            in_array($user->seller_status, ['approved', 'verified'])
        ) {
            return redirect()->route('penjual.dashboard')
                ->with('status', 'Kamu sudah terdaftar sebagai penjual.');
        }

        // Kalau sudah pernah isi data penjual, gunakan untuk prefill / ringkasan
        $penjual = $user->penjual; // relasi hasOne dari User

        return view('penjual.daftar', compact('user', 'penjual'));
    }

    /**
     * Simpan pengajuan penjual + ngopi data dari pembeli
     */
    public function submitDaftar(Request $request)
    {
        $user = $request->user();

        // Kalau sudah penjual & approved, tidak perlu daftar ulang
        if (
            $user->role === 'penjual' &&
            in_array($user->seller_status, ['approved', 'verified'])
        ) {
            return redirect()->route('penjual.dashboard')
                ->with('status', 'Kamu sudah terdaftar sebagai penjual.');
        }

        // Validasi input form pendaftaran penjual
        $data = $request->validate([
            'nama_toko'       => 'required|string|max:100',
            'alamat_toko'     => 'required|string',
            'rekening'        => 'required|string|max:100',
            'nama_rekening'   => 'required|string|max:100',
            'kartu_identitas' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Simpan file kartu identitas ke storage/app/public/kartu_identitas
        $kartuIdentitasPath = null;
        if ($request->hasFile('kartu_identitas')) {
            $kartuIdentitasPath = $request->file('kartu_identitas')
                ->store('kartu_identitas', 'public');
        }

        // Ambil data pembeli berdasarkan user login (boleh null)
        $pembeli = Pembeli::where('idUser', $user->id)->first();

        // Buat / update data penjual
        Penjual::updateOrCreate(
            ['user_id' => $user->id], // kunci
            [
                // Salinan dari pembeli
                'nama_penjual' => $pembeli->nama_pembeli ?? $user->name,
                'no_telp'      => $pembeli->no_telp       ?? null,
                'alamat'       => $pembeli->alamat        ?? null,
                'latitude'     => $pembeli->latitude      ?? null,
                'longitude'    => $pembeli->longitude     ?? null,

                // Data khusus penjual dari form
                'nama_toko'       => $data['nama_toko'],
                'alamat_toko'     => $data['alamat_toko'],
                'rekening'        => $data['rekening'],
                'nama_rekening'   => $data['nama_rekening'],
                'kartu_identitas' => $kartuIdentitasPath,
            ]
        );

        // Tandai user sedang diajukan jadi penjual
        $user->seller_status = 'pending';
        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('status', 'Permintaan menjadi penjual berhasil dikirim. Menunggu verifikasi admin.');
    }

    /**
     * Route "pengajuan-saya" (lihat kembali data pengajuan)
     * -> pakai view yang sama dengan showDaftar
     */
    public function showMyApplication()
    {
        $user = Auth::user();
        $penjual = Penjual::where('user_id', $user->id)->first();

        if (!$penjual) {
            return redirect()->route('profile.edit')
                ->with('error', 'Data pengajuan penjual belum ditemukan.');
        }

        return view('penjual.daftar', compact('user', 'penjual'));
    }

    /**
     * Update pengaturan toko dari halaman /profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // hanya boleh kalau sudah seller verified
        if ($user->seller_status !== 'verified') {
            abort(403);
        }

        $data = $request->validate([
            'nama_toko'    => 'nullable|string|max:100',
            'no_telp'      => 'nullable|string|max:30',
            'nama_rekening'=> 'nullable|string|max:100',
            'rekening'     => 'nullable|string|max:100',
            'alamat_toko'    => 'nullable|string',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
            'kartu_identitas'=> 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $penjual = Penjual::firstOrNew(['user_id' => $user->id]);
        $penjual->nama_toko       = $data['nama_toko']       ?? $penjual->nama_toko;
        $penjual->no_telp         = $data['no_telp']         ?? $penjual->no_telp;
        $penjual->nama_rekening   = $data['nama_rekening']   ?? $penjual->nama_rekening;
        $penjual->rekening        = $data['rekening']        ?? $penjual->rekening;
        $penjual->alamat_toko    = $data['alamat_toko']    ?? $penjual->alamat_toko;
        $penjual->latitude       = $data['latitude']       ?? $penjual->latitude;
        $penjual->longitude      = $data['longitude']      ?? $penjual->longitude;
        
        // kalau ganti kartu identitas
        if ($request->hasFile('kartu_identitas')) {
            $path = $request->file('kartu_identitas')->store('kartu_identitas', 'public');
            $penjual->kartu_identitas = $path;
        }
        $penjual->save();

        return redirect()->route('penjual.profile')->with('success', 'Pengaturan toko diperbarui.');
    }

    
    public function dashboard()
    {
        $totalProduk = Produk::where('user_id', Auth::id())->count();

        return view('penjual.dashboard', compact('totalProduk'));
    }
}
