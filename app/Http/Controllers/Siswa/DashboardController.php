<?php
// app/Http/Controllers/Siswa/DashboardController.php
namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\{Buku, Pinjam};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        $user       = Auth::user();
        $dipinjam   = Pinjam::with('buku')->where('user_id', $user->id)->where('status', 'dipinjam')->get();
        $terlambat  = $dipinjam->filter->terlambat->count();
        $riwayat    = Pinjam::where('user_id', $user->id)->count();
        $buku_baru  = Buku::with('kategori')->latest()->limit(4)->get();
        
        // ========== TAMBAHKAN: Total Denda Belum Dibayar ==========
        $totalDenda = Pinjam::where('user_id', $user->id)
            ->where('denda', '>', 0)
            ->where('status_denda', 'belum_lunas')
            ->sum('denda');

        return view('siswa.dashboard', compact('dipinjam', 'terlambat', 'riwayat', 'buku_baru', 'totalDenda'));
    }

    public function profil()
    {
        return view('siswa.profil', ['user' => Auth::user()]);
    }

    public function updateProfil(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'     => 'required|max:100',
            'no_hp'    => 'nullable|max:20',
            'alamat'   => 'nullable',
            'password' => 'nullable|min:6|confirmed',
        ]);

        if ($validated['password'] ?? null) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}