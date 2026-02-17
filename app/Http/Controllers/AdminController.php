<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Penjual;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();

        $totalAdmin = User::where('role', 'admin')->count();
        $totalPenjual = User::where('role', 'penjual')->count();
        $totalPembeli = User::where('role', 'pembeli')->count();

        return view('admin.dashboard', [
            'totalUsers'   => $totalUsers,
            'totalAdmin' => $totalAdmin,
            'totalPenjual' => $totalPenjual,
            'totalPembeli' => $totalPembeli,
        ]);
    }

    public function show()
    {
        $penjuals = Penjual::with('user')
            ->whereHas('user', function ($q) {
                $q->whereIn('seller_status', ['verified']);
            })
            ->latest()
            ->get();

        return view('admin.toko', compact('penjuals'));
    }

    public function penjuals()
    {
        $penjuals = Penjual::with('user')->orderBy('id', 'desc')->paginate(10);
        return view('admin.penjual', compact('penjuals'));
    }

    public function verifyPenjual($id, Request $request)
    {
        $penjual = Penjual::with('user')->findOrFail($id);
        $user = $penjual->user;

        $status = $request->input('status'); // verified / rejected / pending

        if (!in_array($status, ['verified', 'rejected', 'pending'])) {
            return back()->with('error', 'Status tidak valid.');
        }

        $user->seller_status = $status;
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

    public function deleteUser(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    public function barangIndex(Request $request, $penjualId)
    {
        $q = $request->get('q');

        $barangs = Produk::with(['penjual.user'])
            ->where('penjual_id', $penjualId)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama_barang', 'like', "%{$q}%")
                        ->orWhere('deskripsi', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.barang', compact('barangs', 'q', 'penjualId'));
    }

    public function hapusBarang($id)
    {
        $produk = Produk::findOrFail($id);

        $pernahDipesan = DB::table('order_items')
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

    public function detailTransaksi(Request $request, $id)
    {
        // kolom status order (anti typo)
        if (Schema::hasColumn('orders', 'status_pesanan')) $orderStatusCol = 'status_pesanan';
        elseif (Schema::hasColumn('orders', 'status_pesanna')) $orderStatusCol = 'status_pesanna';
        else $orderStatusCol = 'status_pesanan';

        $orderKodeCol   = Schema::hasColumn('orders', 'kode_order') ? 'kode_order' : null;

        $orderTotalCol  = Schema::hasColumn('orders', 'total_bayar') ? 'total_bayar'
            : (Schema::hasColumn('orders', 'total') ? 'total'
            : (Schema::hasColumn('orders', 'total_harga') ? 'total_harga' : null));

        $orderMetodeCol = Schema::hasColumn('orders', 'metode_pembayaran') ? 'metode_pembayaran'
            : (Schema::hasColumn('orders', 'payment_method') ? 'payment_method' : null);

        $orderStatusBayarCol = Schema::hasColumn('orders', 'status_pembayaran') ? 'status_pembayaran'
            : (Schema::hasColumn('orders', 'payment_status') ? 'payment_status' : null);

        $orderBuktiCol = Schema::hasColumn('orders', 'bukti_pembayaran') ? 'bukti_pembayaran'
            : (Schema::hasColumn('orders', 'bukti') ? 'bukti' : null);

        // ==== ORDER (FIX DUPLIKASI: HILANGKAN OR JOIN) ====
        $orderQ = DB::table('orders as o')
            ->leftJoin('users as pembeli', 'pembeli.id', '=', 'o.user_id')

            // 2 join terpisah (aman, ga dobel)
            ->leftJoin('penjuals as pj_u', 'pj_u.user_id', '=', 'o.penjual_id')
            ->leftJoin('penjuals as pj_i', 'pj_i.id', '=', 'o.penjual_id')

            // ambil identitas penjual dari users berdasarkan penjual.user_id (coalesce)
            ->leftJoin('users as penjual', 'penjual.id', '=', DB::raw('COALESCE(pj_u.user_id, pj_i.user_id)'))

            ->where('o.id', $id)
            ->select([
                'o.id',
                'o.user_id',
                'o.penjual_id',
                'o.created_at',
                'o.updated_at',
                "o.$orderStatusCol as status_pesanan",

                'pembeli.name as pembeli_nama',
                'pembeli.email as pembeli_email',

                DB::raw('COALESCE(pj_u.nama_toko, pj_i.nama_toko) as nama_toko'),
                'penjual.name as penjual_nama',
                'penjual.email as penjual_email',

                DB::raw('COALESCE(pj_u.user_id, pj_i.user_id) as penjual_user_id'),
            ]);

        if ($orderKodeCol)        $orderQ->addSelect("o.$orderKodeCol as kode_order");
        if ($orderTotalCol)       $orderQ->addSelect("o.$orderTotalCol as total");
        if ($orderMetodeCol)      $orderQ->addSelect("o.$orderMetodeCol as metode_pembayaran");
        if ($orderStatusBayarCol) $orderQ->addSelect("o.$orderStatusBayarCol as status_pembayaran");
        if ($orderBuktiCol)       $orderQ->addSelect("o.$orderBuktiCol as bukti_pembayaran");

        $order = $orderQ->first();
        abort_if(!$order, 404);

        // ==== LOGS ====
        $logCols = Schema::getColumnListing('order_status_logs');
        $logsQuery = DB::table('order_status_logs')
            ->where('order_id', $id)
            ->orderBy('created_at');

        $logsQuery->addSelect(in_array('status', $logCols) ? 'status' : DB::raw("NULL as status"));

        if (in_array('catatan', $logCols)) {
            $logsQuery->addSelect('catatan');
        } elseif (in_array('keterangan', $logCols)) {
            $logsQuery->addSelect(DB::raw("keterangan as catatan"));
        } else {
            $logsQuery->addSelect(DB::raw("NULL as catatan"));
        }

        $logsQuery->addSelect('created_at');
        $logs = $logsQuery->get();

        $tglSelesai = $logs->count()
            ? optional($logs->last())->created_at
            : ($order->updated_at ?? null);

        // ==== ADUAN terkait ====
        $aduanCols = Schema::getColumnListing('aduans');

        $aduanSelect = ['id','judul','deskripsi','created_at','status_aduan'];
        if (in_array('catatan_penjual', $aduanCols)) $aduanSelect[] = 'catatan_penjual';
        if (in_array('tgl_catatan_penjual', $aduanCols)) $aduanSelect[] = 'tgl_catatan_penjual';
        if (in_array('catatan_admin', $aduanCols)) $aduanSelect[] = 'catatan_admin';
        if (in_array('tgl_catatan_admin', $aduanCols)) $aduanSelect[] = 'tgl_catatan_admin';

        $aduans = DB::table('aduans')
            ->where('order_id', $id)
            ->select($aduanSelect)
            ->orderByDesc('created_at')
            ->get();

        // ==== RATINGS (produk_rating) - FIX: JANGAN QUERY 2X ====
        $ratings = collect();

        if (Schema::hasTable('produk_rating')) {
            $ratingCols = Schema::getColumnListing('produk_rating');
            $produkTable = Schema::hasTable('produk') ? 'produk' : null;

            $ratingQuery = DB::table('produk_rating as pr')
                ->where('pr.order_id', $id)
                ->orderByDesc('pr.created_at');

            if ($produkTable) {
                $ratingQuery->leftJoin('produk as p', 'p.id', '=', 'pr.produk_id');
            }

            $selectRating = [
                'pr.id',
                'pr.user_id',
                'pr.order_id',
                'pr.produk_id',
                'pr.rating',
                'pr.review',
                'pr.created_at',
            ];

            if (in_array('review_images', $ratingCols)) {
                $selectRating[] = 'pr.review_images';
            } else {
                $selectRating[] = DB::raw("NULL as review_images");
            }

            if ($produkTable) {
                $selectRating[] = 'p.nama_barang as nama_barang';
            } else {
                $selectRating[] = DB::raw("NULL as nama_barang");
            }

            $ratings = $ratingQuery->select($selectRating)->get();
        }

        return view('admin.transaksi_show', [
            'order' => $order,
            'logs' => $logs,
            'aduans' => $aduans,
            'ratings' => $ratings,

            'from' => $request->get('from'),
            'fromTab' => $request->get('from', 'monitoring'),
            'aduanId' => $request->get('aduan_id'),

            'tglSelesai' => $tglSelesai,
        ]);
    }

    public function manajemenTransaksi(Request $request)
    {
        $tab = $request->get('tab', 'monitoring');
        if (!in_array($tab, ['monitoring', 'aduan', 'riwayat'])) $tab = 'monitoring';

        $q = trim((string) $request->get('q'));

        $penjualId = $request->get('penjual_id');
        $status    = $request->get('status');
        $start     = $request->get('start');
        $end       = $request->get('end');

        // status order (aman jika ada typo status_pesanna)
        if (\Schema::hasColumn('orders', 'status_pesanan')) {
            $orderStatusCol = 'status_pesanan';
        } elseif (\Schema::hasColumn('orders', 'status_pesanna')) {
            $orderStatusCol = 'status_pesanna';
        } else {
            $orderStatusCol = 'status_pesanan';
        }

        $orderKodeCol  = \Schema::hasColumn('orders', 'kode_order') ? 'kode_order' : null;

        $orderTotalCol = \Schema::hasColumn('orders', 'total_bayar') ? 'total_bayar'
            : (\Schema::hasColumn('orders', 'total') ? 'total'
            : (\Schema::hasColumn('orders', 'total_harga') ? 'total_harga' : null));

        $aduanStatusCol = \Schema::hasColumn('aduans', 'status_aduan') ? 'status_aduan'
            : (\Schema::hasColumn('aduans', 'status') ? 'status' : null);

        $aktifStatuses   = ['menunggu', 'diproses', 'dikemas', 'dikirim'];
        $riwayatStatuses = ['selesai', 'dibatalkan'];

        // ======= badge count (sesuai ketentuan kamu) =======
        // monitoring: yang masih proses (kecuali selesai)
        $monitoringCount = \DB::table('orders')
            ->when($penjualId, function ($qq) use ($penjualId) {
                // tetap selaras dengan filter penjual dropdown (user_id penjual)
                $qq->where('penjual_id', $penjualId);
            })
            ->whereIn($orderStatusCol, $aktifStatuses)
            ->count();

        // aduan: yang belum ditanggapi admin = status_aduan masih "menunggu" / NULL
        $aduanCount = 0;
        if (\Schema::hasTable('aduans')) {
            $aduanCount = \DB::table('aduans')
                ->when($penjualId, function ($qq) use ($penjualId) {
                    // ambil order dulu biar bisa filter penjual_id juga
                    $qq->whereIn('order_id', function ($sub) use ($penjualId) {
                        $sub->select('id')->from('orders')->where('penjual_id', $penjualId);
                    });
                })
                ->when($aduanStatusCol, function ($qq) use ($aduanStatusCol) {
                    $qq->where(function ($w) use ($aduanStatusCol) {
                        $w->whereNull($aduanStatusCol)
                        ->orWhere($aduanStatusCol, 'menunggu');
                    });
                }, function ($qq) {
                    // kalau kolom status_aduan tidak ada, fallback: tetap 0 agar aman
                    $qq->whereRaw('1=0');
                })
                ->count();
        }

        $lastLogSub = \DB::table('order_status_logs')
            ->select('order_id', \DB::raw('MAX(created_at) as last_status_at'))
            ->groupBy('order_id');

        // dropdown penjual (pakai penjuals biar tampil nama_toko)
        $penjualList = \DB::table('penjuals as pj')
            ->select('pj.user_id as id', 'pj.nama_toko')
            ->orderBy('pj.nama_toko')
            ->get();

        if ($penjualList->count() === 0) {
            $penjualList = \DB::table('users')
                ->select('id', \DB::raw("name as nama_toko"))
                ->orderBy('name')
                ->get();
        }

        $orders = null;
        $aduans = null;

        if ($tab === 'monitoring' || $tab === 'riwayat') {

            $ordersQuery = \DB::table('orders as o')
                ->leftJoin('users as ub', 'ub.id', '=', 'o.user_id') // pembeli

                // fallback penjual user
                ->leftJoin('users as up', 'up.id', '=', 'o.penjual_id')

                // ✅ FIX DOBEL: join penjuals dipisah (tanpa orOn)
                ->leftJoin('penjuals as pj_u', 'pj_u.user_id', '=', 'o.penjual_id')
                ->leftJoin('penjuals as pj_id', function ($join) {
                    $join->on('pj_id.id', '=', 'o.penjual_id')
                        ->whereNull('pj_u.id'); // kalau sudah ketemu via user_id, jangan match via id
                })

                ->leftJoinSub($lastLogSub, 'osl_last', function ($join) {
                    $join->on('osl_last.order_id', '=', 'o.id');
                })

                ->select([
                    'o.id',
                    'o.user_id',
                    'o.penjual_id',
                    'o.created_at',
                    'o.updated_at',

                    "o.$orderStatusCol as status_pesanan",
                    'osl_last.last_status_at',

                    \DB::raw('COALESCE(osl_last.last_status_at, o.updated_at) as tanggal_selesai'),

                    'ub.name as pembeli_nama',
                    \DB::raw("COALESCE(pj_u.nama_toko, pj_id.nama_toko, up.name, '-') as nama_toko"),
                ]);

            if ($orderKodeCol)  $ordersQuery->addSelect("o.$orderKodeCol as kode_order");
            if ($orderTotalCol) $ordersQuery->addSelect("o.$orderTotalCol as total");

            // filter status per tab
            if ($tab === 'monitoring') {
                $ordersQuery->whereIn("o.$orderStatusCol", $aktifStatuses);
            } else {
                $ordersQuery->whereIn("o.$orderStatusCol", $riwayatStatuses);
            }

            // filter penjual (dropdown kirim user_id penjual)
            if (!empty($penjualId)) {
                $ordersQuery->where(function ($w) use ($penjualId) {
                    $w->where('o.penjual_id', $penjualId)
                    ->orWhere('pj_u.user_id', $penjualId)
                    ->orWhere('pj_id.user_id', $penjualId);
                });
            }

            // filter status
            if (!empty($status)) {
                $ordersQuery->where("o.$orderStatusCol", $status);
            }

            // filter tanggal (created_at)
            if (!empty($start)) $ordersQuery->whereDate('o.created_at', '>=', $start);
            if (!empty($end))   $ordersQuery->whereDate('o.created_at', '<=', $end);

            // search
            if ($q !== '') {
                $ordersQuery->where(function ($w) use ($q, $orderKodeCol) {
                    if ($orderKodeCol) $w->where("o.$orderKodeCol", 'like', "%{$q}%");
                    $w->orWhere('ub.name', 'like', "%{$q}%")
                    ->orWhere('pj_u.nama_toko', 'like', "%{$q}%")
                    ->orWhere('pj_id.nama_toko', 'like', "%{$q}%")
                    ->orWhere('up.name', 'like', "%{$q}%");
                });
            }

            $orders = $ordersQuery
                ->orderByDesc(\DB::raw('COALESCE(osl_last.last_status_at, o.updated_at, o.created_at)'))
                ->paginate(10)
                ->withQueryString();

        } else {

            // TAB aduan
            $aduansQuery = \DB::table('aduans as a')
                ->join('orders as o', 'o.id', '=', 'a.order_id')
                ->leftJoin('users as ub', 'ub.id', '=', 'a.user_id')

                // fallback penjual user
                ->leftJoin('users as up', 'up.id', '=', 'o.penjual_id')

                // ✅ FIX DOBEL: join penjuals dipisah (tanpa orOn)
                ->leftJoin('penjuals as pj_u', 'pj_u.user_id', '=', 'o.penjual_id')
                ->leftJoin('penjuals as pj_id', function ($join) {
                    $join->on('pj_id.id', '=', 'o.penjual_id')
                        ->whereNull('pj_u.id');
                })

                ->leftJoinSub($lastLogSub, 'osl_last', function ($join) {
                    $join->on('osl_last.order_id', '=', 'o.id');
                })

                ->select([
                    'a.id',
                    'a.order_id',
                    'a.judul',
                    'a.deskripsi',
                    'a.created_at',
                    "o.$orderStatusCol as status_pesanan",
                    'osl_last.last_status_at',
                    'ub.name as pembeli_nama',
                    \DB::raw("COALESCE(pj_u.nama_toko, pj_id.nama_toko, up.name, '-') as nama_toko"),
                ]);

            if ($aduanStatusCol) $aduansQuery->addSelect("a.$aduanStatusCol as status_aduan");
            if ($orderKodeCol)   $aduansQuery->addSelect("o.$orderKodeCol as kode_order");

            if (!empty($penjualId)) {
                $aduansQuery->where(function ($w) use ($penjualId) {
                    $w->where('o.penjual_id', $penjualId)
                    ->orWhere('pj_u.user_id', $penjualId)
                    ->orWhere('pj_id.user_id', $penjualId);
                });
            }

            if (!empty($status)) {
                if ($aduanStatusCol) $aduansQuery->where("a.$aduanStatusCol", $status);
                else $aduansQuery->where("o.$orderStatusCol", $status);
            }

            if (!empty($start)) $aduansQuery->whereDate('a.created_at', '>=', $start);
            if (!empty($end))   $aduansQuery->whereDate('a.created_at', '<=', $end);

            if ($q !== '') {
                $aduansQuery->where(function ($w) use ($q, $orderKodeCol) {
                    $w->where('a.judul', 'like', "%{$q}%")
                    ->orWhere('ub.name', 'like', "%{$q}%")
                    ->orWhere('pj_u.nama_toko', 'like', "%{$q}%")
                    ->orWhere('pj_id.nama_toko', 'like', "%{$q}%")
                    ->orWhere('up.name', 'like', "%{$q}%");
                    if ($orderKodeCol) $w->orWhere("o.$orderKodeCol", 'like', "%{$q}%");
                });
            }

            $aduans = $aduansQuery
                ->orderByDesc('a.created_at')
                ->paginate(10)
                ->withQueryString();
        }

        return view('admin.transaksi', [
            'tab' => $tab,
            'q' => $q,

            'penjualId' => $penjualId,
            'status' => $status,
            'start' => $start,
            'end' => $end,

            'penjualList' => $penjualList,

            'orders' => $orders,
            'aduans' => $aduans,

            'hasOrderKode' => (bool) $orderKodeCol,
            'hasOrderTotal' => (bool) $orderTotalCol,
            'hasAduanStatus' => (bool) $aduanStatusCol,

            // ✅ badge counts untuk tabs
            'monitoringCount' => $monitoringCount,
            'aduanCount' => $aduanCount,
        ]);
    }

    public function showAduan(Request $request, $id)
    {
        if (Schema::hasColumn('orders', 'status_pesanan')) $orderStatusCol = 'status_pesanan';
        elseif (Schema::hasColumn('orders', 'status_pesanna')) $orderStatusCol = 'status_pesanna';
        else $orderStatusCol = 'status_pesanan';

        $orderKodeCol = Schema::hasColumn('orders', 'kode_order') ? 'kode_order' : null;

        $hasBukti             = Schema::hasColumn('aduans', 'bukti');
        $hasStatusAduan       = Schema::hasColumn('aduans', 'status_aduan');
        $hasCatatanAdmin      = Schema::hasColumn('aduans', 'catatan_admin');
        $hasTglCatatanAdmin   = Schema::hasColumn('aduans', 'tgl_catatan_admin');
        $hasCatatanPenjual    = Schema::hasColumn('aduans', 'catatan_penjual');
        $hasTglCatatanPenjual = Schema::hasColumn('aduans', 'tgl_catatan_penjual');

        // FIX DUPLIKASI: 2 join penjuals terpisah
        $q = DB::table('aduans as a')
            ->join('orders as o', 'o.id', '=', 'a.order_id')
            ->leftJoin('users as ub', 'ub.id', '=', 'a.user_id')
            ->leftJoin('penjuals as pj_u', 'pj_u.user_id', '=', 'o.penjual_id')
            ->leftJoin('penjuals as pj_i', 'pj_i.id', '=', 'o.penjual_id')
            ->where('a.id', $id)
            ->select([
                'a.id',
                'a.order_id',
                'a.user_id',
                'a.penjual_id',
                'a.judul',
                'a.deskripsi',
                'a.created_at',
                "o.$orderStatusCol as status_pesanan",
                'ub.name as pembeli_nama',
                DB::raw("COALESCE(pj_u.nama_toko, pj_i.nama_toko, '-') as nama_toko"),
            ]);

        if ($orderKodeCol) $q->addSelect("o.$orderKodeCol as kode_order");

        if ($hasBukti)             $q->addSelect('a.bukti');
        if ($hasStatusAduan)       $q->addSelect('a.status_aduan');
        if ($hasCatatanAdmin)      $q->addSelect('a.catatan_admin');
        if ($hasTglCatatanAdmin)   $q->addSelect('a.tgl_catatan_admin');
        if ($hasCatatanPenjual)    $q->addSelect('a.catatan_penjual');
        if ($hasTglCatatanPenjual) $q->addSelect('a.tgl_catatan_penjual');

        $aduan = $q->first();
        abort_if(!$aduan, 404);

        $logs = DB::table('order_status_logs')
            ->where('order_id', $aduan->order_id)
            ->orderBy('created_at')
            ->get();

        return view('admin.aduan_show', compact('aduan', 'logs'));
    }

    public function updateStatusAduan(Request $request, $id)
    {
        $request->validate([
            'status_aduan' => 'required|in:menunggu,diproses,selesai,dibatalkan',
        ]);

        DB::table('aduans')
            ->where('id', $id)
            ->update([
                'status_aduan' => $request->status_aduan,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Status aduan diperbarui.');
    }

    public function tanggapiAduan(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:2000',
        ]);

        DB::table('aduans')
            ->where('id', $id)
            ->update([
                'catatan_admin' => $request->catatan_admin,
                'tgl_catatan_admin' => now(),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Catatan admin berhasil disimpan.');
    }
}
