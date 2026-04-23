<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{User, Buku, Pinjam};
use Illuminate\Http\{Request, JsonResponse};
use Carbon\Carbon;

class DashboardApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $terlambat = Pinjam::where('status','dipinjam')
                ->where('tgl_kembali_rencana','<',Carbon::today())->count();

            return response()->json(['success' => true, 'data' => [
                'total_buku'    => Buku::count(),
                'total_anggota' => User::where('role','siswa')->count(),
                'dipinjam'      => Pinjam::where('status','dipinjam')->count(),
                'terlambat'     => $terlambat,
                'transaksi_terbaru' => Pinjam::with(['user:id,name,kelas','buku:id,judul'])
                    ->latest()->limit(5)->get(),
            ]]);
        }

        // Siswa
        $aktif = Pinjam::with('buku:id,judul,pengarang,kode_buku')
            ->where('user_id', $user->id)->where('status','dipinjam')->get()
            ->map(fn($p) => [
                ...$p->toArray(),
                'terlambat'  => $p->terlambat,
                'sisa_hari'  => $p->sisa_hari,
                'denda_hitung' => $p->denda_hitung,
            ]);

        return response()->json(['success' => true, 'data' => [
            'nama'          => $user->name,
            'kelas'         => $user->kelas,
            'aktif_pinjam'  => $aktif->count(),
            'total_pinjam'  => Pinjam::where('user_id',$user->id)->count(),
            'terlambat'     => $aktif->where('terlambat', true)->count(),
            'pinjaman_aktif'=> $aktif,
        ]]);
    }
}
