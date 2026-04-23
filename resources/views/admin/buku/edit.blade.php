@extends('layouts.app')
@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku')
@section('page-subtitle', 'Perbarui informasi data buku')

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

        <form action="{{ route('admin.buku.update', $buku) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Kode Buku *</label>
                    <input type="text" name="kode_buku" value="{{ old('kode_buku', $buku->kode_buku) }}" class="input" required>
                </div>
                <div>
                    <label class="label">ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn', $buku->isbn) }}" class="input">
                </div>
                <div class="col-span-2">
                    <label class="label">Judul Buku *</label>
                    <input type="text" name="judul" value="{{ old('judul', $buku->judul) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Pengarang *</label>
                    <input type="text" name="pengarang" value="{{ old('pengarang', $buku->pengarang) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Penerbit *</label>
                    <input type="text" name="penerbit" value="{{ old('penerbit', $buku->penerbit) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Tahun Terbit *</label>
                    <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Kategori *</label>
                    <select name="kategori_id" class="input" required>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_id', $buku->kategori_id) == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Stok Total *</label>
                    <input type="number" name="stok" value="{{ old('stok', $buku->stok) }}" class="input" min="1" required>
                </div>
                <div>
                    <label class="label">Lokasi Rak</label>
                    <input type="text" name="rak" value="{{ old('rak', $buku->rak) }}" class="input">
                </div>
                <div class="col-span-2">
                    <label class="label">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="input resize-none">{{ old('deskripsi', $buku->deskripsi) }}</textarea>
                </div>
                <div class="col-span-2">
                    <label class="label">Sampul Buku</label>
                    @if($buku->sampul)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $buku->sampul) }}" class="h-24 rounded-lg object-cover border border-gray-200">
                            <p class="text-xs text-gray-400 mt-1">Sampul saat ini. Upload baru untuk mengganti.</p>
                        </div>
                    @endif
                    <input type="file" name="sampul" accept="image/*" class="input py-2">
                </div>
            </div>
            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Perbarui
                </button>
                <a href="{{ route('admin.buku.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
