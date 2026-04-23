@extends('layouts.app')
@section('title', 'Approval Pengembalian Buku')
@section('page-title', 'Approval Pengembalian Buku')
@section('page-subtitle', 'Kelola request pengembalian buku dari siswa')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6">
    
    {{-- Header Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-xs">Menunggu Approval</p>
                    <p class="text-white text-2xl font-bold">{{ $pendingReturns->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-hourglass-half text-white text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-xs">Total Diproses</p>
                    <p class="text-white text-2xl font-bold">{{ $approvedReturns->total() }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-white text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-xs">Disetujui</p>
                    <p class="text-white text-2xl font-bold">{{ $approvedReturns->where('status_pengembalian', 'disetujui')->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-white text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-xs">Ditolak</p>
                    <p class="text-white text-2xl font-bold">{{ $approvedReturns->where('status_pengembalian', 'ditolak')->count() }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-times-circle text-white text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Returns Section --}}
    @if($pendingReturns->isNotEmpty())
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-bell text-blue-500 text-xl"></i>
                <h2 class="font-bold text-gray-800">Request Pengembalian Menunggu</h2>
                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">{{ $pendingReturns->count() }}</span>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            @foreach($pendingReturns as $p)
                @php
                    $terlambat = \Carbon\Carbon::today()->gt($p->tgl_kembali_rencana);
                    $hariTerlambat = $terlambat ? abs(\Carbon\Carbon::today()->diffInDays($p->tgl_kembali_rencana, false)) : 0;
                    $denda = $hariTerlambat * 1000;
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-blue-200 overflow-hidden hover:shadow-md transition">
                    <div class="bg-blue-50 px-4 py-3 border-b border-blue-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-receipt text-blue-600 text-sm"></i>
                                <span class="font-mono text-xs text-blue-700">{{ $p->kode_pinjam }}</span>
                            </div>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">
                                <i class="fa-solid fa-hourglass-half mr-1"></i>Menunggu
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <div class="flex gap-4 mb-4">
                            <div class="w-16 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-user-graduate text-white text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800">{{ $p->user->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $p->user->kelas ?? 'Kelas tidak tersedia' }}</p>
                                <p class="text-sm text-gray-700 mt-1 font-medium">{{ $p->buku->judul }}</p>
                                <p class="text-xs text-gray-500">Kode: {{ $p->buku->kode_buku }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="bg-gray-50 rounded-lg p-2">
                                <p class="text-xs text-gray-400">Tgl Pinjam</p>
                                <p class="font-semibold text-sm">{{ $p->tgl_pinjam->format('d/m/Y') }}</p>
                            </div>
                            <div class="{{ $terlambat ? 'bg-red-50' : 'bg-gray-50' }} rounded-lg p-2">
                                <p class="text-xs {{ $terlambat ? 'text-red-500' : 'text-gray-400' }}">Batas Kembali</p>
                                <p class="font-semibold text-sm {{ $terlambat ? 'text-red-700' : 'text-gray-800' }}">{{ $p->tgl_kembali_rencana->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        @if($terlambat)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                                <div class="flex items-start gap-2">
                                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                                    <div>
                                        <p class="text-sm font-semibold text-red-700">Terlambat {{ $hariTerlambat }} hari</p>
                                        <p class="text-xs text-red-600">Denda: Rp {{ number_format($denda, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex gap-2">
                            <a href="{{ route('admin.pengembalian-approval.show', $p->id) }}" 
                               class="flex-1 px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-semibold text-center transition">
                                <i class="fa-solid fa-eye mr-1"></i> Detail
                            </a>
                            <form action="{{ route('admin.pengembalian-approval.approve', $p->id) }}" method="POST" class="flex-1"
                                  onsubmit="return confirm('Setujui pengembalian buku {{ $p->buku->judul }} oleh {{ $p->user->name }}?{{ $terlambat ? ' Denda: Rp ' . number_format($denda, 0, ',', '.') : '' }}')">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-semibold transition">
                                    <i class="fa-solid fa-check mr-1"></i> Setujui
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-8 text-center">
        <i class="fa-solid fa-check-circle text-blue-500 text-2xl mb-2 block"></i>
        <p class="text-blue-700">Tidak ada request pengembalian yang menunggu</p>
    </div>
    @endif

    {{-- History Returns Section --}}
    <div>
        <div class="flex items-center gap-2 mb-4">
            <i class="fa-solid fa-history text-blue-500 text-xl"></i>
            <h2 class="font-bold text-gray-800">Riwayat Pengembalian</h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-blue-500 to-blue-600">
                        <tr>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-white rounded-l-xl">Kode</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-white">Peminjam</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-white">Buku</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-white">Tgl Kembali</th>
                            <th class="text-center py-3 px-4 text-xs font-semibold text-white">Status</th>
                            <th class="text-center py-3 px-4 text-xs font-semibold text-white">Denda</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-white">Diproses Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($approvedReturns as $t)
                            @php
                                $terlambat = \Carbon\Carbon::parse($t->tgl_kembali_rencana)->lt($t->tgl_kembali_aktual);
                            @endphp
                            <tr class="hover:bg-blue-50/50 transition">
                                <td class="py-3 px-4 font-mono text-xs text-gray-500">{{ $t->kode_pinjam }}</td>
                                <td class="py-3 px-4">
                                    <p class="font-medium text-gray-800">{{ $t->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $t->user->kelas ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-gray-700 truncate max-w-[200px]">{{ $t->buku->judul }}</p>
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-500">
                                    {{ $t->tgl_kembali_aktual ? \Carbon\Carbon::parse($t->tgl_kembali_aktual)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($t->status_pengembalian == 'disetujui')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">
                                            <i class="fa-solid fa-check-circle text-[10px]"></i> Disetujui
                                        </span>
                                    @elseif($t->status_pengembalian == 'ditolak')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">
                                            <i class="fa-solid fa-times-circle text-[10px]"></i> Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($t->denda > 0)
                                        <span class="text-red-600 font-semibold">Rp {{ number_format($t->denda, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-500">
                                    {{ $t->approver ? $t->approver->name : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 text-gray-400">
                                    <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                                    <p>Belum ada riwayat pengembalian</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $approvedReturns->links() }}
            </div>
        </div>
    </div>
</div>
@endsection