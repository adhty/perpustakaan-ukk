<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Buku, Pinjam};
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_buku'     => Buku::count(),
            'total_anggota'  => User::where('role', 'siswa')->count(),
            'dipinjam'       => Pinjam::where('status', 'dipinjam')->count(),
            'terlambat'      => Pinjam::where('status', 'dipinjam')
                                      ->where('tgl_kembali_rencana', '<', Carbon::today())
                                      ->count(),
            'total_denda'    => Pinjam::where('denda', '>', 0)
                                    ->where('status_denda', 'belum_lunas')
                                    ->sum('denda'),
        ];

        $pinjam_terbaru = Pinjam::with(['user', 'buku'])
            ->latest()
            ->limit(8)
            ->get();

        $buku_populer = Buku::withCount('pinjams')
            ->orderByDesc('pinjams_count')
            ->limit(5)
            ->get();

        // Data grafik 6 bulan terakhir
        $grafik = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            $grafik[] = [
                'bulan'  => $bulan->isoFormat('MMM YYYY'),
                'jumlah' => Pinjam::whereYear('tgl_pinjam', $bulan->year)
                                  ->whereMonth('tgl_pinjam', $bulan->month)
                                  ->count(),
            ];
        }

        return view('admin.dashboard', compact('stats', 'pinjam_terbaru', 'buku_populer', 'grafik'));
    }
}