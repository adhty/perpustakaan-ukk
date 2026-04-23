<?php
// app/Http/Controllers/Admin/ApproveDendaController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApproveDendaController extends Controller
{
    public function index()
    {
        $pendingDenda = Pinjam::with(['user', 'buku'])
            ->where('denda', '>', 0)
            ->where('status_denda', 'menunggu')
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        $riwayatLunas = Pinjam::with(['user', 'buku'])
            ->where('denda', '>', 0)
            ->where('status_denda', 'lunas')
            ->orderBy('tanggal_approve', 'desc')
            ->paginate(15);

        // TAMBAHKAN STATS
        $stats = [
            'total_pending' => $pendingDenda->count(),
            'total_nominal_pending' => $pendingDenda->sum('denda'),
            'total_lunas' => Pinjam::where('denda', '>', 0)->where('status_denda', 'lunas')->sum('denda'),
        ];

        return view('admin.denda.index', compact('pendingDenda', 'riwayatLunas', 'stats'));
    }

    public function approve($id)
    {
        $pinjam = Pinjam::findOrFail($id);
        
        $pinjam->update([
            'status_denda' => 'lunas',
            'tanggal_approve' => now(),
            'approver_id' => Auth::id()
        ]);

        return redirect()->route('admin.denda.index')->with('success', 'Denda berhasil dikonfirmasi lunas.');
    }

    public function reject($id)
    {
        $pinjam = Pinjam::findOrFail($id);
        
        $pinjam->update([
            'status_denda' => 'belum_lunas',
            'tanggal_bayar' => null
        ]);

        return redirect()->route('admin.denda.index')->with('error', 'Pembayaran denda ditolak.');
    }
}