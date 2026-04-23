<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Pinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    public function index()
    {
        // Buku yang sedang dipinjam dan BELUM request pengembalian
        $pinjams = Pinjam::with('buku')
            ->where('user_id', Auth::id())
            ->where('status', 'dipinjam')
            ->where('status_pengembalian', 'pending') // Hanya yang belum request
            ->orderBy('tgl_kembali_rencana', 'asc')
            ->get();

        // Buku yang sudah request pengembalian (menunggu approval)
        $pendingReturns = Pinjam::with('buku')
            ->where('user_id', Auth::id())
            ->where('status', 'dipinjam')
            ->where('status_pengembalian', 'pending')
            ->whereNotNull('keterangan')
            ->where('keterangan', 'like', 'Request pengembalian%')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Riwayat pengembalian yang sudah disetujui
        $approvedReturns = Pinjam::with('buku', 'approver')
            ->where('user_id', Auth::id())
            ->where('status_pengembalian', 'disetujui')
            ->orderBy('tanggal_pengembalian_approve', 'desc')
            ->get();

        // Riwayat pengembalian yang ditolak
        $rejectedReturns = Pinjam::with('buku')
            ->where('user_id', Auth::id())
            ->where('status_pengembalian', 'ditolak')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('siswa.pengembalian.index', compact(
            'pinjams', 
            'pendingReturns', 
            'approvedReturns', 
            'rejectedReturns'
        ));
    }

    /**
     * Mengajukan request pengembalian (bukan langsung mengembalikan)
     */
    public function store(Request $request, int $id)
    {
        $pinjam = Pinjam::where('user_id', Auth::id())
            ->where('status', 'dipinjam')
            ->where('status_pengembalian', 'pending')
            ->findOrFail($id);

        // Cek apakah sudah pernah request sebelumnya
        if ($pinjam->status_pengembalian !== 'pending') {
            return back()->with('error', 'Anda sudah mengajukan request untuk buku ini.');
        }

        // Hitung denda sementara (akan dihitung ulang oleh admin saat approve)
        $tglKembali = Carbon::today();
        $dendaSementara = 0;

        if ($tglKembali->gt($pinjam->tgl_kembali_rencana)) {
            $hari = $tglKembali->diffInDays($pinjam->tgl_kembali_rencana);
            $dendaSementara = $hari * 1000;
        }

        // Simpan request pengembalian (tidak mengubah status buku)
        $pinjam->update([
            'status_pengembalian' => 'pending', // Menunggu approval admin
            'keterangan' => 'Request pengembalian buku - ' . now() . ' (Denda sementara: Rp ' . number_format($dendaSementara, 0, ',', '.') . ')'
        ]);

        $pesan = "Request pengembalian buku \"{$pinjam->buku->judul}\" berhasil dikirim. ";
        $pesan .= "Menunggu persetujuan admin.";
        
        if ($dendaSementara > 0) {
            $pesan .= " Denda sementara: Rp " . number_format($dendaSementara, 0, ',', '.');
        }

        return redirect()->route('siswa.pengembalian.index')->with('success', $pesan);
    }

    /**
     * Membatalkan request pengembalian
     */
    public function cancel(Request $request, int $id)
    {
        $pinjam = Pinjam::where('user_id', Auth::id())
            ->where('status', 'dipinjam')
            ->where('status_pengembalian', 'pending')
            ->findOrFail($id);

        // Cek apakah keterangan berisi request pengembalian
        if (!$pinjam->keterangan || !str_contains($pinjam->keterangan, 'Request pengembalian')) {
            return back()->with('error', 'Tidak ada request pengembalian yang aktif.');
        }

        $pinjam->update([
            'status_pengembalian' => 'pending', // Reset ke pending
            'keterangan' => 'Request pengembalian dibatalkan - ' . now()
        ]);

        return redirect()->route('siswa.pengembalian.index')
            ->with('success', "Request pengembalian buku \"{$pinjam->buku->judul}\" dibatalkan.");
    }

    /**
     * Menampilkan detail request pengembalian
     */
    public function show($id)
    {
        $pinjam = Pinjam::with(['buku', 'approver'])
            ->where('user_id', Auth::id())
            ->whereIn('status_pengembalian', ['pending', 'disetujui', 'ditolak'])
            ->findOrFail($id);

        $terlambat = Carbon::today()->gt($pinjam->tgl_kembali_rencana);
        $hariTerlambat = $terlambat ? abs(Carbon::today()->diffInDays($pinjam->tgl_kembali_rencana, false)) : 0;
        $denda = $hariTerlambat * 1000;

        return view('siswa.pengembalian.show', compact('pinjam', 'terlambat', 'hariTerlambat', 'denda'));
    }
}