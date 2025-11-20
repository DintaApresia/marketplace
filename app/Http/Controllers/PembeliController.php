<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembeli;
use Illuminate\Support\Facades\Auth;

class PembeliController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        $pembeli = Pembeli::where('idUser', $user->id)->first();
         return view('profile', [
            'user'    => $user,
            'pembeli' => $pembeli,
        ]);
    }

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

        Pembeli::updateOrCreate(
            ['idUser' => $user->id],
            [
                'nama_pembeli' => $validated['receiver_name'],
                'no_telp'   => $validated['phone'],
                'alamat'       => $validated['address_line'] ?? '',
                'latitude'     => $validated['latitude'] ?? null,
                'longitude'    => $validated['longitude'] ?? null,
            ]
        );

        // balik ke halaman profile (GET) yang pakai method profile()
        return redirect()
            ->route('profile')
            ->with('success', 'Preferensi pembeli berhasil disimpan.');
    }
}