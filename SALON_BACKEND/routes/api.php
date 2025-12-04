<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PembayaranController;

// Auth Public Routes
Route::post('/admin/register', [AdminController::class, 'register']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/pelanggan/register', [PelangganController::class, 'register']);
Route::post('/pelanggan/login', [PelangganController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', function () {
        auth()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    });

    Route::middleware('pelanggan')->group(function () {
        Route::post('/pemesanan/create', [PemesananController::class, 'store']);
        Route::post('/pembayaran/create', [PembayaranController::class, 'store']);
    });

    Route::get('/pemesanan/read', [PemesananController::class, 'index']);
    Route::post('/pemesanan/update', [PemesananController::class, 'update']);
    Route::delete('/pemesanan/delete/{id}', [PemesananController::class, 'destroy']);

    Route::get('/pembayaran/read', [PembayaranController::class, 'index']);
    Route::get('/pembayaran/pendapatan', [PembayaranController::class, 'totalPendapatan']);

    Route::get('/layanan/read', [LayananController::class, 'index']);

    Route::get('/pegawai/read', [PegawaiController::class, 'index']);

    Route::middleware('admin')->group(function () {
        Route::post('/pegawai/create', [PegawaiController::class, 'store']);
        Route::post('/pegawai/update', [PegawaiController::class, 'update']);
        Route::delete('/pegawai/delete/{id}', [PegawaiController::class, 'destroy']);

        Route::post('/layanan/create', [LayananController::class, 'store']);
        Route::post('/layanan/update', [LayananController::class, 'update']);
        Route::delete('/layanan/delete/{id}', [LayananController::class, 'destroy']);
    });
});
