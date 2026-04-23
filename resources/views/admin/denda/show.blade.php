@extends('layouts.app')
@section('title', 'Detail Denda')
@section('page-title', 'Detail Denda')
@section('page-subtitle', 'Konfirmasi pembayaran denda')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/80 text-xs">Kode Transaksi</p>
                    <p class="text-white font-mono text-lg">{{ $pinjam->kode_pinjam }}</p>
                </div>
                @if($pinjam->status_denda == 'menunggu')
                    <span class="px-3 py-1 bg-yellow-400 text-yellow-900 rounded-full text-xs font-semibold">
                        <i class="fa-solid fa-hourglass-half mr-1"></i> Menunggu Konfirmasi
                    </span>
                @elseif($pinjam->status_denda == 'lunas')
                    <span class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-semibold">
                        <i class="fa-solid fa-check-circle mr-1"></i> Lunas
                    </span>
                @else
                    <span class="px-3 py-1 bg-red-500 text-white rounded-full text-xs font-semibold">
                        <i class="fa-solid fa-exclamation-triangle mr-1"></i> Belum Dibayar
                    </span>
                @endif
            </div>
        </div>

        {{-- Content --}}
        <div class="p-6">
            {{-- Informasi Siswa --}}
            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-user text-blue-500"></i> Informasi Siswa
                </h3>
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400">Nama Lengkap</p>
                            <p class="font-medium">{{ $pinjam->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Kelas</p>
                            <p class="font-medium">{{ $pinjam->user->kelas ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Email</p>
                            <p class="font-medium">{{ $pinjam->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">No. Telepon</p>
                            <p class="font-medium">{{ $pinjam->user->no_telp ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informasi Buku --}}
            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-book text-blue-500"></i> Informasi Buku
                </h3>
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400">Judul Buku</p>
                            <p class="font-medium">{{ $pinjam->buku->judul }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Kode Buku</p>
                            <p class="font-medium">{{ $pinjam->buku->kode_buku }}</p>
                        </div