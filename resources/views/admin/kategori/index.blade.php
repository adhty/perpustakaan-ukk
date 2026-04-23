@extends('layouts.app')
@section('title', 'Kelola Kategori')
@section('page-title', 'Kelola Kategori')
@section('page-subtitle', 'Manajemen kategori / jenis buku perpustakaan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

    {{-- Form Tambah Kategori --}}
    <div class="card lg:col-span-2">
        <h3 class="font-bold text-gray-800 mb-5">➕ Tambah Kategori Baru</h3>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.kategori.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="label">Nama Kategori *</label>
                <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}"
                    class="input" placeholder="cth: Pemrograman, Matematika..." required>
            </div>
            <div>
                <label class="label">Keterangan</label>
                <textarea name="keterangan" rows="3" class="input resize-none"
                    placeholder="Deskripsi singkat kategori ini...">{{ old('keterangan') }}</textarea>
            </div>
            <button type="submit" class="btn-primary w-full justify-center">
                <i class="fa-solid fa-plus"></i> Tambah Kategori
            </button>
        </form>
    </div>

    {{-- Daftar Kategori --}}
    <div class="card lg:col-span-3">
        <h3 class="font-bold text-gray-800 mb-5">📋 Daftar Kategori</h3>

        <div class="space-y-2">
            @forelse($kategoris as $kat)
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition group">
                    <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-tag text-primary text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 text-sm">{{ $kat->nama_kategori }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $kat->keterangan ?? '-' }}</p>
                    </div>
                    <span class="badge bg-blue-100 text-blue-700 flex-shrink-0">
                        {{ $kat->bukus_count }} buku
                    </span>

                    {{-- Tombol Edit (modal inline) --}}
                    <button onclick="openEdit({{ $kat->id }}, '{{ addslashes($kat->nama_kategori) }}', '{{ addslashes($kat->keterangan ?? '') }}')"
                        class="w-7 h-7 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition opacity-0 group-hover:opacity-100">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </button>

                    {{-- Tombol Hapus --}}
                    @if($kat->bukus_count == 0)
                        <form action="{{ route('admin.kategori.destroy', $kat) }}" method="POST"
                              onsubmit="return confirm('Hapus kategori {{ $kat->nama_kategori }}?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-7 h-7 flex items-center justify-center bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition opacity-0 group-hover:opacity-100">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    @else
                        <div class="w-7 h-7 flex items-center justify-center text-gray-300 opacity-0 group-hover:opacity-100" title="Tidak bisa dihapus (ada buku)">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-10 text-gray-400">
                    <i class="fa-solid fa-tags text-3xl mb-2 block opacity-20"></i>
                    <p class="text-sm">Belum ada kategori. Tambahkan di form sebelah kiri.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal Edit Kategori --}}
<div id="modal-edit" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <h3 class="font-bold text-gray-800 text-lg mb-5">✏️ Edit Kategori</h3>
        <form id="form-edit" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="label">Nama Kategori *</label>
                <input type="text" id="edit-nama" name="nama_kategori" class="input" required>
            </div>
            <div>
                <label class="label">Keterangan</label>
                <textarea id="edit-ket" name="keterangan" rows="3" class="input resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 justify-center">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
                <button type="button" onclick="closeEdit()" class="btn-ghost flex-1 justify-center">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEdit(id, nama, ket) {
    document.getElementById('form-edit').action = `/admin/kategori/${id}`;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-ket').value = ket;
    document.getElementById('modal-edit').classList.remove('hidden');
}
function closeEdit() {
    document.getElementById('modal-edit').classList.add('hidden');
}
// Tutup modal jika klik backdrop
document.getElementById('modal-edit').addEventListener('click', function(e) {
    if (e.target === this) closeEdit();
});
</script>
@endpush
