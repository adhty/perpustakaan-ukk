@extends('layouts.app')
@section('title', 'Tambah Buku')
@section('page-title', 'Tambah Buku')
@section('page-subtitle', 'Tambah koleksi buku baru ke perpustakaan')

@section('content')
<div class="max-w-3xl">
    <div class="card">
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 text-sm text-red-700">
                <p class="font-semibold mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.buku.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Kode Buku</label>
                    <input type="text" name="kode_buku" value="{{ $kode_otomatis }}" class="input bg-gray-100 cursor-not-allowed" readonly>
                    <input type="hidden" name="kode_buku" value="{{ $kode_otomatis }}">
                    <p class="text-xs text-gray-400 mt-1">Kode buku akan digenerate otomatis oleh sistem</p>
                </div>
                <div>
                    <label class="label">ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn') }}" class="input" placeholder="978-xxx-xxx-xxx">
                </div>
                <div class="col-span-2">
                    <label class="label">Judul Buku *</label>
                    <input type="text" name="judul" value="{{ old('judul') }}" class="input" placeholder="Judul lengkap buku" required>
                </div>
                <div>
                    <label class="label">Pengarang *</label>
                    <input type="text" name="pengarang" value="{{ old('pengarang') }}" class="input" placeholder="Nama pengarang" required>
                </div>
                <div>
                    <label class="label">Penerbit *</label>
                    <input type="text" name="penerbit" value="{{ old('penerbit') }}" class="input" placeholder="Nama penerbit" required>
                </div>
                <div>
                    <label class="label">Tahun Terbit *</label>
                    <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', date('Y')) }}" class="input" min="1900" max="{{ date('Y') }}" required>
                </div>
                <div>
                    <label class="label">Kategori *</label>
                    <select name="kategori_id" class="input" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Stok *</label>
                    <input type="number" name="stok" value="{{ old('stok', 1) }}" class="input" min="1" required>
                </div>
                <div>
                    <label class="label">Lokasi Rak</label>
                    <input type="text" name="rak" value="{{ old('rak') }}" class="input" placeholder="cth: A1">
                </div>
                <div class="col-span-2">
                    <label class="label">Deskripsi Buku</label>
                    <textarea name="deskripsi" rows="3" class="input resize-none" placeholder="Deskripsi singkat tentang buku...">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="col-span-2">
                    <label class="label">Sampul Buku</label>
                    <input type="file" name="sampul" accept="image/*" class="input py-2">
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, max 2MB</p>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Buku
                </button>
                <a href="{{ route('admin.buku.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection