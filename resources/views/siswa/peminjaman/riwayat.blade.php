@extends('layouts.app')
@section('title', 'Riwayat Peminjaman')
@section('page-title', 'Riwayat Peminjaman')
@section('page-subtitle', 'Semua riwayat peminjaman buku Anda')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6">
    
    {{-- Header --}}
    <div class="mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-sky-400 rounded-2xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-clock-rotate-left text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-white/80 text-xs">Histori Peminjaman</p>
                        <p class="text-white font-semibold text-sm">Riwayat Lengkap Anda</p>
                    </div>
                </div>
                <div class="bg-white/20 rounded-lg px-3 py-1">
                    <p class="text-white text-xs font-medium">{{ $riwayat->total() }} Transaksi</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Summary --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xl font-bold text-blue-600">{{ $riwayat->total() }}</p>
                    <p class="text-[10px] text-gray-500">Total Transaksi</p>
                </div>
                <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-book text-blue-600 text-xs"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xl font-bold text-green-600">{{ $riwayat->where('status', 'dikembalikan')->count() }}</p>
                    <p class="text-[10px] text-gray-500">Dikembalikan</p>
                </div>
                <div class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-green-600 text-xs"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xl font-bold text-amber-500">{{ $riwayat->where('status', 'dipinjam')->count() }}</p>
                    <p class="text-[10px] text-gray-500">Masih Dipinjam</p>
                </div>
                <div class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-book-open text-amber-500 text-xs"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xl font-bold text-red-500">Rp {{ number_format($riwayat->where('status_denda', 'belum_lunas')->sum('denda'), 0, ',', '.') }}</p>
                    <p class="text-[10px] text-gray-500">Total Denda</p>
                </div>
                <div class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-wave text-red-500 text-xs"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 bg-gradient-to-r from-blue-500 to-sky-400">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-white text-xs"></i>
                    <p class="text-white text-xs font-semibold">Daftar Riwayat Peminjaman</p>
                </div>
                <p class="text-white/80 text-[10px]">Total: {{ $riwayat->total() }} transaksi</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500">Buku</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500">Tgl Pinjam</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500">Batas Kembali</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-gray-500">Tgl Kembali</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-gray-500">Status</th>
                        <th class="text-right py-3 px-3 text-xs font-semibold text-gray-500">Denda</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-gray-500">Status Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayat as $p)
                        @php
                            $terlambat = $p->status === 'dipinjam' && \Carbon\Carbon::today()->gt($p->tgl_kembali_rencana);
                            $isReturned = $p->status === 'dikembalikan';
                        @endphp
                        <tr class="hover:bg-gray-50 transition {{ $terlambat ? 'bg-red-50/30' : '' }}">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-9 bg-gradient-to-br from-blue-400 to-sky-300 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-book text-white text-[10px]"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $p->buku->judul }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $p->buku->pengarang }}</p>
                                    </div>
                                </div>
                                </div>
                                </td>
                            <td class="py-3 px-3 text-gray-500 text-xs">{{ $p->tgl_pinjam->format('d/m/Y') }}</td>
                            <td class="py-3 px-3">
                                <span class="text-xs {{ $terlambat ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    {{ $p->tgl_kembali_rencana->format('d/m/Y') }}
                                </span>
                                @if($terlambat)
                                    <span class="ml-1 px-1.5 py-0.5 text-[9px] rounded bg-red-100 text-red-700">Terlambat</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-gray-500 text-xs">
                                {{ $p->tgl_kembali_aktual ? $p->tgl_kembali_aktual->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($p->status === 'menunggu')
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-amber-100 text-amber-700">Menunggu</span>
                                @elseif($p->status === 'dipinjam')
                                    @if($terlambat)
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-100 text-red-700">Terlambat</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-blue-100 text-blue-700">Dipinjam</span>
                                    @endif
                                @elseif($p->status === 'ditolak')
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-gray-200 text-gray-700">Ditolak</span>
                                @elseif($p->status === 'hilang')
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-100 text-red-700">Hilang</span>
                                @elseif($p->status === 'rusak')
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-orange-100 text-orange-700">Rusak</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-green-100 text-green-700">Kembali</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right">
                                @if($p->denda > 0)
                                    <span class="text-xs font-bold text-red-600">Rp {{ number_format($p->denda, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($p->denda > 0)
                                    @if($p->status_denda === 'lunas')
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-green-100 text-green-700">
                                            <i class="fa-solid fa-check-circle text-[8px] mr-0.5"></i> Lunas
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-100 text-red-700">
                                            <i class="fa-solid fa-exclamation-circle text-[8px] mr-0.5"></i> Belum Lunas
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-clock-rotate-left text-gray-300 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-400 font-medium text-sm">Belum ada riwayat peminjaman</p>
                                    <p class="text-[10px] text-gray-400 mt-1">Anda belum pernah meminjam buku</p>
                                    <a href="{{ route('siswa.peminjaman.index') }}" class="inline-flex items-center gap-2 mt-3 px-3 py-1.5 bg-gradient-to-r from-blue-500 to-sky-400 text-white text-xs rounded-lg hover:shadow-md transition">
                                        Pinjam Buku Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($riwayat->total() > 0)
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
            {{ $riwayat->links() }}
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        flex-wrap: wrap;
    }
    .pagination .page-item {
        list-style: none;
    }
    .pagination .page-item .page-link {
        padding: 5px 10px;
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
        text-decoration: none;
        font-size: 11px;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #3b82f6, #38bdf8);
        border-color: transparent;
        color: white;
    }
    .pagination .page-item .page-link:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
</style>
@endpush
@endsection