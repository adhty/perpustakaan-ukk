<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Pinjam, Buku, User};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

// ── Peminjaman ────────────────────────────────────────────
class PeminjamanApiController extends Controller
{
    // Aktif pinjaman siswa
    public function aktif(Request $request): JsonResponse
    {
        $pinjams = Pinjam::with('buku:id,judul,pengarang,kode_buku,rak')
            ->where('user_id', $request->user()->id)
            ->where('status', 'dipinjam')
            ->latest()->get()
            ->map(fn($p) => [...$p->toArray(),
                'terlambat'    => $p->terlambat,
                'sisa_hari'    => $p->sisa_hari,
                'denda_hitung' => $p->denda_hitung,
            ]);

        return response()->json(['success' => true, 'data' => $pinjams]);
    }

    // Semua peminjaman (riwayat) siswa
    public function riwayat(Request $request): JsonResponse
    {
        $pinjams = Pinjam::with('buku:id,judul,pengarang')
            ->where('user_id', $request->user()->id)
            ->latest()->paginate(10);

        return response()->json(['success' => true,
            'data' => $pinjams->items(),
            'meta' => ['total' => $pinjams->total(), 'last_page' => $pinjams->lastPage()],
        ]);
    }

    // Index alias
    public function index(Request $request): JsonResponse { return $this->aktif($request); }

    // Pinjam buku (siswa)
    public function store(Request $request): JsonResponse
    {
        $request->validate(['buku_id' => 'required|exists:bukus,id']);

        $user = $request->user();
        $buku = Buku::findOrFail($request->buku_id);

        if ($buku->stok_tersedia < 1)
            return response()->json(['success'=>false,'message'=>'Stok buku tidak tersedia.'], 422);

        if (Pinjam::where('user_id',$user->id)->where('buku_id',$buku->id)->where('status','dipinjam')->exists())
            return response()->json(['success'=>false,'message'=>'Anda sudah meminjam buku ini.'], 422);

        if (Pinjam::where('user_id',$user->id)->where('status','dipinjam')->count() >= 3)
            return response()->json(['success'=>false,'message'=>'Maksimal 3 buku aktif.'], 422);

        $tglPinjam  = Carbon::today();
        $tglKembali = $tglPinjam->copy()->addDays(7);

        $pinjam = Pinjam::create([
            'kode_pinjam'         => Pinjam::generateKode(),
            'user_id'             => $user->id,
            'buku_id'             => $buku->id,
            'tgl_pinjam'          => $tglPinjam,
            'tgl_kembali_rencana' => $tglKembali,
            'status'              => 'dipinjam',
        ]);
        $buku->decrement('stok_tersedia');

        return response()->json(['success'=>true,
            'message' => 'Berhasil meminjam buku. Kembalikan sebelum '.$tglKembali->isoFormat('D MMMM Y'),
            'data'    => $pinjam->load('buku:id,judul'),
        ], 201);
    }

    // Kembalikan (siswa)
    public function kembalikan(Request $request, int $id): JsonResponse
    {
        $pinjam = Pinjam::where('user_id',$request->user()->id)->findOrFail($id);

        if ($pinjam->status !== 'dipinjam')
            return response()->json(['success'=>false,'message'=>'Buku sudah dikembalikan.'], 422);

        $tgl   = Carbon::today();
        $denda = $tgl->gt($pinjam->tgl_kembali_rencana)
            ? $tgl->diffInDays($pinjam->tgl_kembali_rencana) * 1000 : 0;

        $pinjam->update(['status'=>'dikembalikan','tgl_kembali_aktual'=>$tgl,'denda'=>$denda]);
        $pinjam->buku->increment('stok_tersedia');

        return response()->json(['success'=>true,
            'message' => 'Buku berhasil dikembalikan.' . ($denda > 0 ? ' Denda: Rp '.number_format($denda,0,',','.') : ''),
            'data'    => ['denda' => $denda],
        ]);
    }

    // Admin: semua transaksi
    public function adminIndex(Request $request): JsonResponse
    {
        $query = Pinjam::with(['user:id,name,kelas','buku:id,judul,kode_buku']);
        if ($request->status) $query->where('status',$request->status);
        if ($request->search) $query->whereHas('user',fn($q)=>$q->where('name','like',"%{$request->search}%"));

        $pinjams = $query->latest()->paginate(15);
        return response()->json(['success'=>true,'data'=>$pinjams->items(),
            'meta'=>['total'=>$pinjams->total(),'last_page'=>$pinjams->lastPage()]]);
    }

    // Admin: kembalikan
    public function adminKembalikan(Request $request, int $id): JsonResponse
    {
        $pinjam = Pinjam::findOrFail($id);
        if ($pinjam->status !== 'dipinjam')
            return response()->json(['success'=>false,'message'=>'Sudah dikembalikan.'], 422);

        $tgl   = Carbon::today();
        $denda = $tgl->gt($pinjam->tgl_kembali_rencana)
            ? $tgl->diffInDays($pinjam->tgl_kembali_rencana) * 1000 : 0;

        $pinjam->update(['status'=>'dikembalikan','tgl_kembali_aktual'=>$tgl,'denda'=>$denda,'admin_id'=>$request->user()->id]);
        $pinjam->buku->increment('stok_tersedia');

        return response()->json(['success'=>true,'message'=>'Berhasil.','data'=>['denda'=>$denda]]);
    }
}
