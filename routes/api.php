<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BukuApiController;
use App\Http\Controllers\Api\PeminjamanApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\AnggotaApiController;

// ─── PUBLIC (tanpa token) ────────────────────────────────
Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/login',    [AuthApiController::class, 'login']);
    Route::post('/register', [AuthApiController::class, 'register']);

    // ─── PROTECTED (butuh Sanctum token) ────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/me',      [AuthApiController::class, 'me']);

        // Dashboard
        Route::get('/dashboard', [DashboardApiController::class, 'index']);

        // Buku (semua user bisa lihat)
        Route::get('/buku',        [BukuApiController::class, 'index']);
        Route::get('/buku/{id}',   [BukuApiController::class, 'show']);

        // Peminjaman Siswa
        Route::get('/peminjaman',          [PeminjamanApiController::class, 'index']);
        Route::post('/peminjaman',         [PeminjamanApiController::class, 'store']);
        Route::get('/peminjaman/aktif',    [PeminjamanApiController::class, 'aktif']);
        Route::get('/peminjaman/riwayat',  [PeminjamanApiController::class, 'riwayat']);
        Route::post('/peminjaman/{id}/kembalikan', [PeminjamanApiController::class, 'kembalikan']);

        // ─── ADMIN ONLY ──────────────────────────────────
        Route::middleware('role:admin')->group(function () {

            // CRUD Buku
            Route::post('/buku',        [BukuApiController::class, 'store']);
            Route::put('/buku/{id}',    [BukuApiController::class, 'update']);
            Route::delete('/buku/{id}', [BukuApiController::class, 'destroy']);

            // Kelola Anggota
            Route::get('/anggota',         [AnggotaApiController::class, 'index']);
            Route::get('/anggota/{id}',    [AnggotaApiController::class, 'show']);
            Route::post('/anggota',        [AnggotaApiController::class, 'store']);
            Route::put('/anggota/{id}',    [AnggotaApiController::class, 'update']);
            Route::delete('/anggota/{id}', [AnggotaApiController::class, 'destroy']);

            // Semua transaksi (admin)
            Route::get('/transaksi',               [PeminjamanApiController::class, 'adminIndex']);
            Route::post('/transaksi/{id}/kembalikan', [PeminjamanApiController::class, 'adminKembalikan']);
        });
    });
});
