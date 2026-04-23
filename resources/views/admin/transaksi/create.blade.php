@extends('layouts.app')
@section('title', 'Tambah Transaksi')
@section('page-title', 'Tambah Transaksi Peminjaman')
@section('page-subtitle', 'Buat transaksi peminjaman buku baru')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.transaksi.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="label">Anggota / Peminjam *</label>
                <select name="user_id" class="input" required>
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($anggotas as $a)
                        <option value="{{ $a->id }}" {{ old('user_id') == $a->id ? 'selected' : '' }}>
                            {{ $a->name }} – {{ $a->nis }} ({{ $a->kelas }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Buku *</label>
                <select name="buku_id" class="input" required>
                    <option value="">-- Pilih Buku --</option>
                    @foreach($bukus as $b)
                        <option value="{{ $b->id }}" {{ old('buku_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->judul }} – {{ $b->pengarang }} (Stok: {{ $b->stok_tersedia }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Tanggal Pinjam *</label>
                    <input type="date" name="tgl_pinjam" value="{{ old('tgl_pinjam', date('Y-m-d')) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Batas Pengembalian *</label>
                    <input type="date" name="tgl_kembali_rencana"
                        value="{{ old('tgl_kembali_rencana', date('Y-m-d', strtotime('+7 days'))) }}"
                        class="input" required>
                </div>
            </div>
            <div>
                <label class="label">Keterangan</label>
                <textarea name="keterangan" rows="2" class="input resize-none" placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                <p><i class="fa-solid fa-circle-info mr-1"></i><strong>Ketentuan:</strong> Denda keterlambatan Rp 1.000/hari. Maksimum 3 buku per siswa.</p>
            </div>

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-book-bookmark"></i> Buat Transaksi
                </button>
                <a href="{{ route('admin.transaksi.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
