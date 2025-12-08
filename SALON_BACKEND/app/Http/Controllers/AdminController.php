<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email|unique:admin,email',
            'password' => 'required|min:6',
        ]);

        $admin = Admin::create([
            'nama' => $request->nama,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Admin berhasil register', 'data' => $admin]);
    }

    public function login(Request $request)
    {
        $admin = Admin::where('email', strtolower($request->email))->first();

        if (!$admin) {
            return response()->json([
                'message' => 'Email tidak ditemukan.'
            ], 404);
        }

        if (!Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Password salah.'
            ], 401);
        }

        $token = $admin->createToken('token_admin')->plainTextToken;

        return response()->json([
            'message' => 'Login admin berhasil',
            'token' => $token,
            'data' => $admin
        ]);
    }

}
