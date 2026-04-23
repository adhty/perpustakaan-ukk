<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Pinjam, Buku, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pinjam::with(['user', 'buku']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('user', fn($qq) => $qq->where('name', 'like', "%{$request->search}%"))
                    ->orWhereHas('buku', fn($qq) => $qq->where('judul', 'like', "%{$request->search}%"))
                    ->orWhere('kode_pinjam', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tgl_pinjam', $request->tanggal);
        }

        if ($request->filled('bulan')) {
            [$tahun, $bulan] = explode('-', $request->bulan);
            $query->whereYear('tgl_pinjam', $tahun)->whereMonth('tgl_pinjam', $bulan);
        }

        $transaksis = $query->latest()->paginate(10)->withQueryString();

        return view('admin.transaksi.index', compact('transaksis'));
    }

    public function create()
    {
        $anggotas = User::where('role', 'siswa')->where('is_active', true)->get();
        $bukus    = Buku::where('stok_tersedia', '>', 0)->get();
        return view('admin.transaksi.create', compact('anggotas', 'bukus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'              => 'required|exists:users,id',
            'buku_id'              => 'required|exists:bukus,id',
            'tgl_pinjam'           => 'required|date',
            'tgl_kembali_rencana'  => 'required|date|after:tgl_pinjam',
            'keterangan'           => 'nullable',
        ]);

        $buku = Buku::findOrFail($validated['buku_id']);

        if ($buku->stok_tersedia < 1) {
            return back()->with('error', 'Stok buku tidak tersedia.')->withInput();
        }

        // Cek apakah siswa sudah meminjam/menunggu buku yang sama
        $already = Pinjam::where('user_id', $validated['user_id'])
                          ->where('buku_id', $validated['buku_id'])
                          ->whereIn('status', ['menunggu', 'dipinjam'])
                          ->exists();

        if ($already) {
            return back()->with('error', 'Siswa ini sudah mengajukan/meminjam buku tersebut.')->withInput();
        }

        // Cek batas maksimal peminjaman (3 buku)
        $activeCount = Pinjam::where('user_id', $validated['user_id'])
            ->whereIn('status', ['menunggu', 'dipinjam'])
            ->count();
            
        if ($activeCount >= 3) {
            return back()->with('error', 'Siswa ini sudah memiliki 3 request/pinjaman aktif.')->withInput();
        }

        $validated['kode_pinjam'] = Pinjam::generateKode();
        $validated['status']      = 'menunggu'; // Langsung menunggu, bukan dipinjam
        $validated['admin_id']    = Auth::id();

        Pinjam::create($validated);
        $buku->decrement('stok_tersedia');

        return redirect()->route('admin.transaksi.index')
                         ->with('success', 'Request peminjaman berhasil ditambahkan dan menunggu persetujuan.');
    }

    /**
     * Approve request peminjaman (ubah status dari menunggu menjadi dipinjam)
     */
    public function approve(int $id)
    {
        $pinjam = Pinjam::with('buku')->findOrFail($id);

        // Cek apakah status masih menunggu
        if ($pinjam->status !== 'menunggu') {
            return back()->with('error', 'Hanya request yang menunggu yang dapat disetujui.');
        }

        // Cek stok buku
        if ($pinjam->buku->stok_tersedia < 1) {
            return back()->with('error', 'Stok buku tidak tersedia untuk disetujui.');
        }

        // Update status menjadi dipinjam
        $pinjam->update([
            'status' => 'dipinjam',
            'admin_id' => Auth::id(),
            'keterangan' => $pinjam->keterangan . ' | Disetujui oleh admin pada ' . Carbon::now()->format('d/m/Y H:i'),
        ]);

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Request peminjaman berhasil disetujui. Buku telah dipinjamkan.');
    }

    /**
     * Reject request peminjaman (ubah status dari menunggu menjadi ditolak)
     */
    public function reject(Request $request, int $id)
    {
        $pinjam = Pinjam::with('buku')->findOrFail($id);

        // Cek apakah status masih menunggu
        if ($pinjam->status !== 'menunggu') {
            return back()->with('error', 'Hanya request yang menunggu yang dapat ditolak.');
        }

        // Kembalikan stok buku
        $pinjam->buku->increment('stok_tersedia');

        // Update status menjadi ditolak
        $pinjam->update([
            'status' => 'ditolak',
            'admin_id' => Auth::id(),
            'keterangan' => $request->keterangan ?: 'Ditolak oleh admin',
        ]);

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Request peminjaman berhasil ditolak.');
    }

    /**
     * Proses pengembalian buku (ubah status dari dipinjam menjadi dikembalikan)
     */
    public function kembalikan(Request $request, int $id)
    {
        $pinjam = Pinjam::with('buku')->findOrFail($id);

        // Cek apakah status sedang dipinjam
        if ($pinjam->status !== 'dipinjam') {
            return back()->with('error', 'Hanya buku yang sedang dipinjam yang dapat dikembalikan.');
        }

        $tglKembali = Carbon::today();
        $dueDate = Carbon::parse($pinjam->tgl_kembali_rencana);
        
        // Hitung denda keterlambatan
        $daysLate = $tglKembali->diffInDays($dueDate, false);
        $denda = 0;
        
        if ($daysLate > 7) {
            $lateDays = $daysLate - 7;
            $denda = $lateDays * 1000;
        }

        // Kembalikan stok buku
        $pinjam->buku->increment('stok_tersedia');

        // Update status menjadi dikembalikan
        $pinjam->update([
            'status' => 'dikembalikan',
            'tgl_kembali_aktual' => $tglKembali,
            'denda' => $denda,
            'admin_id' => Auth::id(),
            'keterangan' => ($pinjam->keterangan ? $pinjam->keterangan . ' | ' : '') . 'Dikembalikan pada ' . $tglKembali->format('d/m/Y'),
        ]);

        $message = 'Buku berhasil dikembalikan.';
        if ($denda > 0) {
            $message .= ' Denda keterlambatan: Rp ' . number_format($denda, 0, ',', '.');
        }

        return redirect()->route('admin.transaksi.index')->with('success', $message);
    }

    public function show(Pinjam $transaksi)
    {
        $transaksi->load(['user', 'buku.kategori', 'admin']);
        return view('admin.transaksi.show', compact('transaksi'));
    }

    public function edit(Pinjam $transaksi)
    {
        $transaksi->load(['user', 'buku']);
        return view('admin.transaksi.edit', compact('transaksi'));
    }

    public function update(Request $request, Pinjam $transaksi)
    {
        // Validasi hanya untuk status hilang dan rusak
        $validated = $request->validate([
            'status' => 'required|in:hilang,rusak',
            'keterangan' => 'nullable|string',
        ]);

        $statusLama = $transaksi->status;
        $statusBaru = $validated['status'];
        
        // Tentukan denda otomatis berdasarkan status
        $denda = $statusBaru === 'hilang' ? 40000 : 20000;
        
        $updateData = [
            'status' => $statusBaru,
            'denda' => $denda,
            'status_denda' => 'belum_lunas',
            'keterangan' => $validated['keterangan'] ?? $transaksi->keterangan,
            'admin_id' => Auth::id(),
            'tgl_kembali_aktual' => Carbon::today(),
        ];
        
        // Manajemen stok
        // Jika status lama dipinjam dan status baru hilang/rusak, stok bertambah
        if ($statusLama === 'dipinjam' && in_array($statusBaru, ['hilang', 'rusak'])) {
            $transaksi->buku->increment('stok_tersedia');
        }
        
        // Jika status baru hilang, kurangi stok (buku dianggap hilang dari sistem)
        if ($statusBaru === 'hilang' && $statusLama !== 'hilang') {
            $transaksi->buku->decrement('stok_tersedia');
        }
        
        $transaksi->update($updateData);
        
        $message = 'Transaksi berhasil diperbarui. Denda: Rp ' . number_format($denda, 0, ',', '.');
        
        return redirect()->route('admin.transaksi.index')->with('success', $message);
    }

    public function destroy(Pinjam $transaksi)
    {
        // Jika status dipinjam, kembalikan stok dulu
        if ($transaksi->status === 'dipinjam') {
            $transaksi->buku->increment('stok_tersedia');
        }
        
        // Jika status menunggu, kembalikan stok
        if ($transaksi->status === 'menunggu') {
            $transaksi->buku->increment('stok_tersedia');
        }
        
        $transaksi->delete();

        return redirect()->route('admin.transaksi.index')
                         ->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Lunasi denda
     */
    public function lunasiDenda(Request $request, $id)
    {
        $pinjam = Pinjam::findOrFail($id);
        
        $pinjam->update([
            'status_denda' => 'lunas'
        ]);
        
        return redirect()->back()->with('success', 'Denda berhasil dilunasi.');
    }

    /**
     * Laporan transaksi
     */
    public function laporan(Request $request)
    {
        $query = Pinjam::with(['user', 'buku']);

        if ($request->tanggal) {
            $query->whereDate('tgl_pinjam', $request->tanggal);
        }

        if ($request->bulan) {
            [$tahun, $bulan] = explode('-', $request->bulan);
            $query->whereYear('tgl_pinjam', $tahun)->whereMonth('tgl_pinjam', $bulan);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $transaksis   = $query->latest()->get();
        $total_denda  = $transaksis->sum('denda');

        return view('admin.transaksi.laporan', compact('transaksis', 'total_denda'));
    }
}