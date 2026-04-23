@extends('layouts.app')
@section('title', 'Tambah Anggota')
@section('page-title', 'Tambah Anggota')
@section('page-subtitle', 'Daftarkan siswa baru sebagai anggota perpustakaan')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 text-sm text-red-700">
                <p class="font-semibold mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.anggota.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="label">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="input" placeholder="Nama lengkap siswa" required>
                </div>
                <div>
                    <label class="label">Username *</label>
                    <input type="text" name="username" value="{{ old('username') }}" class="input" placeholder="username login" required>
                </div>
                <div>
                    <label class="label">NIS *</label>
                    <input type="text" name="nis" value="{{ old('nis') }}" class="input" placeholder="Nomor Induk Siswa" required>
                </div>
                <div>
                    <label class="label">Kelas *</label>
                    <input type="text" name="kelas" value="{{ old('kelas') }}" class="input" placeholder="cth: XII RPL 1" required>
                </div>
                <div>
                    <label class="label">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="input" placeholder="08xxxxxxxxxx">
                </div>
                <div class="col-span-2">
                    <label class="label">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="input" required>
                </div>
                <div class="col-span-2">
                    <label class="label">Alamat</label>
                    <textarea name="alamat" rows="2" class="input resize-none" placeholder="Alamat lengkap siswa">{{ old('alamat') }}</textarea>
                </div>
                <div>
                    <label class="label">Password *</label>
                    <input type="password" name="password" class="input" placeholder="Min. 6 karakter" required>
                </div>
                <div>
                    <label class="label">Konfirmasi Password *</label>
                    <input type="password" name="password_confirmation" class="input" placeholder="Ulangi password" required>
                </div>
            </div>

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-user-plus"></i> Tambah Anggota
                </button>
                <a href="{{ route('admin.anggota.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
