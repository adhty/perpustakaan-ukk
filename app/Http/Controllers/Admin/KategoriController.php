<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

/**
 * KategoriController
 * Mengelola data kategori buku (diakses dari halaman kelola buku)
 */
class KategoriController extends Controller
{
    /**
     * Tampilkan semua kategori
     */
    public function index()
    {
        $kategoris = Kategori::withCount('bukus')->latest()->get();
        return view('admin.kategori.index', compact('kategoris'));
    }

    /**
     * Simpan kategori baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategoris',
            'keterangan'    => 'nullable|string',
        ], [
            'nama_kategori.unique' => 'Nama kategori sudah ada.',
        ]);

        Kategori::create($validated);

        return redirect()->route('admin.buku.index')
                         ->with('success', 'Kategori "' . $validated['nama_kategori'] . '" berhasil ditambahkan.');
    }

    /**
     * Perbarui kategori
     */
    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategoris,nama_kategori,' . $kategori->id,
            'keterangan'    => 'nullable|string',
        ]);

        $kategori->update($validated);

        return redirect()->route('admin.buku.index')
                         ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori (hanya jika tidak ada buku)
     */
    public function destroy(Kategori $kategori)
    {
        if ($kategori->bukus()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki buku.');
        }

        $kategori->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
