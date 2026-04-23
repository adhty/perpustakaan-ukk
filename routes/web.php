<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\PengembalianApprovalController;
use App\Http\Controllers\Admin\ApproveDendaController; // GANTI dengan ApproveDendaController
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Siswa\PeminjamanController;
use App\Http\Controllers\Siswa\PengembalianController;
use App\Http\Controllers\Siswa\DendaController;

// ─── AUTH ───────────────────────────────────────────────

// ✅ FIX: SELALU KE LOGIN
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================= LUPA PASSWORD =================
Route::middleware('guest')->group(function () {

    Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('forgot');
    Route::post('/forgot-password', [AuthController::class, 'forgot'])->name('forgot.post');

    Route::get('/reset-password', [AuthController::class, 'showReset'])->name('reset.form');
    Route::post('/reset-password', [AuthController::class, 'reset'])->name('reset.post');
});

// ─── ADMIN ──────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // 🔥 TEMPLATE (HARUS DI ATAS RESOURCE)
    Route::get('buku/template', [BukuController::class, 'downloadTemplate'])
        ->name('buku.template');

    // 📚 Kelola Buku
    Route::resource('buku', BukuController::class);
    Route::post('buku/import', [BukuController::class, 'import'])->name('buku.import');

    // 👥 Kelola Anggota
    Route::resource('anggota', AnggotaController::class)->parameters(['anggota' => 'anggota']);

    // 🔄 Kelola Transaksi
    Route::resource('transaksi', TransaksiController::class);
    Route::post('transaksi/{id}/approve', [TransaksiController::class, 'approve'])->name('transaksi.approve');
    Route::post('transaksi/{id}/reject', [TransaksiController::class, 'reject'])->name('transaksi.reject');
    Route::post('transaksi/{id}/kembalikan', [TransaksiController::class, 'kembalikan'])->name('transaksi.kembalikan');
    Route::get('laporan', [TransaksiController::class, 'laporan'])->name('laporan');

    // 🏷️ Kelola Kategori
    Route::resource('kategori', KategoriController::class)->only(['index', 'store', 'update', 'destroy']);

    // ================= APPROVAL DENDA (ADMIN) =================
    Route::prefix('denda')->name('denda.')->group(function () {
        Route::get('/', [ApproveDendaController::class, 'index'])->name('index');
        Route::post('/{id}/approve', [ApproveDendaController::class, 'approve'])->name('approve');
        // Route reject dihapus karena tidak ada di ApproveDendaController
        // ================= APPROVAL DENDA (ADMIN) =================
Route::prefix('denda')->name('denda.')->group(function () {
    Route::get('/', [ApproveDendaController::class, 'index'])->name('index');
    Route::post('/{id}/approve', [ApproveDendaController::class, 'approve'])->name('approve');
    Route::post('/{id}/reject', [ApproveDendaController::class, 'reject'])->name('reject');
});
    });

    // ================= APPROVAL PENGEMBALIAN =================
    Route::prefix('pengembalian-approval')->name('pengembalian-approval.')->group(function () {
        Route::get('/', [PengembalianApprovalController::class, 'index'])->name('index');
        Route::get('/{id}', [PengembalianApprovalController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [PengembalianApprovalController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [PengembalianApprovalController::class, 'reject'])->name('reject');
        Route::post('/batch-approve', [PengembalianApprovalController::class, 'batchApprove'])->name('batch.approve');
        Route::get('/export', [PengembalianApprovalController::class, 'export'])->name('export');
        Route::get('/search', [PengembalianApprovalController::class, 'search'])->name('search');
    });
});

// ================= ADMIN OTP =================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/reset-password-requests', [AdminDashboard::class, 'resetRequests'])->name('reset.index');
    Route::post('/reset-password/{id}/send-otp', [AdminDashboard::class, 'sendOtp'])->name('reset.sendOtp');
});

// ─── SISWA ──────────────────────────────────────────────
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {

    Route::get('/dashboard', [SiswaDashboard::class, 'index'])->name('dashboard');

    // 📖 Peminjaman
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('/peminjaman/riwayat', [PeminjamanController::class, 'riwayat'])->name('peminjaman.riwayat');
    Route::post('/peminjaman/{id}/cancel', [PeminjamanController::class, 'cancel'])->name('peminjaman.cancel');
    Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.show');

    // 🔁 Pengembalian
    Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::get('/pengembalian/{id}', [PengembalianController::class, 'show'])->name('pengembalian.show');
    Route::post('/pengembalian/{id}', [PengembalianController::class, 'store'])->name('pengembalian.store');
    Route::post('/pengembalian/{id}/cancel', [PengembalianController::class, 'cancel'])->name('pengembalian.cancel');

    // 💰 Denda (SISWA)
    Route::get('/denda', [DendaController::class, 'index'])->name('denda.index');
    Route::post('/denda/{id}/bayar', [DendaController::class, 'bayar'])->name('denda.bayar');

    // 👤 Profil
    Route::get('/profil', [SiswaDashboard::class, 'profil'])->name('profil');
    Route::put('/profil', [SiswaDashboard::class, 'updateProfil'])->name('profil.update');
});