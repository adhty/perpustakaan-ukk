@extends('layouts.app')
@section('title', 'Kelola Denda')
@section('page-title', 'Kelola Denda')
@section('page-subtitle', 'Konfirmasi pembayaran denda siswa')

@section('content')
<div class="max-w-7xl mx-auto">
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-400 rounded-xl p-4">
            <div class="flex justify-between">
                <div>
                    <p class="text-white/80 text-xs">Menunggu Konfirmasi</p>
                    <p class="text-white text-2xl font-bold">{{ $stats['total_pending'] }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-clock text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4">
            <div class="flex justify-between">
                <div>
                    <p class="text-white/80 text-xs">Total Denda Menunggu</p>
                    <p class="text-white text-2xl font-bold">Rp {{ number_format($stats['total_nominal_pending'], 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-wave text-white"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4">
            <div class="flex justify-between">
                <div>
                    <p class="text-white/80 text-xs">Sudah Lunas</p>
                    <p class="text-white text-2xl font-bold">Rp {{ number_format($stats['total_lunas'], 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-white"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Denda --}}
    @if($pendingDenda->isNotEmpty())
    <div class="mb-8">
        <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-bell text-yellow-500"></i> Menunggu Konfirmasi
            <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">{{ $pendingDenda->count() }}</span>
        </h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            @foreach($pendingDenda as $denda)
            <div class="bg-white rounded-xl shadow-sm border border-yellow-200 overflow-hidden">
                <div class="bg-yellow-50 px-4 py-3 border-b border-yellow-100">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs text-yellow-700">{{ $denda->kode_pinjam }}</span>
                        <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-700">
                            <i class="fa-solid fa-hourglass-half mr-1"></i>Menunggu
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex gap-4 mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-user-graduate text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800">{{ $denda->user->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $denda->user->kelas ?? '-' }}</p>
                            <p class="text-sm text-gray-700 mt-1">{{ $denda->buku->judul }}</p>
                        </div>
                    </div>
                    <div class="bg-red-50 rounded-lg p-3 mb-4">
                        <p class="text-sm font-semibold text-red-700">Denda: Rp {{ number_format($denda->denda, 0, ',', '.') }}</p>
                        <p class="text-xs text-red-600">Tanggal Bayar: {{ $denda->tanggal_bayar ? \Carbon\Carbon::parse($denda->tanggal_bayar)->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('admin.denda.approve', $denda->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-semibold transition"
                                    onclick="return confirm('Konfirmasi pembayaran denda Rp {{ number_format($denda->denda, 0, ',', '.') }} dari {{ $denda->user->name }}?')">
                                <i class="fa-solid fa-check mr-1"></i> Konfirmasi Lunas
                            </button>
                        </form>
                        <form action="{{ route('admin.denda.reject', $denda->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-semibold transition"
                                    onclick="return confirm('Tolak pembayaran denda {{ $denda->user->name }}?')">
                                <i class="fa-solid fa-times mr-1"></i> Tolak
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-8 text-center">
        <i class="fa-solid fa-check-circle text-green-500 text-2xl mb-2 block"></i>
        <p class="text-green-700">Tidak ada pembayaran denda yang menunggu konfirmasi</p>
    </div>
    @endif

    {{-- Riwayat Denda --}}
    <div>
        <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-history text-blue-500"></i> Riwayat Denda
        </h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gradient-to-r from-blue-500 to-blue-600">
                    <tr>
                        <th class="text-left py-3 px-4 text-white">Kode</th>
                        <th class="text-left py-3 px-4 text-white">Siswa</th>
                        <th class="text-left py-3 px-4 text-white">Buku</th>
                        <th class="text-center py-3 px-4 text-white">Denda</th>
                        <th class="text-center py-3 px-4 text-white">Tgl Bayar</th>
                        <th class="text-center py-3 px-4 text-white">Tgl Approve</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayatLunas as $denda)
                    <tr class="hover:bg-blue-50/50">
                        <td class="py-3 px-4 font-mono text-xs">{{ $denda->kode_pinjam }}</td>
                        <td class="py-3 px-4">{{ $denda->user->name }}<br><span class="text-xs text-gray-400">{{ $denda->user->kelas ?? '-' }}</span></td>
                        <td class="py-3 px-4">{{ $denda->buku->judul }}</td>
                        <td class="py-3 px-4 text-center text-red-600 font-semibold">Rp {{ number_format($denda->denda, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center text-gray-500">{{ $denda->tanggal_bayar ? \Carbon\Carbon::parse($denda->tanggal_bayar)->format('d/m/Y') : '-' }}</td>
                        <td class="py-3 px-4 text-center text-gray-500">{{ $denda->tanggal_approve ? \Carbon\Carbon::parse($denda->tanggal_approve)->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400">
                            <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                            <p>Belum ada riwayat denda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-gray-100">
                {{ $riwayatLunas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection