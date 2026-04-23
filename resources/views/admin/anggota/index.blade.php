@extends('layouts.app')
@section('title', 'Kelola Anggota')
@section('page-title', 'Kelola Anggota')
@section('page-subtitle', 'Manajemen data siswa/anggota perpustakaan')

@section('content')
<div class="card mb-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-48">
            <label class="label">Cari Anggota</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fa-solid fa-search text-sm"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, NIS, username..." class="input pl-9">
            </div>
        </div>
        <div class="w-44">
            <label class="label">Kelas</label>
            <select name="kelas" class="input">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $kls)
                    <option value="{{ $kls }}" {{ request('kelas') == $kls ? 'selected' : '' }}>{{ $kls }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
        @if(request()->hasAny(['search','kelas']))
            <a href="{{ route('admin.anggota.index') }}" class="btn-ghost">Reset</a>
        @endif
        <div class="flex-1"></div>
        <a href="{{ route('admin.anggota.create') }}" class="btn-accent"><i class="fa-solid fa-user-plus"></i> Tambah Anggota</a>
    </form>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 rounded-l-xl">Anggota</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500">NIS</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500">Kelas</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500">No. HP</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-gray-500">Status</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-gray-500 rounded-r-xl">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($anggotas as $anggota)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-primary font-bold text-sm">{{ strtoupper(substr($anggota->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $anggota->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $anggota->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-mono text-xs text-gray-600">{{ $anggota->nis }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $anggota->kelas }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $anggota->no_hp ?? '-' }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="badge {{ $anggota->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $anggota->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.anggota.show', $anggota) }}" class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.anggota.edit', $anggota) }}" class="w-8 h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('admin.anggota.destroy', $anggota) }}" method="POST" onsubmit="return confirm('Yakin hapus anggota ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-16 text-gray-400">
                        <i class="fa-solid fa-users text-4xl mb-3 block opacity-30"></i>
                        <p>Belum ada data anggota</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $anggotas->links() }}</div>
</div>
@endsection
