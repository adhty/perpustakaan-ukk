<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengembalianApprovalController extends Controller
{
    /**
     * Menampilkan daftar request pengembalian
     */
    public function index()
    {
        // Request yang pending (menunggu approval)
        $pendingReturns = Pinjam::with(['user', 'buku'])
            ->where('status', 'dipinjam')
            ->where('status_pengembalian', 'pending')
            ->where('keterangan', 'like', 'Request pengembalian%')
            ->orderBy('tgl_kembali_rencana', 'asc')
            ->get();

        // Riwayat yang sudah diproses (disetujui/ditolak)
        $approvedReturns = Pinjam::with(['user', 'buku', 'approver'])
            ->whereIn('status_pengembalian', ['disetujui', 'ditolak'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        // Statistik
        $statistics = [
            'total_pending' => $pendingReturns->count(),
            'total_approved_today' => Pinjam::where('status_pengembalian', 'disetujui')
                ->whereDate('tanggal_pengembalian_approve', Carbon::today())
                ->count(),
            'total_rejected_today' => Pinjam::where('status_pengembalian', 'ditolak')
                ->whereDate('updated_at', Carbon::today())
                ->count(),
        ];

        return view('admin.pengembalian-approval.index', compact('pendingReturns', 'approvedReturns', 'statistics'));
    }

    /**
     * Menampilkan detail request pengembalian
     */
    public function show($id)
    {
        $pinjam = Pinjam::with(['user', 'buku'])
            ->where('id', $id)
            ->where('status', 'dipinjam')
            ->where('status_pengembalian', 'pending')
            ->firstOrFail();

        // Hitung keterlambatan dan denda
        $terlambat = Carbon::today()->gt($pinjam->tgl_kembali_rencana);
        $hariTerlambat = $terlambat ? abs(Carbon::today()->diffInDays($pinjam->tgl_kembali_rencana, false)) : 0;
        $denda = $hariTerlambat * 1000;

        // Ambil informasi tambahan
        $totalPinjamanUser = Pinjam::where('user_id', $pinjam->user_id)
            ->where('status', 'dipinjam')
            ->count();

        $riwayatTerlambat = Pinjam::where('user_id', $pinjam->user_id)
            ->where('status', 'dikembalikan')
            ->where('denda', '>', 0)
            ->count();

        return view('admin.pengembalian-approval.show', compact(
            'pinjam', 
            'terlambat', 
            'hariTerlambat', 
            'denda',
            'totalPinjamanUser',
            'riwayatTerlambat'
        ));
    }

    /**
     * Menyetujui pengembalian buku
     */
    public function approve($id)
    {
        $pinjam = Pinjam::where('id', $id)
            ->where('status', 'dipinjam')
            ->where('status_pengembalian', 'pending')
            ->firstOrFail();

        // Hitung denda jika terlambat
        $terlambat = Carbon::today()->gt($pinjam->tgl_kembali_rencana);
        $hariTerlambat = $terlambat ? abs(Carbon::today()->diffInDays($pinjam->tgl_kembali_rencana, false)) : 0;
        $denda = $hariTerlambat * 1000;

        // Update data peminjaman
        $pinjam->update([
            'status' => 'dikembalikan',
            'status_pengembalian' => 'disetujui',
            'tgl_kembali_aktual' => Carbon::today(),
            'denda' => $denda,
            'status_denda' => $denda > 0 ? 'belum_lunas' : 'lunas',
            'tanggal_pengembalian_approve' => Carbon::now(),
            'disetujui_oleh' => Auth::id(),
            'keterangan' => 'Pengembalian disetujui oleh ' . Auth::user()->name . ' - ' . Carbon::now()
        ]);

        // Tambah stok buku
        $pinjam->buku->increment('stok_tersedia');

        // Buat pesan sukses
        $message = "Pengembalian buku \"{$pinjam->buku->judul}\" oleh {$pinjam->user->name} telah disetujui.";
        if ($denda > 0) {
            $message .= " Denda terlambat: Rp " . number_format($denda, 0, ',', '.');
        }

        return redirect()->route('admin.pengembalian-approval.index')
            ->with('success', $message);
    }

    /**
     * Menolak pengembalian buku
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|min:10|max:500'
        ]);

        $pinjam = Pinjam::where('id', $id)
            ->where('status', 'dipinjam')
            ->where('status_pengembalian', 'pending')
            ->firstOrFail();

        $pinjam->update([
            'status_pengembalian' => 'ditolak',
            'alasan_penolakan_pengembalian' => $request->alasan_penolakan,
            'disetujui_oleh' => Auth::id(),
            'keterangan' => 'Pengembalian ditolak oleh ' . Auth::user()->name . ': ' . $request->alasan_penolakan
        ]);

        return redirect()->route('admin.pengembalian-approval.index')
            ->with('warning', "Pengembalian buku \"{$pinjam->buku->judul}\" ditolak. Alasan: {$request->alasan_penolakan}");
    }

    /**
     * Approve multiple pengembalian sekaligus (batch approval)
     */
    public function batchApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pinjams,id'
        ]);

        $successCount = 0;
        $totalDenda = 0;

        foreach ($request->ids as $id) {
            $pinjam = Pinjam::where('id', $id)
                ->where('status', 'dipinjam')
                ->where('status_pengembalian', 'pending')
                ->first();

            if ($pinjam) {
                $terlambat = Carbon::today()->gt($pinjam->tgl_kembali_rencana);
                $hariTerlambat = $terlambat ? abs(Carbon::today()->diffInDays($pinjam->tgl_kembali_rencana, false)) : 0;
                $denda = $hariTerlambat * 1000;
                $totalDenda += $denda;

                $pinjam->update([
                    'status' => 'dikembalikan',
                    'status_pengembalian' => 'disetujui',
                    'tgl_kembali_aktual' => Carbon::today(),
                    'denda' => $denda,
                    'status_denda' => $denda > 0 ? 'belum_lunas' : 'lunas',
                    'tanggal_pengembalian_approve' => Carbon::now(),
                    'disetujui_oleh' => Auth::id(),
                ]);

                $pinjam->buku->increment('stok_tersedia');
                $successCount++;
            }
        }

        return redirect()->route('admin.pengembalian-approval.index')
            ->with('success', "{$successCount} pengembalian berhasil disetujui. Total denda: Rp " . number_format($totalDenda, 0, ',', '.'));
    }

    /**
     * Export daftar pengembalian ke Excel/PDF
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $status = $request->get('status', 'disetujui');

        $returns = Pinjam::with(['user', 'buku', 'approver'])
            ->where('status_pengembalian', $status)
            ->whereBetween('tanggal_pengembalian_approve', [$startDate, $endDate])
            ->orderBy('tanggal_pengembalian_approve', 'desc')
            ->get();

        // Return view untuk export (bisa diubah ke Excel nanti)
        return view('admin.pengembalian-approval.export', compact('returns', 'startDate', 'endDate', 'status'));
    }

    /**
     * Mencari request pengembalian
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'pending');

        $query = Pinjam::with(['user', 'buku'])
            ->where('status_pengembalian', $status);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_pinjam', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('buku', function ($q) use ($search) {
                        $q->where('judul', 'like', "%{$search}%");
                    });
            });
        }

        $results = $query->orderBy('tgl_kembali_rencana', 'asc')->get();

        if ($request->ajax()) {
            return response()->json($results);
        }

        return redirect()->route('admin.pengembalian-approval.index')
            ->with('search_results', $results);
    }
}