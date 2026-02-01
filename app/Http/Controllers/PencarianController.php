<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Produk;

class PencarianController extends Controller
{
    /**
     * Search marketplace + LBS (Haversine) jika lokasi pembeli tersedia.
     * - lokasi pembeli: tabel `pembeli` (idUser, latitude, longitude)
     * - lokasi penjual: tabel `penjuals` (user_id, latitude, longitude)
     * - produk: tabel `produk` (user_id)
     */
    public function searchNearby(Request $request)
    {
        $q     = trim($request->input('q', ''));
        $maxKm = (float) ($request->input('max_km', 10)); // default 10 km

        // ✅ Ambil lokasi pembeli dari tabel pembeli (BUKAN users)
        $buyer = DB::table('pembeli')
            ->where('idUser', auth()->id())
            ->select('latitude', 'longitude')
            ->first();

        $buyerLat = $buyer->latitude ?? null;
        $buyerLng = $buyer->longitude ?? null;

        // Base query produk
        $base = Produk::query()
            ->where('produk.is_active', 1)
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($sub) use ($q) {
                    $sub->where('produk.nama_barang', 'like', "%{$q}%");
                        // ->orWhere('produk.deskripsi', 'like', "%{$q}%");
                });
            });

        /**
         * ✅ Kalau lokasi pembeli belum ada:
         * Tampilkan hasil normal tanpa jarak (tidak redirect ke profile).
         */
        if (!$buyerLat || !$buyerLng) {
            $products = $base
                ->orderByDesc('produk.created_at')
                ->paginate(12)
                ->withQueryString();

            $lbs_enabled = false;

            return view('pembeli.hasilpencarian', compact('products', 'q', 'maxKm', 'lbs_enabled'));
        }

        // ✅ Bounding box biar cepat
        $latDelta = $maxKm / 111;
        $lngDelta = $maxKm / (111 * cos(deg2rad($buyerLat)));

        /**
         * ✅ LBS mode:
         * join ke tabel penjuals (bukan penjual)
         */
        $products = $base
            ->join('penjuals as pj', 'pj.id', '=', 'produk.penjual_id')
            ->whereNotNull('pj.latitude')
            ->whereNotNull('pj.longitude')
            ->whereBetween('pj.latitude',  [$buyerLat - $latDelta, $buyerLat + $latDelta])
            ->whereBetween('pj.longitude', [$buyerLng - $lngDelta, $buyerLng + $lngDelta])
            ->select('produk.*')
            ->selectRaw(
                "(6371 * acos(
                    cos(radians(?)) * cos(radians(pj.latitude)) *
                    cos(radians(pj.longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(pj.latitude))
                )) AS distance",
                [$buyerLat, $buyerLng, $buyerLat]
            )
            ->having('distance', '<=', $maxKm)
            ->orderBy('distance', 'asc')
            ->paginate(12)
            ->withQueryString();

        $lbs_enabled = true;

        return view('pembeli.hasilpencarian', compact('products', 'q', 'maxKm', 'lbs_enabled'));
    }
}
