@extends('layouts.app')
@section('title', 'Dashboard Siswa')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Perpustakaan Digital - ' . auth()->user()->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6">
    
    {{-- Welcome Header --}}
    <div class="mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-sky-400 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-user text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-white/80 text-xs">Selamat datang kembali,</p>
                        <h2 class="text-white font-bold text-lg">{{ auth()->user()->name }}</h2>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-white/70 text-[10px]">{{ now()->isoFormat('dddd') }}</p>
                    <p class="text-white text-sm font-medium">{{ now()->isoFormat('D MMM Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-blue-600">{{ $riwayat }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total Peminjaman</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-clock-rotate-left text-blue-600 text-sm"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm border {{ $terlambat > 0 ? 'border-red-200 bg-red-50' : 'border-gray-100' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold {{ $terlambat > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $terlambat }}</p>
                    <p class="text-xs text-gray-500 mt-1">Terlambat</p>
                </div>
                <div class="w-10 h-10 {{ $terlambat > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-clock {{ $terlambat > 0 ? 'text-red-500' : 'text-gray-400' }} text-sm"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-green-600">{{ $dipinjam->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Sedang Dipinjam</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-book-open text-green-600 text-sm"></i>
                </div>
            </div>
        </div>

        {{-- Card Denda --}}
        <div class="bg-white rounded-xl p-4 shadow-sm border {{ isset($totalDenda) && $totalDenda > 0 ? 'border-red-200 bg-red-50' : 'border-gray-100' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xl font-bold {{ isset($totalDenda) && $totalDenda > 0 ? 'text-red-600' : 'text-gray-800' }}">
                        Rp {{ number_format($totalDenda ?? 0, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">Total Denda</p>
                </div>
                <div class="w-10 h-10 {{ isset($totalDenda) && $totalDenda > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-money-bill {{ isset($totalDenda) && $totalDenda > 0 ? 'text-red-500' : 'text-gray-400' }} text-sm"></i>
                </div>
            </div>
            @if(isset($totalDenda) && $totalDenda > 0)
                <div class="mt-2">
                    <a href="{{ route('siswa.denda.index') }}" class="text-xs text-red-600 hover:underline font-medium">
                        Bayar denda sekarang →
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        {{-- Kolom Kiri: Info & Quote --}}
        <div class="space-y-5">
            {{-- Info Peminjaman --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 bg-gradient-to-r from-blue-500 to-sky-400">
                    <h3 class="font-semibold text-white text-sm">Aturan Peminjaman</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex items-center gap-3 pb-2 border-b border-gray-100">
                        <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="text-blue-600 text-xs">⏱️</span>
                        </div>
                        <span class="text-sm text-gray-600">Durasi pinjam: <strong class="text-blue-600">7 hari</strong></span>
                    </div>
                    <div class="flex items-center gap-3 pb-2 border-b border-gray-100">
                        <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="text-blue-600 text-xs">📚</span>
                        </div>
                        <span class="text-sm text-gray-600">Maksimal: <strong class="text-blue-600">3 buku</strong> sekaligus</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="text-blue-600 text-xs">💰</span>
                        </div>
                        <span class="text-sm text-gray-600">Denda terlambat: <strong class="text-red-500">Rp 1.000/hari</strong></span>
                    </div>
                </div>
            </div>

            {{-- Quote Motivasi --}}
            <div class="bg-gradient-to-br from-blue-50 to-sky-50 rounded-xl p-4 border border-blue-100">
                <div class="flex items-start gap-3">
                    <div class="text-2xl">📖</div>
                    <div>
                        <p class="text-xs text-gray-600 italic leading-relaxed">"Baca buku 10 menit sehari, tambah ilmu selamanya."</p>
                        <p class="text-[10px] text-gray-400 mt-2">- Tips Perpustakaan</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Buku Aktif Dipinjam --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 bg-gradient-to-r from-blue-500 to-sky-400 flex items-center justify-between">
                    <h3 class="font-semibold text-white text-sm">📖 Buku yang Sedang Dipinjam</h3>
                    <a href="{{ route('siswa.pengembalian.index') }}" class="text-xs text-white/80 hover:text-white font-medium transition">
                        Lihat Semua →
                    </a>
                </div>
                
                <div class="p-4 space-y-3">
                    @forelse($dipinjam as $p)
                        @php
                            $terlambatStatus = \Carbon\Carbon::today()->gt($p->tgl_kembali_rencana);
                            $sisa = \Carbon\Carbon::today()->diffInDays($p->tgl_kembali_rencana, false);
                        @endphp
                        <div class="flex items-center gap-3 p-3 rounded-lg {{ $terlambatStatus ? 'bg-red-50 border border-red-100' : 'bg-gray-50' }}">
                            <div class="w-10 h-12 bg-gradient-to-br from-blue-400 to-sky-300 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-book text-white text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm truncate">{{ $p->buku->judul }}</p>
                                <p class="text-xs text-gray-400">{{ $p->buku->pengarang }}</p>
                                <p class="text-xs {{ $terlambatStatus ? 'text-red-600 font-semibold' : 'text-gray-500' }} mt-0.5">
                                    @if($terlambatStatus)
                                        ⚠️ Terlambat {{ abs($sisa) }} hari
                                    @else
                                        📅 Kembali: {{ $p->tgl_kembali_rencana->isoFormat('D MMM Y') }} ({{ $sisa }} hari)
                                    @endif
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $terlambatStatus ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $terlambatStatus ? 'Terlambat' : 'Aktif' }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fa-solid fa-book-open text-gray-300 text-xl"></i>
                            </div>
                            <p class="text-gray-400 text-sm">Belum ada buku yang dipinjam</p>
                            <a href="{{ route('siswa.peminjaman.index') }}" class="inline-block mt-3 px-4 py-2 bg-gradient-to-r from-blue-500 to-sky-400 text-white text-xs rounded-lg hover:from-blue-600 transition">
                                + Pinjam Sekarang
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Koleksi Terbaru --}}
    <div class="mt-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-gradient-to-r from-blue-500 to-sky-400 flex items-center justify-between">
                <h3 class="font-semibold text-white text-sm">🆕 Koleksi Buku Terbaru</h3>
                <a href="{{ route('siswa.peminjaman.index') }}" class="text-xs text-white/80 hover:text-white font-medium transition">
                    Lihat Semua Buku →
                </a>
            </div>
            
            <div class="p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($buku_baru as $buku)
                        <div class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition border border-gray-100">
                            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-book text-blue-500 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-800 truncate">{{ $buku->judul }}</p>
                                <p class="text-[10px] text-gray-400 truncate">{{ $buku->kategori->nama_kategori }}</p>
                            </div>
                            <span class="px-1.5 py-0.5 text-[9px] rounded-full {{ $buku->stok_tersedia > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                {{ $buku->stok_tersedia > 0 ? 'Ada' : 'Habis' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection