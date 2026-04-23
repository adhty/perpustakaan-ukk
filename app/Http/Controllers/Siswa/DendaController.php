<?php
// app/Http/Controllers/Siswa/DendaController.php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pinjam;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DendaController extends Controller
{
    public function index()
    {
        // Denda yang BELUM LUNAS (belum dibayar)
        $dendaBelumLunas = Pinjam::with('buku')
            ->where('user_id', Auth::id())
            ->where('denda', '>', 0)
            ->where('status_denda', 'belum_lunas')
            ->orderBy('created_at', 'desc')
            ->get();

        // Denda yang MENUNGGU KONFIRMASI ADMIN (sudah bayar tapi belum di approve)
        $dendaMenunggu = Pinjam::with('buku')
            ->where('user_id', Auth::id())
            ->where('denda', '>', 0)
            ->where('status_denda', 'menunggu')
            ->orderBy('created_at', 'desc')
            ->get();

        // Denda yang sudah LUNAS (sudah di approve admin)
        $riwayatLunas = Pinjam::with('buku')
            ->where('user_id', Auth::id())
            ->where('denda', '>', 0)
            ->where('status_denda', 'lunas')
            ->orderBy('updated_at', 'desc')
            ->get();

        $totalDenda = $dendaBelumLunas->sum('denda');

        return view('siswa.denda.index', compact('dendaBelumLunas', 'dendaMenunggu', 'riwayatLunas', 'totalDenda'));
    }

    public function bayar($id)
    {
        $pinjam = Pinjam::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('denda', '>', 0)
            ->where('status_denda', 'belum_lunas')
            ->first();

        if (!$pinjam) {
            return redirect()->route('siswa.denda.index')->with('error', 'Denda tidak ditemukan.');
        }

        DB::beginTransaction();

        try {
            // Update status menjadi menunggu
            $pinjam->update([
                'status_denda' => 'menunggu',
                'tanggal_bayar' => now()
            ]);

            // Kirim notifikasi ke semua admin
            $admins = User::where('role', 'admin')->get();
            $user = Auth::user();

            foreach ($admins as $admin) {
                Notification::create([
                    'title' => '💰 Pembayaran Denda Menunggu Konfirmasi',
                    'message' => "{$user->name} ({$user->kelas}) membayar denda Rp " . number_format($pinjam->denda, 0, ',', '.') . " untuk buku \"{$pinjam->buku->judul}\"",
                    'type' => 'denda',
                    'user_id' => $admin->id,
                    'related_id' => $pinjam->id,
                    'related_type' => 'App\\Models\\Pinjam',
                    'is_read' => false
                ]);
            }

            DB::commit();

            return redirect()->route('siswa.denda.index')->with('success', 'Pembayaran berhasil! Menunggu konfirmasi admin.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('siswa.denda.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}