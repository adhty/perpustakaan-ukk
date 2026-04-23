@extends('layouts.app')
@section('title', 'Detail Buku')
@section('page-title', 'Detail Buku')
@section('page-subtitle', $buku->judul)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Info Buku --}}
    <div class="card lg:col-span-1">
        <div class="text-center mb-5">
            @if($buku->sampul)
                <img src="{{ asset('storage/' . $buku->sampul) }}" class="h-48 mx-auto rounded-xl object-cover shadow">
            @else
                <div class="h-48 bg-gradient-to-br from-primary to-primary-light rounded-xl flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-book text-white text-5xl opacity-40"></i>
                </div>
            @endif
        </div>

        <div class="space-y-3 text-sm">

            @foreach([
                ['Kode', $buku->kode_buku, 'font-mono'],
                ['Judul', $buku->judul, 'font-semibold'],
                ['Pengarang', $buku->pengarang],
                ['Penerbit', $buku->penerbit],
                ['Tahun Terbit', $buku->tahun_terbit],
                ['ISBN', $buku->isbn ?? '-'],
                ['Kategori', $buku->kategori->nama_kategori],
                ['Rak', $buku->rak ?? '-'],
            ] as $item)

                @php
                    $k = $item[0];
                    $v = $item[1];
                    $cls = $item[2] ?? '';
                @endphp

                <div class="flex justify-between items-start gap-2">
                    <span class="text-gray-400 flex-shrink-0">{{ $k }}</span>
                    <span class="text-gray-800 text-right {{ $cls }}">{{ $v }}</span>
                </div>

            @endforeach

            <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                <span class="text-gray-400">Stok Total</span>
                <span class="font-bold text-gray-800">{{ $buku->stok }}</span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-gray-400">Stok Tersedia</span>
                <span class="badge {{ $buku->stok_tersedia > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-sm">
                    {{ $buku->stok_tersedia }}
                </span>
            </div>

        </div>

        <div class="flex gap-2 mt-5 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.buku.edit', $buku) }}" class="btn-primary flex-1 justify-center">
                <i class="fa-solid fa-pen"></i> Edit
            </a>
            <a href="{{ route('admin.buku.index') }}" class="btn-ghost flex-1 justify-center">Kembali</a>
        </div>
    </div>

    {{-- Riwayat Pinjam --}}
    <div class="card lg:col-span-2">
        <h3 class="font-bold text-gray-800 mb-4">Riwayat Peminjaman Buku</h3>

        @if($buku->deskripsi)
            <div class="bg-blue-50 rounded-xl p-4 mb-5 text-sm text-blue-800">
                <p class="font-semibold mb-1">Deskripsi:</p>
                <p>{{ $buku->deskripsi }}</p>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500">Peminjam</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500">Tgl Pinjam</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500">Tgl Kembali</th>
                        <th class="text-left py-2 px-3 text-xs font-semibold text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($buku->pinjams as $p)
                        <tr>
                            <td class="py-2.5 px-3 font-medium">{{ $p->user->name }}</td>
                            <td class="py-2.5 px-3 text-gray-500">{{ $p->tgl_pinjam->format('d/m/Y') }}</td>
                            <td class="py-2.5 px-3 text-gray-500">
                                {{ $p->tgl_kembali_aktual ? $p->tgl_kembali_aktual->format('d/m/Y') : $p->tgl_kembali_rencana->format('d/m/Y') }}
                            </td>
                            <td class="py-2.5 px-3">
                                @if($p->status === 'dipinjam')
                                    <span class="badge bg-blue-100 text-blue-700">Dipinjam</span>
                                @else
                                    <span class="badge bg-green-100 text-green-700">Dikembalikan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-400 py-8">
                                Belum pernah dipinjam
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection