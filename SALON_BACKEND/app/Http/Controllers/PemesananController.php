<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use Illuminate\Http\Request;

class PemesananController extends Controller
{
    public function index()
{
    $user = auth()->user();

    // Admin lihat semua
    if ($user instanceof \App\Models\Admin) {
        return response()->json(
            Pemesanan::with(['pelanggan', 'layanan', 'pegawai'])->get()
        );
    }

    // Pelanggan lihat semua pesanan miliknya (termasuk dibatalkan)
    return response()->json(
        Pemesanan::with(['layanan', 'pegawai'])
            ->where('id_pelanggan', $user->id_pelanggan)
            ->get()
    );
}


    public function store(Request $request)
    {
        $request->validate([
            'id_layanan' => 'required|exists:layanan,id_layanan',
            'id_pegawai' => 'required|exists:pegawai,id_pegawai',
            'tanggal_booking' => 'required|date',
            'jam_booking' => 'required'
        ]);

        $user = auth()->user();

        $pemesanan = Pemesanan::create([
            'id_pelanggan' => $user->id_pelanggan,
            'id_layanan' => $request->id_layanan,
            'id_pegawai' => $request->id_pegawai,
            'tanggal_pemesanan' => now(),
            'tanggal_booking' => $request->tanggal_booking,
            'jam_booking' => $request->jam_booking,
            'status_pemesanan' => 'pending'
        ]);

        return response()->json([
            'message' => 'Pemesanan berhasil dibuat',
            'data' => $pemesanan
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id_pemesanan' => 'required|exists:pemesanan,id_pemesanan',
            'id_layanan' => 'required|exists:layanan,id_layanan',
            'id_pegawai' => 'required|exists:pegawai,id_pegawai',
            'tanggal_booking' => 'required|date',
            'jam_booking' => 'required'
        ]);

        $pemesanan = Pemesanan::find($request->id_pemesanan);

        // Cegah pelanggan update milik orang lain
        $user = auth()->user();
        if ($user instanceof \App\Models\Pelanggan && $pemesanan->id_pelanggan !== $user->id_pelanggan) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pemesanan->update([
    'id_layanan' => $request->id_layanan,
    'id_pegawai' => $request->id_pegawai,
    'tanggal_booking' => $request->tanggal_booking,
    'jam_booking' => $request->jam_booking,
    'status_pemesanan' => $request->status_pemesanan ?? $pemesanan->status_pemesanan
]);


        return response()->json([
            'message' => 'Pemesanan berhasil diperbarui',
            'data' => $pemesanan
        ]);
    }

    public function destroy($id)
{
    $pemesanan = Pemesanan::find($id);

    if (!$pemesanan) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    $user = auth()->user();

    // Pelanggan hanya boleh membatalkan miliknya sendiri
    if ($user instanceof \App\Models\Pelanggan && $pemesanan->id_pelanggan !== $user->id_pelanggan) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $pemesanan->update([
        'status_pemesanan' => 'dibatalkan'
    ]);

    return response()->json(['message' => 'Pesanan berhasil dibatalkan', 'data' => $pemesanan]);
}

}
