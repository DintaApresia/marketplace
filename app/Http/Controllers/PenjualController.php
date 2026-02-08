<?php

namespace App\Http\Controllers;
use App\Models\Produk;
use App\Models\Pembeli;
use App\Models\Penjual;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    //pernah submit sebagai penjual
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
     * Simpan pengajuan penjual + ngopi data dari pembeli (daftar)
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
            ->route('pembeli.profile')
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
            'nama_penjual' => 'nullable|string|max:100',
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
        $penjual->nama_penjual       = $data['nama_penjual']       ?? $penjual->nama_penjual;
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
        $penjualId = Auth::user()->penjual->id;

        $totalProduk = Produk::where('penjual_id', $penjualId)->count();

        $produkAktif = Produk::where('penjual_id', $penjualId)
            ->where('stok', '>', 0)->count();

        $produkNonaktif = Produk::where('penjual_id', $penjualId)
            ->where('stok', '=', 0)->count();

        $pesananDikemas = Order::where('status_pesanan', 'dikemas')
            ->whereHas('items.produk', fn ($q) =>
                $q->where('penjual_id', $penjualId)
            )->count();

        $pesananDikirim = Order::where('status_pesanan', 'dikirim')
            ->whereHas('items.produk', fn ($q) =>
                $q->where('penjual_id', $penjualId)
            )->count();

        $pesananSelesai = Order::where('status_pesanan', 'selesai')
            ->whereHas('items.produk', fn ($q) =>
                $q->where('penjual_id', $penjualId)
            )->count();

        $pesananDitolak = Order::where('status_pesanan', 'ditolak')
            ->whereHas('items.produk', fn ($q) =>
                $q->where('penjual_id', $penjualId)
            )->count();

        $pesananMasuk = $pesananDikemas + $pesananDikirim + $pesananSelesai + $pesananDitolak;

        return view('penjual.dashboard', compact(
            'totalProduk',
            'produkAktif',
            'produkNonaktif',
            'pesananMasuk',
            'pesananDikemas',
            'pesananDikirim',
            'pesananSelesai',
            'pesananDitolak'
        ));
    }

    // Laporan
    private function getLaporanData(Request $request)
    {
        $penjualId = Auth::user()->penjual->id;

        $startDate = $request->tanggal_mulai
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : now()->startOfMonth();

        $endDate = $request->tanggal_selesai
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : now()->endOfMonth();

        $totalProduk = Produk::where('penjual_id', $penjualId)->count();
        $produkAktif = Produk::where('penjual_id', $penjualId)->where('is_active', 1)->count();
        $produkNonaktif = Produk::where('penjual_id', $penjualId)->where('is_active', 0)->count();

        $baseOrderQuery = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('items.produk', fn ($q) =>
                $q->where('penjual_id', $penjualId)
            );

        $pesananDikemas = (clone $baseOrderQuery)->where('status_pesanan', 'dikemas')->count();
        $pesananDikirim = (clone $baseOrderQuery)->where('status_pesanan', 'dikirim')->count();
        $pesananSelesai = (clone $baseOrderQuery)->where('status_pesanan', 'selesai')->count();
        $pesananDitolak = (clone $baseOrderQuery)->where('status_pesanan', 'ditolak')->count();

        $pesananMasuk = $pesananDikemas + $pesananDikirim + $pesananSelesai + $pesananDitolak;

        $totalTerjual = (clone $baseOrderQuery)
            ->where('status_pesanan', 'selesai')
            ->sum('total_bayar');

        $produkTerjual = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('produk', 'order_items.produk_id', '=', 'produk.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('produk.penjual_id', $penjualId)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'produk.nama_barang',
                'order_items.jumlah',
                'order_items.subtotal_item',
                'users.name as nama_pembeli',
                'orders.status_pesanan',
                'orders.created_at as tanggal_pembelian'
            )
            ->orderBy('orders.created_at', 'desc')
            ->get();

        $totalSubtotal = $produkTerjual->sum('subtotal_item');

        return compact(
            'totalProduk',
            'produkAktif',
            'produkNonaktif',
            'pesananMasuk',
            'pesananDikemas',
            'pesananDikirim',
            'pesananSelesai',
            'pesananDitolak',
            'totalTerjual',
            'produkTerjual',
            'totalSubtotal',
            'startDate',
            'endDate'
        );
    }

    public function laporan(Request $request)
    {
        return view('penjual.laporan_dashboard', $this->getLaporanData($request));
    }
    public function downloadLaporan(Request $request)
    {
        $data = $this->getLaporanData($request);

        return Pdf::loadView('penjual.laporan_pdf', $data)
            ->setPaper('a4', 'portrait')
            ->download(
                'laporan-penjual-' .
                $data['startDate']->format('Ymd') . '-' .
                $data['endDate']->format('Ymd') . '.pdf'
            );
    }

}
