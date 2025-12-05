<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PembayaranController;

/*
|--------------------------------------------------------------------------
| PUBLIC AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/admin/register', [AdminController::class, 'register']);
Route::post('/admin/login', [AdminController::class, 'login']);

Route::post('/pelanggan/register', [PelangganController::class, 'register']);
Route::post('/pelanggan/login', [PelangganController::class, 'login']);


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (SANCTUM)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // LOGOUT
    Route::post('/logout', function () {
        auth()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    });

    /*
    |--------------------------------------------------------------------------
    | PELANGGAN-ONLY ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('pelanggan')->group(function () {
        Route::post('/pemesanan/create', [PemesananController::class, 'store']);
        Route::post('/pembayaran/create', [PembayaranController::class, 'store']);
    });

    /*
    |--------------------------------------------------------------------------
    | UNIVERSAL READ ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/pemesanan/read', [PemesananController::class, 'index']);
    Route::post('/pemesanan/update', [PemesananController::class, 'update']);
    Route::delete('/pemesanan/delete/{id}', [PemesananController::class, 'destroy']);

    Route::get('/pembayaran/read', [PembayaranController::class, 'index']);
    Route::get('/pembayaran/pendapatan', [PembayaranController::class, 'totalPendapatan']);

    Route::get('/layanan/read', [LayananController::class, 'index']);
    Route::get('/pegawai/read', [PegawaiController::class, 'index']);

    // 🔥 Read Pelanggan untuk Admin Dashboard
    Route::get('/pelanggan/read', [PelangganController::class, 'index']);


    /*
    |--------------------------------------------------------------------------
    | ADMIN FULL CRUD ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {

        // ========= PEGAWAI =========
        Route::post('/pegawai/create', [PegawaiController::class, 'store']);
        Route::post('/pegawai/update', [PegawaiController::class, 'update']);
        Route::delete('/pegawai/delete/{id}', [PegawaiController::class, 'destroy']);

        // ========= LAYANAN =========
        Route::post('/layanan/create', [LayananController::class, 'store']);
        Route::post('/layanan/update', [LayananController::class, 'update']);
        Route::delete('/layanan/delete/{id}', [LayananController::class, 'destroy']);

        // ========= PELANGGAN (ADMIN CRUD) =========
        Route::post('/pelanggan/update/{id}', [PelangganController::class, 'update']);
        Route::delete('/pelanggan/delete/{id}', [PelangganController::class, 'destroy']);
    });
});
