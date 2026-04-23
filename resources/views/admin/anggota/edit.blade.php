@extends('layouts.app')
@section('title', 'Edit Anggota')
@section('page-title', 'Edit Anggota')
@section('page-subtitle', 'Perbarui data anggota perpustakaan')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.anggota.update', $anggota) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="label">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name', $anggota->name) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Username *</label>
                    <input type="text" name="username" value="{{ old('username', $anggota->username) }}" class="input" required>
                </div>
                <div>
                    <label class="label">NIS *</label>
                    <input type="text" name="nis" value="{{ old('nis', $anggota->nis) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Kelas *</label>
                    <input type="text" name="kelas" value="{{ old('kelas', $anggota->kelas) }}" class="input" required>
                </div>
                <div>
                    <label class="label">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $anggota->no_hp) }}" class="input">
                </div>
                <div class="col-span-2">
                    <label class="label">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $anggota->email) }}" class="input" required>
                </div>
                <div class="col-span-2">
                    <label class="label">Alamat</label>
                    <textarea name="alamat" rows="2" class="input resize-none">{{ old('alamat', $anggota->alamat) }}</textarea>
                </div>
                <div>
                    <label class="label">Password Baru</label>
                    <input type="password" name="password" class="input" placeholder="Kosongkan jika tidak diubah">
                </div>
                <div>
                    <label class="label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="input" placeholder="Konfirmasi password baru">
                </div>
                <div class="col-span-2 flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="w-4 h-4 rounded"
                        {{ old('is_active', $anggota->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-semibold text-gray-700">Akun Aktif</label>
                    <p class="text-xs text-gray-400 ml-auto">Nonaktifkan untuk memblokir akses login</p>
                </div>
            </div>

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Perbarui
                </button>
                <a href="{{ route('admin.anggota.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
