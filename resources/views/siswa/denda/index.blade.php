@extends('layouts.app')
@section('title', 'Denda Saya')
@section('page-title', 'Denda Saya')
@section('page-subtitle', 'Kelola pembayaran denda')

@section('content')
<div class="max-w-4xl mx-auto">
    
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

    {{-- Total Denda --}}
    @if($totalDenda > 0)
    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm">Total Denda Belum Dibayar</p>
                <p class="text-3xl font-bold">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
            </div>
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                <i class="fa-solid fa-money-bill-wave text-2xl"></i>
            </div>
        </div>
    </div>
    @endif

    {{-- Denda Menunggu Konfirmasi --}}
    @if($dendaMenunggu->isNotEmpty())
    <div class="mb-8">
        <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-hourglass-half text-yellow-500"></i> Menunggu Konfirmasi Admin
        </h2>
        <div class="space-y-4">
            @foreach($dendaMenunggu as $denda)
            <div class="bg-yellow-50 rounded-xl shadow-sm border border-yellow-300 p-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-mono text-xs text-gray-500">{{ $denda->kode_pinjam }}</span>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-200 text-yellow-800">
                                <i class="fa-solid fa-hourglass-half mr-1"></i>Menunggu Konfirmasi
                            </span>
                        </div>
                        <h4 class="font-semibold text-gray-800">{{ $denda->buku->judul }}</h4>
                        <p class="text-sm text-gray-500">Terlambat dari {{ \Carbon\Carbon::parse($denda->tgl_kembali_rencana)->format('d/m/Y') }}</p>
                        <p class="text-lg font-bold text-yellow-700 mt-1">Rp {{ number_format($denda->denda, 0, ',', '.') }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fa-solid fa-check-circle mr-1"></i> Pembayaran telah dikirim, menunggu konfirmasi admin
                        </p>
                    </div>
                    <div>
                        <span class="inline-block px-4 py-2 bg-yellow-400 text-yellow-900 rounded-xl text-sm font-semibold">
                            <i class="fa-solid fa-clock mr-1"></i> Diproses
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Denda Belum Lunas --}}
    @if($dendaBelumLunas->isNotEmpty())
    <div class="mb-8">
        <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-clock text-red-500"></i> Denda Belum Dibayar
        </h2>
        <div class="space-y-4">
            @foreach($dendaBelumLunas as $denda)
            <div class="bg-white rounded-xl shadow-sm border border-red-200 p-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-mono text-xs text-gray-500">{{ $denda->kode_pinjam }}</span>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700">
                                <i class="fa-solid fa-exclamation-triangle mr-1"></i>Belum Dibayar
                            </span>
                        </div>
                        <h4 class="font-semibold text-gray-800">{{ $denda->buku->judul }}</h4>
                        <p class="text-sm text-gray-500">Terlambat dari {{ \Carbon\Carbon::parse($denda->tgl_kembali_rencana)->format('d/m/Y') }}</p>
                        <p class="text-lg font-bold text-red-600 mt-1">Rp {{ number_format($denda->denda, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <form action="{{ route('siswa.denda.bayar', $denda->id) }}" method="POST" 
                              onsubmit="return confirm('Bayar denda sebesar Rp {{ number_format($denda->denda, 0, ',', '.') }}?')">
                            @csrf
                            <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-sm font-semibold transition">
                                <i class="fa-solid fa-credit-card mr-1"></i> Bayar Denda
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Riwayat Denda Lunas --}}
    @if($riwayatLunas->isNotEmpty())
    <div>
        <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-check-circle text-green-500"></i> Riwayat Denda Lunas
        </h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-4">Buku</th>
                        <th class="text-left py-3 px-4">Denda</th>
                        <th class="text-left py-3 px-4">Tanggal Bayar</th>
                        <th class="text-left py-3 px-4">Dikonfirmasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($riwayatLunas as $denda)
                    <tr>
                        <td class="py-3 px-4">{{ $denda->buku->judul }}</td>
                        <td class="py-3 px-4 text-red-600 font-semibold">Rp {{ number_format($denda->denda, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-gray-500">{{ $denda->tanggal_bayar ? \Carbon\Carbon::parse($denda->tanggal_bayar)->format('d/m/Y') : '-' }}</td>
                        <td class="py-3 px-4 text-gray-500">{{ $denda->tanggal_approve ? \Carbon\Carbon::parse($denda->tanggal_approve)->format('d/m/Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($dendaBelumLunas->isEmpty() && $dendaMenunggu->isEmpty() && $riwayatLunas->isEmpty())
    <div class="text-center py-16">
        <i class="fa-regular fa-circle-check text-green-500 text-5xl mb-4"></i>
        <p class="text-gray-500">Tidak ada denda</p>
        <p class="text-sm text-gray-400">Anda selalu mengembalikan buku tepat waktu</p>
    </div>
    @endif
</div>
@endsection