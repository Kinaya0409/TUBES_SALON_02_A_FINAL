<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PelangganController extends Controller
{
    /* =====================================================
     *  REGISTER PELANGGAN
     * ===================================================== */
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:pelanggan,email',
            'password' => 'required|min:6',
            'no_hp' => 'required',
            'alamat' => 'required'
        ]);

        $pelanggan = Pelanggan::create([
            'nama' => $request->nama,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return response()->json([
            'message' => 'Register berhasil',
            'data' => $pelanggan
        ]);
    }

    /* =====================================================
     *  LOGIN PELANGGAN
     * ===================================================== */
    public function login(Request $request)
    {
        // cek apakah email ada
        $user = Pelanggan::where('email', strtolower($request->email))->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email tidak ditemukan, silakan daftar.'
            ], 404);
        }

        // cek password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Password salah.'
            ], 401);
        }

        // jika lolos validasi semua
        $token = $user->createToken('token_pelanggan')->plainTextToken;

        return response()->json([
            'message' => 'Login pelanggan berhasil',
            'token' => $token,
            'data' => $user
        ]);
    }
    /* =====================================================
     *  ADMIN CRUD PELANGGAN
     * ===================================================== */

    // READ ALL
    public function index()
    {
        return response()->json(Pelanggan::all());
    }

    // READ BY ID
    public function show($id)
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404);
        }

        return response()->json($pelanggan);
    }

    // UPDATE PELANGGAN OLEH ADMIN
    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404);
        }

        $pelanggan->update([
            'nama' => $request->nama ?? $pelanggan->nama,
            'email' => strtolower($request->email) ?? $pelanggan->email,
            'no_hp' => $request->no_hp ?? $pelanggan->no_hp,
            'alamat' => $request->alamat ?? $pelanggan->alamat,
        ]);

        return response()->json([
            'message' => 'Data berhasil diupdate',
            'data' => $pelanggan
        ]);
    }

    // DELETE DATA
    public function destroy($id)
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404);
        }

        $pelanggan->delete();

        return response()->json(['message' => 'Data pelanggan berhasil dihapus']);
    }
}
