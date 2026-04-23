<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Buku, Kategori, Pinjam};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class BukuController extends Controller
{
    /**
     * =========================
     * IMPORT EXCEL
     * =========================
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        $created = 0;
        $updated = 0;

        try {
            $spreadsheet = IOFactory::load($request->file('file_excel')->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            if (count($rows) < 2) {
                return back()->with('error', 'File kosong atau tidak memiliki data.');
            }

            $headers = array_map(
                fn($h) => strtolower(trim((string) $h)),
                array_values($rows[0] ?? [])
            );

            foreach (array_slice($rows, 1) as $row) {
                $data = [];
                foreach ($headers as $index => $key) {
                    $data[$key] = $row[$index] ?? null;
                }

                $this->upsertBukuFromImportRow($data, $created, $updated);
            }

        } catch (Throwable $e) {
            return back()->with('error', 'Import gagal. Pastikan format benar.');
        }

        return redirect()->route('admin.buku.index')->with(
            'success',
            "Import selesai: {$created} baru, {$updated} diperbarui"
        );
    }

    /**
     * =========================
     * DOWNLOAD TEMPLATE (FIX)
     * =========================
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ✅ HEADER SAJA (TANPA CONTOH DATA)
        $headers = [
            'kode_buku',
            'judul',
            'pengarang',
            'penerbit',
            'tahun_terbit',
            'stok',
            'kategori',
            'isbn',
            'rak',
            'deskripsi'
        ];

        $sheet->fromArray([$headers], null, 'A1');

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_buku.xlsx');
    }

    /**
     * =========================
     * LOGIC IMPORT
     * =========================
     */
    private function upsertBukuFromImportRow(array $data, int &$created, int &$updated): void
    {
        $kodeBuku = trim((string) ($data['kode_buku'] ?? ''));
        $judul = trim((string) ($data['judul'] ?? ''));

        if ($kodeBuku === '' || $judul === '') return;

        $kategoriId = null;

        if (!empty($data['kategori_id'])) {
            $kategori = Kategori::find((int) $data['kategori_id']);
            $kategoriId = $kategori?->id;
        }

        if (!$kategoriId) {
            $namaKategori = trim((string) ($data['kategori'] ?? ''));
            if ($namaKategori !== '') {
                $kategori = Kategori::firstOrCreate(
                    ['nama_kategori' => $namaKategori],
                    ['keterangan' => 'Import Excel']
                );
                $kategoriId = $kategori->id;
            }
        }

        if (!$kategoriId) return;

        $stok = max((int) ($data['stok'] ?? 1), 1);

        $payload = [
            'judul' => $judul,
            'pengarang' => trim((string) ($data['pengarang'] ?? 'Tidak diketahui')),
            'penerbit' => trim((string) ($data['penerbit'] ?? 'Tidak diketahui')),
            'tahun_terbit' => (int) ($data['tahun_terbit'] ?? date('Y')),
            'kategori_id' => $kategoriId,
            'stok' => $stok,
            'isbn' => trim((string) ($data['isbn'] ?? '')) ?: null,
            'rak' => trim((string) ($data['rak'] ?? '')) ?: null,
            'deskripsi' => trim((string) ($data['deskripsi'] ?? '')) ?: null,
        ];

        $buku = Buku::where('kode_buku', $kodeBuku)->first();

        if ($buku) {
            $selisih = $stok - $buku->stok;
            $payload['stok_tersedia'] = max(0, $buku->stok_tersedia + $selisih);
            $buku->update($payload);
            $updated++;
            return;
        }

        $payload['kode_buku'] = $kodeBuku;
        $payload['stok_tersedia'] = $stok;

        Buku::create($payload);
        $created++;
    }

    /**
     * =========================
     * CRUD
     * =========================
     */
    public function index(Request $request)
    {
        $query = Buku::with('kategori');

        if ($request->search) {
            $query->search($request->search);
        }

        if ($request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $bukus = $query->latest()->paginate(10)->withQueryString();
        $kategoris = Kategori::all();

        // Variabel untuk view (jika diperlukan)
        $dipinjamIds = []; // Default array kosong untuk admin
        $activePinjamCount = 0; // Default 0 untuk admin
        $hasUnpaidDenda = false; // Default false untuk admin

        return view('admin.buku.index', compact('bukus', 'kategoris', 'dipinjamIds', 'activePinjamCount', 'hasUnpaidDenda'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        
        // Generate kode buku otomatis
        $lastBuku = Buku::orderBy('id', 'desc')->first();
        if ($lastBuku && $lastBuku->kode_buku) {
            $lastKode = $lastBuku->kode_buku;
            // Ambil angka dari kode (misal BK001 -> 1)
            $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastKode);
            $newNumber = $lastNumber + 1;
            $kode_otomatis = 'BK' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        } else {
            $kode_otomatis = 'BK001';
        }
        
        return view('admin.buku.create', compact('kategoris', 'kode_otomatis'));
    }

    public function store(Request $request)
    {
        // Generate kode buku otomatis jika tidak dikirim dari form
        $kodeBuku = $request->input('kode_buku');
        
        if (empty($kodeBuku)) {
            $lastBuku = Buku::orderBy('id', 'desc')->first();
            if ($lastBuku && $lastBuku->kode_buku) {
                $lastKode = $lastBuku->kode_buku;
                $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastKode);
                $newNumber = $lastNumber + 1;
                $kodeBuku = 'BK' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            } else {
                $kodeBuku = 'BK001';
            }
        }
        
        $validated = $request->validate([
            'kode_buku'    => 'required|unique:bukus|max:30',
            'judul'        => 'required|max:200',
            'pengarang'    => 'required|max:100',
            'penerbit'     => 'required|max:100',
            'tahun_terbit' => 'required|digits:4|integer',
            'kategori_id'  => 'required|exists:kategoris,id',
            'stok'         => 'required|integer|min:1',
            'isbn'         => 'nullable|max:30',
            'deskripsi'    => 'nullable',
            'rak'          => 'nullable|max:20',
            'sampul'       => 'nullable|image|max:2048',
        ]);

        $validated['kode_buku'] = $kodeBuku;
        $validated['stok_tersedia'] = $validated['stok'];

        if ($request->hasFile('sampul')) {
            $validated['sampul'] = $request->file('sampul')->store('sampul', 'public');
        }

        Buku::create($validated);

        return redirect()->route('admin.buku.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show(Buku $buku)
    {
        $buku->load(['kategori', 'pinjams.user']);
        return view('admin.buku.show', compact('buku'));
    }

    public function edit(Buku $buku)
    {
        $kategoris = Kategori::all();
        return view('admin.buku.edit', compact('buku', 'kategoris'));
    }

    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'kode_buku'    => 'required|max:30|unique:bukus,kode_buku,' . $buku->id,
            'judul'        => 'required|max:200',
            'pengarang'    => 'required|max:100',
            'penerbit'     => 'required|max:100',
            'tahun_terbit' => 'required|digits:4|integer',
            'kategori_id'  => 'required|exists:kategoris,id',
            'stok'         => 'required|integer|min:1',
            'isbn'         => 'nullable|max:30',
            'deskripsi'    => 'nullable',
            'rak'          => 'nullable|max:20',
            'sampul'       => 'nullable|image|max:2048',
        ]);

        $selisih = $validated['stok'] - $buku->stok;
        $validated['stok_tersedia'] = max(0, $buku->stok_tersedia + $selisih);

        if ($request->hasFile('sampul')) {
            if ($buku->sampul) Storage::disk('public')->delete($buku->sampul);
            $validated['sampul'] = $request->file('sampul')->store('sampul', 'public');
        }

        $buku->update($validated);

        return redirect()->route('admin.buku.index')
            ->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Buku $buku)
    {
        if ($buku->pinjams()->where('status', 'dipinjam')->exists()) {
            return back()->with('error', 'Buku sedang dipinjam.');
        }

        if ($buku->sampul) {
            Storage::disk('public')->delete($buku->sampul);
        }

        $buku->delete();

        return redirect()->route('admin.buku.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}