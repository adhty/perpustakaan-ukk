@extends('layouts.app')
@section('title', 'Detail Anggota')
@section('page-title', 'Detail Anggota')
@section('page-subtitle', $anggota->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="card text-center">
        <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="text-primary font-bold text-3xl">{{ strtoupper(substr($anggota->name, 0, 1)) }}</span>
        </div>
        <h3 class="font-bold text-gray-800 text-lg">{{ $anggota->name }}</h3>
        <p class="text-gray-400 text-sm">{{ $anggota->email }}</p>
        <span class="badge {{ $anggota->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} mt-2">
            {{ $anggota->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>

        <div class="mt-5 space-y-3 text-sm text-left border-t border-gray-100 pt-4">
            @foreach(['NIS' => $anggota->nis, 'Username' => $anggota->username, 'Kelas' => $anggota->kelas, 'No. HP' => ($anggota->no_hp ?? '-'), 'Terdaftar' => $anggota->created_at->format('d/m/Y')] as $k => $v)
                <div class="flex justify-between">
                    <span class="text-gray-400">{{ $k }}</span>
                    <span class="font-medium text-gray-800">{{ $v }}</span>
                </div>
            @endforeach
        </div>

        <div class="flex gap-2 mt-5">
            <a href="{{ route('admin.anggota.edit', $anggota) }}" class="btn-primary flex-1 justify-center text-xs">
                <i class="fa-solid fa-pen"></i> Edit
            </a>
            <a href="{{ route('admin.anggota.index') }}" class="btn-ghost flex-1 justify-center text-xs">Kembali</a>
        </div>
    </div>

    <div class="card lg:col-span-2">
        <h3 class="font-bold text-gray-800 mb-4">Riwayat Peminjaman</h3>

        <div class="grid grid-cols-3 gap-3 mb-5">
            <div class="bg-blue-50 rounded-xl p-3 text-center">
                <p class="text-xl font-bold text-blue-700">{{ $anggota->pinjams->count() }}</p>
                <p class="text-xs text-blue-500">Total Pinjam</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-3 text-center">
                <p class="text-xl font-bold text-amber-700">{{ $anggota->pinjams->where('status','dipinjam')->count() }}</p>
                <p class="text-xs text-amber-500">Sedang Dipinjam</p>
            </div>
            <div class="bg-green-50 rounded-xl p-3 text-center">
                <p class="text-xl font-bold text-green-700">{{ $anggota->pinjams->where('status','dikembalikan')->count() }}</p>
                <p class="text-xs text-green-500">Sudah Kembali</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500">Buku</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500">Tgl Pinjam</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500">Tgl Kembali</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500">Status</th>
                        <th class="text-right py-2 px-3 text-xs font-semibold text-gray-500">Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($anggota->pinjams->sortByDesc('created_at') as $p)
                        <tr>
                            <td class="py-2.5 px-3 font-medium text-gray-800 max-w-xs truncate">{{ $p->buku ? $p->buku->judul : 'Buku tidak ditemukan' }}</td>
                            <td class="py-2.5 px-3 text-gray-500 text-xs">{{ $p->tgl_pinjam ? $p->tgl_pinjam->format('d/m/Y') : '-' }}</td>
                            <td class="py-2.5 px-3 text-gray-500 text-xs">{{ ($p->tgl_kembali_aktual ?? $p->tgl_kembali_rencana) ? ($p->tgl_kembali_aktual ?? $p->tgl_kembali_rencana)->format('d/m/Y') : '-' }}</td>
                            <td class="py-2.5 px-3">
                                @if($p->status === 'dipinjam')
                                    <span class="badge bg-blue-100 text-blue-700">Dipinjam</span>
                                @else
                                    <span class="badge bg-green-100 text-green-700">Kembali</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-right text-xs">
                                @if($p->denda > 0)
                                    <span class="text-red-600 font-semibold">Rp {{ number_format($p->denda,0,',','.') }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-8 text-gray-400">Belum ada riwayat peminjaman</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
