@extends('layouts.app')
@section('title', 'Kelola Buku')
@section('page-title', 'Kelola Buku')
@section('page-subtitle', 'Manajemen data koleksi buku perpustakaan')

@section('content')
{{-- Filter & Tambah --}}
<div class="card mb-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-48">
            <label class="label">Cari Buku</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fa-solid fa-search text-sm"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Judul, pengarang, kode..."
                    class="input pl-9">
            </div>
        </div>

        <div class="w-48">
            <label class="label">Kategori</label>
            <select name="kategori_id" class="input">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $kat)
                    <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn-primary">
            <i class="fa-solid fa-filter"></i> Filter
        </button>

        @if(request('search') || request('kategori_id'))
            <a href="{{ route('admin.buku.index') }}" class="btn-ghost">Reset</a>
        @endif

        <div class="flex-1"></div>

        <a href="{{ route('admin.buku.create') }}" class="btn-accent">
            <i class="fa-solid fa-plus"></i> Tambah Buku
        </a>
    </form>

    {{-- IMPORT --}}
    <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="rounded-2xl border border-sky-100 bg-gradient-to-br from-sky-50 via-white to-cyan-50 p-4 md:p-5">
            <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-sm font-bold text-sky-900 tracking-wide uppercase">Import Data Buku Massal</h3>
                    <p class="text-xs text-sky-700 mt-1">
                        Support file <span class="font-semibold">.xlsx, .xls, .csv</span>
                    </p>
                </div>
                <span class="badge bg-sky-100 text-sky-700">
                    <i class="fa-solid fa-bolt mr-1"></i> Bulk Upload
                </span>
            </div>

            <form action="{{ route('admin.buku.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <label for="file_excel"
                    class="block cursor-pointer rounded-xl border-2 border-dashed border-sky-300 bg-white/90 p-5 text-center hover:bg-sky-50 transition">
                    <i class="fa-solid fa-file-arrow-up text-2xl text-sky-500 mb-2"></i>
                    <p class="text-sm font-semibold text-sky-800">Klik untuk pilih file Excel</p>
                    <p class="text-xs text-sky-600 mt-1">Atau drag and drop file ke area ini</p>
                    <p id="selected-file-name" class="text-xs text-gray-500 mt-2">Belum ada file dipilih</p>
                    <input id="file_excel" type="file" name="file_excel" accept=".xlsx,.xls,.csv,.txt" class="hidden" required>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-xs">
                    <div class="rounded-lg bg-white border border-sky-100 p-2.5 text-gray-600">
                        <span class="font-semibold text-gray-700">Kolom wajib:</span> kode_buku, judul, pengarang, penerbit, tahun_terbit, stok
                    </div>
                    <div class="rounded-lg bg-white border border-sky-100 p-2.5 text-gray-600">
                        <span class="font-semibold text-gray-700">Kategori:</span> gunakan <span class="font-semibold">kategori</span> atau <span class="font-semibold">kategori_id</span>
                    </div>
                    <div class="rounded-lg bg-white border border-sky-100 p-2.5 text-gray-600">
                        <span class="font-semibold text-gray-700">Kolom opsional:</span> isbn, rak, deskripsi
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-file-import"></i> Import Sekarang
                    </button>

                    <a href="/admin/buku/template"
                       class="btn-accent flex items-center gap-2">
                        <i class="fa-solid fa-download"></i>
                        Download Template
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('file_excel');
    const output = document.getElementById('selected-file-name');

    if (!input || !output) return;

    input.addEventListener('change', function () {
        output.textContent = input.files.length
            ? 'File dipilih: ' + input.files[0].name
            : 'Belum ada file dipilih';
    });
});
</script>

{{-- TABEL --}}
<div class="card">
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">
            Menampilkan <span class="font-semibold text-gray-800">{{ $bukus->total() }}</span> buku
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 rounded-xl">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 rounded-l-xl">Kode</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500">Judul Buku</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500">Pengarang</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500">Kategori</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-gray-500">Stok</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-gray-500">Tersedia</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-gray-500 rounded-r-xl">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-50">
                @forelse($bukus as $buku)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3 px-4 font-mono text-xs text-gray-500">{{ $buku->kode_buku }}</td>
                        <td class="py-3 px-4">
                            <p class="font-semibold text-gray-800">{{ $buku->judul }}</p>
                            <p class="text-xs text-gray-400">{{ $buku->penerbit }}, {{ $buku->tahun_terbit }}</p>
                        </td>
                        <td class="py-3 px-4 text-gray-600">{{ $buku->pengarang }}</td>
                        <td class="py-3 px-4">
                            <span class="badge bg-blue-50 text-blue-700">{{ $buku->kategori->nama_kategori }}</span>
                        </td>
                        <td class="py-3 px-4 text-center font-semibold">{{ $buku->stok }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="badge {{ $buku->stok_tersedia > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $buku->stok_tersedia }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-center gap-2">
                                
                                <a href="{{ route('admin.buku.show', $buku) }}" class="btn-primary">
                                    Detail
                                </a>

                                <a href="{{ route('admin.buku.edit', $buku) }}" class="btn-accent">
                                    Edit
                                </a>

                                <form action="{{ route('admin.buku.destroy', $buku) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus buku ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="px-3 py-2 rounded-xl text-sm font-semibold bg-red-500 text-white hover:bg-red-600 transition">
                                        Hapus
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-16 text-gray-400">
                            Tidak ada data buku
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $bukus->links() }}</div>
</div>
@endsection