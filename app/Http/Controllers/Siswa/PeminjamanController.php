<?php
namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\{Buku, Pinjam, Kategori};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    const MAX_PINJAM = 3;

    public function index(Request $request)
    {
        $query = Buku::with('kategori')->where('stok_tersedia', '>', 0);

        if ($request->search) {
            $query->search($request->search);
        }
        if ($request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $bukus      = $query->paginate(9)->withQueryString();
        $kategoris  = Kategori::all();

        // Buku yang sedang dalam proses (menunggu atau dipinjam)
        $dipinjamIds = Pinjam::where('user_id', Auth::id())
                     ->whereIn('status', ['menunggu', 'dipinjam'])
                     ->pluck('buku_id')
                     ->toArray();

        // Hitung jumlah peminjaman aktif (menunggu + dipinjam)
        $activePinjamCount = Pinjam::where('user_id', Auth::id())
            ->whereIn('status', ['menunggu', 'dipinjam'])
            ->count();

        $remainingQuota = self::MAX_PINJAM - $activePinjamCount;

        // CEK: Apakah user memiliki denda yang BELUM LUNAS (belum dibayar)
        $hasUnpaidDenda = Pinjam::where('user_id', Auth::id())
            ->where('denda', '>', 0)
            ->where('status_denda', 'belum_lunas')
            ->exists();

        // CEK: Apakah user memiliki denda yang MENUNGGU KONFIRMASI ADMIN
        $hasPendingDenda = Pinjam::where('user_id', Auth::id())
            ->where('denda', '>', 0)
            ->where('status_denda', 'menunggu')
            ->exists();

        return view('siswa.peminjaman.index', compact('bukus', 'kategoris', 'dipinjamIds', 'activePinjamCount', 'remainingQuota', 'hasUnpaidDenda', 'hasPendingDenda'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'buku_id' => 'required|exists:bukus,id',
            'tgl_pinjam' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addDays(7)->format('Y-m-d'),
        ]);

        $buku = Buku::findOrFail($validated['buku_id']);

        // CEK 1: Stok
        if ($buku->stok_tersedia < 1) {
            return back()->with('error', 'Stok buku "' . $buku->judul . '" tidak tersedia.');
        }

        // CEK 2: Sudah request/pinjam buku yang sama
        $already = Pinjam::where('user_id', Auth::id())
                         ->where('buku_id', $buku->id)
                         ->whereIn('status', ['menunggu', 'dipinjam'])
                         ->exists();

        if ($already) {
            return back()->with('error', 'Anda sudah mengajukan/meminjam buku "' . $buku->judul . '".');
        }

        // CEK 3: Batas maksimum
        $jumlahAktif = Pinjam::where('user_id', Auth::id())
            ->whereIn('status', ['menunggu', 'dipinjam'])
            ->count();
            
        if ($jumlahAktif >= self::MAX_PINJAM) {
            return back()->with('error', 'Anda sudah memiliki ' . $jumlahAktif . ' request/pinjaman aktif. Maksimal ' . self::MAX_PINJAM . ' buku.');
        }

        // CEK 4: Denda belum lunas (belum dibayar)
        $hasUnpaidDenda = Pinjam::where('user_id', Auth::id())
            ->where('denda', '>', 0)
            ->where('status_denda', 'belum_lunas')
            ->exists();
            
        if ($hasUnpaidDenda) {
            return back()->with('error', 'Anda masih memiliki denda yang belum dibayar. Harap lunasi denda terlebih dahulu.');
        }

        // CEK 5: Denda menunggu konfirmasi admin
        $hasPendingDenda = Pinjam::where('user_id', Auth::id())
            ->where('denda', '>', 0)
            ->where('status_denda', 'menunggu')
            ->exists();
            
        if ($hasPendingDenda) {
            return back()->with('error', 'Anda masih memiliki pembayaran denda yang menunggu konfirmasi admin. Harap tunggu konfirmasi terlebih dahulu.');
        }

        $tglPinjam  = Carbon::parse($validated['tgl_pinjam']);
        $tglKembali = $tglPinjam->copy()->addDays(7);

        // Buat transaksi dengan status MENUNGGU
        $pinjam = Pinjam::create([
            'kode_pinjam'          => Pinjam::generateKode(),
            'user_id'              => Auth::id(),
            'buku_id'              => $buku->id,
            'tgl_pinjam'           => $tglPinjam,
            'tgl_kembali_rencana'  => $tglKembali,
            'status'               => 'menunggu',
            'keterangan'           => 'Menunggu persetujuan admin',
        ]);

        // Kurangi stok
        $buku->decrement('stok_tersedia');

        return redirect()->route('siswa.peminjaman.riwayat')
            ->with('success', "Request peminjaman \"{$buku->judul}\" berhasil dikirim. Menunggu persetujuan admin.");
    }

    public function riwayat()
    {
        $riwayat = Pinjam::with('buku', 'admin')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
            
        $stats = [
            'total' => Pinjam::where('user_id', Auth::id())->count(),
            'menunggu' => Pinjam::where('user_id', Auth::id())->where('status', 'menunggu')->count(),
            'dipinjam' => Pinjam::where('user_id', Auth::id())->where('status', 'dipinjam')->count(),
            'dikembalikan' => Pinjam::where('user_id', Auth::id())->where('status', 'dikembalikan')->count(),
            'ditolak' => Pinjam::where('user_id', Auth::id())->where('status', 'ditolak')->count(),
            'total_denda' => Pinjam::where('user_id', Auth::id())->sum('denda'),
            'denda_belum_lunas' => Pinjam::where('user_id', Auth::id())->where('denda', '>', 0)->where('status_denda', 'belum_lunas')->sum('denda'),
            'denda_menunggu' => Pinjam::where('user_id', Auth::id())->where('denda', '>', 0)->where('status_denda', 'menunggu')->sum('denda'),
        ];
        
        return view('siswa.peminjaman.riwayat', compact('riwayat', 'stats'));
    }
    
    public function cancel($id)
    {
        $pinjam = Pinjam::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'menunggu')
            ->firstOrFail();
            
        $buku = $pinjam->buku;
        $buku->increment('stok_tersedia');
        
        $pinjam->update([
            'status' => 'ditolak',
            'keterangan' => 'Dibatalkan oleh peminjam',
        ]);
        
        return redirect()->route('siswa.peminjaman.riwayat')
            ->with('success', 'Request peminjaman berhasil dibatalkan.');
    }
    
    public function show($id)
    {
        $pinjam = Pinjam::with(['buku', 'admin'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
            
        return view('siswa.peminjaman.detail', compact('pinjam'));
    }
}