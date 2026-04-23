<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Buku, Kategori};
use Illuminate\Http\{Request, JsonResponse};

class BukuApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Buku::with('kategori');
        if ($request->search)      $query->search($request->search);
        if ($request->kategori_id) $query->where('kategori_id', $request->kategori_id);
        if ($request->tersedia)    $query->where('stok_tersedia', '>', 0);

        $bukus = $query->latest()->paginate($request->per_page ?? 12);

        return response()->json([
            'success' => true,
            'data'    => $bukus->items(),
            'meta'    => [
                'total'        => $bukus->total(),
                'per_page'     => $bukus->perPage(),
                'current_page' => $bukus->currentPage(),
                'last_page'    => $bukus->lastPage(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $buku = Buku::with('kategori')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $buku]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kode_buku'    => 'required|unique:bukus|max:30',
            'judul'        => 'required|max:200',
            'pengarang'    => 'required|max:100',
            'penerbit'     => 'required|max:100',
            'tahun_terbit' => 'required|digits:4',
            'kategori_id'  => 'required|exists:kategoris,id',
            'stok'         => 'required|integer|min:1',
            'isbn'         => 'nullable|max:30',
            'deskripsi'    => 'nullable',
            'rak'          => 'nullable|max:20',
        ]);
        $validated['stok_tersedia'] = $validated['stok'];
        $buku = Buku::create($validated);
        return response()->json(['success' => true, 'message' => 'Buku berhasil ditambahkan.', 'data' => $buku], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $buku = Buku::findOrFail($id);
        $validated = $request->validate([
            'judul'        => 'sometimes|max:200',
            'pengarang'    => 'sometimes|max:100',
            'penerbit'     => 'sometimes|max:100',
            'tahun_terbit' => 'sometimes|digits:4',
            'kategori_id'  => 'sometimes|exists:kategoris,id',
            'stok'         => 'sometimes|integer|min:1',
            'isbn'         => 'nullable|max:30',
            'deskripsi'    => 'nullable',
            'rak'          => 'nullable|max:20',
        ]);
        if (isset($validated['stok'])) {
            $selisih = $validated['stok'] - $buku->stok;
            $validated['stok_tersedia'] = max(0, $buku->stok_tersedia + $selisih);
        }
        $buku->update($validated);
        return response()->json(['success' => true, 'message' => 'Buku berhasil diperbarui.', 'data' => $buku]);
    }

    public function destroy(int $id): JsonResponse
    {
        $buku = Buku::findOrFail($id);
        if ($buku->pinjams()->where('status', 'dipinjam')->exists()) {
            return response()->json(['success' => false, 'message' => 'Buku sedang dipinjam, tidak bisa dihapus.'], 422);
        }
        $buku->delete();
        return response()->json(['success' => true, 'message' => 'Buku berhasil dihapus.']);
    }
}
