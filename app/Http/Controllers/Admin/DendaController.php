<?php
// app/Http/Controllers/Admin/DendaController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pinjam;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DendaController extends Controller
{
    public function index()
    {
        // Denda yang menunggu konfirmasi
        $pendingDenda = Pinjam::with(['user', 'buku'])
            ->where('denda', '>', 0)
            ->where('status_denda', 'menunggu')
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        // Denda yang sudah lunas
        $lunasDenda = Pinjam::with(['user', 'buku', 'approver'])
            ->where('denda', '>', 0)
            ->where('status_denda', 'lunas')
            ->orderBy('tanggal_approve', 'desc')
            ->paginate(15);

        // Denda yang belum dibayar
        $belumLunasDenda = Pinjam::with(['user', 'buku'])
            ->where('denda', '>', 0)
            ->where('status_denda', 'belum_lunas')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_pending' => $pendingDenda->count(),
            'total_belum_lunas' => $belumLunasDenda->sum('denda'),
            'total_lunas' => Pinjam::where('denda', '>', 0)->where('status_denda', 'lunas')->sum('denda'),
            'total_menunggu' => $pendingDenda->sum('denda'),
        ];

        return view('admin.denda.index', compact('pendingDenda', 'lunasDenda', 'belumLunasDenda', 'stats'));
    }

    public function show($id)
    {
        $pinjam = Pinjam::with(['user', 'buku'])
            ->where('denda', '>', 0)
            ->findOrFail($id);

        return view('admin.denda.show', compact('pinjam'));
    }

    public function approve(Request $request, $id)
    {
        $pinjam = Pinjam::with('user', 'buku')
            ->where('denda', '>', 0)
            ->where('status_denda', 'menunggu')
            ->findOrFail($id);

        $pinjam->update([
            'status_denda' => 'lunas',
            'tanggal_approve' => now(),
            'approver_id' => Auth::id(),
            'keterangan_denda' => $request->keterangan ?? 'Denda disetujui admin'
        ]);

        // Kirim notifikasi ke siswa
        Notification::create([
            'title' => '✅ Denda Telah Dikonfirmasi',
            'message' => "Denda sebesar Rp " . number_format($pinjam->denda, 0, ',', '.') . " untuk buku \"{$pinjam->buku->judul}\" telah dikonfirmasi lunas.",
            'type' => 'denda',
            'user_id' => $pinjam->user_id,
            'related_id' => $pinjam->id,
            'related_type' => 'App\\Models\\Pinjam',
            'is_read' => false
        ]);

        return redirect()->route('admin.denda.index')
            ->with('success', 'Denda berhasil dikonfirmasi lunas.');
    }

    public function reject(Request $request, $id)
    {
        $pinjam = Pinjam::with('user', 'buku')
            ->where('denda', '>', 0)
            ->where('status_denda', 'menunggu')
            ->findOrFail($id);

        $pinjam->update([
            'status_denda' => 'belum_lunas',
            'keterangan_denda' => $request->alasan,
            'tanggal_bayar' => null
        ]);

        // Kirim notifikasi ke siswa
        Notification::create([
            'title' => '❌ Pembayaran Denda Ditolak',
            'message' => "Pembayaran denda untuk buku \"{$pinjam->buku->judul}\" ditolak. Alasan: {$request->alasan}",
            'type' => 'denda',
            'user_id' => $pinjam->user_id,
            'related_id' => $pinjam->id,
            'related_type' => 'App\\Models\\Pinjam',
            'is_read' => false
        ]);

        return redirect()->route('admin.denda.index')
            ->with('error', 'Pembayaran denda ditolak.');
    }
}