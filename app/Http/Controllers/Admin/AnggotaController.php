<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'siswa');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('nis', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%");
            });
        }
        if ($request->kelas) {
            $query->where('kelas', $request->kelas);
        }

        $anggotas = $query->latest()->paginate(10)->withQueryString();
        $kelasList = User::where('role', 'siswa')->distinct()->pluck('kelas')->filter()->sort()->values();

        return view('admin.anggota.index', compact('anggotas', 'kelasList'));
    }

    public function create()
    {
        return view('admin.anggota.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|max:100',
            'username' => 'required|max:50|unique:users',
            'email'    => 'required|email|unique:users',
            'nis'      => 'required|max:20|unique:users',
            'kelas'    => 'required|max:20',
            'no_hp'    => 'nullable|max:20',
            'alamat'   => 'nullable',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            ...$validated,
            'role'     => 'siswa',
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.anggota.index')
                         ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function show(User $anggota)
    {
        if ($anggota->role !== 'siswa') {
            abort(404);
        }

        $anggota->load('pinjams.buku');
        return view('admin.anggota.show', compact('anggota'));
    }

    public function edit(User $anggota)
    {
        if ($anggota->role !== 'siswa') {
            abort(404);
        }

        return view('admin.anggota.edit', compact('anggota'));
    }

    public function update(Request $request, User $anggota)
    {
        $validated = $request->validate([
            'name'      => 'required|max:100',
            'username'  => 'required|max:50|unique:users,username,' . $anggota->id,
            'email'     => 'required|email|unique:users,email,' . $anggota->id,
            'nis'       => 'required|max:20|unique:users,nis,' . $anggota->id,
            'kelas'     => 'required|max:20',
            'no_hp'     => 'nullable|max:20',
            'alamat'    => 'nullable',
            'is_active' => 'boolean',
            'password'  => 'nullable|min:6|confirmed',
        ]);

        if ($validated['password'] ?? null) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $anggota->update($validated);

        return redirect()->route('admin.anggota.index')
                         ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(User $anggota)
    {
        if ($anggota->aktivePinjam()->exists()) {
            return back()->with('error', 'Anggota masih memiliki peminjaman aktif.');
        }

        $anggota->delete();
        return redirect()->route('admin.anggota.index')
                         ->with('success', 'Anggota berhasil dihapus.');
    }
}
