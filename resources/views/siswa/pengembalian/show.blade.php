@extends('layouts.app')
@section('title', 'Detail Request Pengembalian')
@section('page-title', 'Detail Request Pengembalian')
@section('page-subtitle', 'Lihat detail request pengembalian buku')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-yellow-500 to-orange-400 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-clock text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-white/80 text-xs">Request Pengembalian</p>
                        <p class="text-white font-semibold">
                            @if($pinjam->status_pengembalian == 'pending')
                                Menunggu Persetujuan
                            @elseif($pinjam->status_pengembalian == 'disetujui')
                                Telah Disetujui
                            @else
                                Ditolak
                            @endif
                        </p>
                    </div>
                </div>
                <div class="bg-white/20 rounded-lg px-3 py-1">
                    <p class="text-white text-xs font-mono">{{ $pinjam->kode_pinjam }}</p>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="p-6">
            {{-- Status Badge --}}
            <div class="mb-6 text-center">
                @if($pinjam->status_pengembalian == 'pending')
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-100 text-yellow-700 rounded-full text-sm">
                        <i class="fa-solid fa-hourglass-half"></i> Menunggu Persetujuan Admin
                    </span>
                @elseif($pinjam->status_pengembalian == 'disetujui')
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm">
                        <i class="fa-solid fa-check-circle"></i> Telah Disetujui
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 rounded-full text-sm">
                        <i class="fa-solid fa-times-circle"></i> Ditolak
                    </span>
                @endif
            </div>

            {{-- Progress Status --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col items-center text-center flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center 
                            {{ $pinjam->status_pengembalian == 'pending' || $pinjam->status_pengembalian == 'disetujui' || $pinjam->status_pengembalian == 'ditolak' 
                               ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                            <i class="fa-solid fa-paper-plane text-sm"></i>
                        </div>
                        <p class="text-xs mt-2 font-medium {{ $pinjam->status_pengembalian == 'pending' || $pinjam->status_pengembalian == 'disetujui' || $pinjam->status_pengembalian == 'ditolak' ? 'text-blue-600' : 'text-gray-400' }}">Request Dikirim</p>
                        @if($pinjam->updated_at)
                            <p class="text-xs text-gray-400 mt-1">{{ $pinjam->updated_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                    
                    <div class="flex-1 h-0.5 bg-gray-200 mx-2">
                        <div class="h-full bg-blue-500 rounded" 
                             style="width: {{ $pinjam->status_pengembalian == 'disetujui' || $pinjam->status_pengembalian == 'ditolak' ? '100%' : '50%' }}"></div>
                    </div>
                    
                    <div class="flex flex-col items-center text-center flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center 
                            {{ $pinjam->status_pengembalian == 'disetujui' || $pinjam->status_pengembalian == 'ditolak' 
                               ? ($pinjam->status_pengembalian == 'disetujui' ? 'bg-green-500 text-white' : 'bg-red-500 text-white') 
                               : 'bg-gray-200 text-gray-400' }}">
                            <i class="fa-solid fa-{{ $pinjam->status_pengembalian == 'disetujui' ? 'check' : 'times' }} text-sm"></i>
                        </div>
                        <p class="text-xs mt-2 font-medium {{ $pinjam->status_pengembalian == 'disetujui' ? 'text-green-600' : ($pinjam->status_pengembalian == 'ditolak' ? 'text-red-600' : 'text-gray-400') }}">
                            {{ $pinjam->status_pengembalian == 'disetujui' ? 'Disetujui' : ($pinjam->status_pengembalian == 'ditolak' ? 'Ditolak' : 'Diproses Admin') }}
                        </p>
                        @if(($pinjam->status_pengembalian == 'disetujui' || $pinjam->status_pengembalian == 'ditolak') && $pinjam->tanggal_pengembalian_approve)
                            <p class="text-xs text-gray-400 mt-1">{{ $pinjam->tanggal_pengembalian_approve->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Informasi Buku --}}
            <div class="border-b border-gray-100 pb-4 mb-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-book text-blue-500"></i>
                    Informasi Buku
                </h3>
                <div class="flex gap-4">
                    <div class="w-20 h-24 bg-gradient-to-br from-blue-400 to-sky-300 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-book text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800">{{ $pinjam->buku->judul }}</h4>
                        <p class="text-sm text-gray-600 mt-1">Pengarang: {{ $pinjam->buku->pengarang }}</p>
                        <p class="text-sm text-gray-600">Penerbit: {{ $pinjam->buku->penerbit }}</p>
                        <p class="text-sm text-gray-600">Kode Buku: {{ $pinjam->buku->kode_buku }}</p>
                    </div>
                </div>
            </div>

            {{-- Informasi Peminjaman --}}
            <div class="grid grid-cols-2 gap-4 mb-4 border-b border-gray-100 pb-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400">Tanggal Pinjam</p>
                    <p class="font-semibold text-gray-800">{{ $pinjam->tgl_pinjam->format('d/m/Y') }}</p>
                </div>
                <div class="{{ $terlambat ? 'bg-red-50' : 'bg-gray-50' }} rounded-lg p-3">
                    <p class="text-xs {{ $terlambat ? 'text-red-500' : 'text-gray-400' }}">Batas Kembali</p>
                    <p class="font-semibold {{ $terlambat ? 'text-red-700' : 'text-gray-800' }}">{{ $pinjam->tgl_kembali_rencana->format('d/m/Y') }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400">Status Pinjaman</p>
                    <p class="font-semibold text-gray-800 capitalize">{{ $pinjam->status }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400">Request Dikirim</p>
                    <p class="font-semibold text-gray-800">{{ $pinjam->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            {{-- Informasi Keterlambatan & Denda --}}
            @if($terlambat && $pinjam->status_pengembalian == 'pending')
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation text-red-500 text-lg"></i>
                        <div>
                            <p class="font-semibold text-red-700">Terlambat {{ $hariTerlambat }} Hari</p>
                            <p class="text-sm text-red-600 mt-1">
                                Denda yang akan dikenakan: <strong>Rp {{ number_format($denda, 0, ',', '.') }}</strong>
                            </p>
                            <p class="text-xs text-red-500 mt-2">
                                <i class="fa-solid fa-info-circle mr-1"></i>
                                Denda akan dihitung otomatis saat admin menyetujui pengembalian
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Informasi Denda jika sudah dikembalikan --}}
            @if($pinjam->status_pengembalian == 'disetujui' && $pinjam->denda > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-coins text-yellow-500 text-lg"></i>
                        <div>
                            <p class="font-semibold text-yellow-700">Denda Keterlambatan</p>
                            <p class="text-lg font-bold text-yellow-800 mt-1">Rp {{ number_format($pinjam->denda, 0, ',', '.') }}</p>
                            <p class="text-xs text-yellow-600 mt-2">
                                Status Denda: 
                                <span class="font-semibold {{ $pinjam->status_denda == 'lunas' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $pinjam->status_denda == 'lunas' ? 'LUNAS' : 'BELUM LUNAS' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Alasan Penolakan (jika ditolak) --}}
            @if($pinjam->status_pengembalian == 'ditolak' && $pinjam->alasan_penolakan_pengembalian)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-ban text-red-500 text-lg"></i>
                        <div>
                            <p class="font-semibold text-red-700">Alasan Penolakan</p>
                            <p class="text-sm text-red-600 mt-1">{{ $pinjam->alasan_penolakan_pengembalian }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Informasi Approver (jika sudah diproses) --}}
            @if($pinjam->status_pengembalian != 'pending' && $pinjam->approver)
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-user-check text-gray-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">
                                Diproses oleh: <strong class="text-gray-800">{{ $pinjam->approver->name }}</strong>
                            </p>
                            @if($pinjam->tanggal_pengembalian_approve)
                                <p class="text-xs text-gray-500">
                                    Pada: {{ $pinjam->tanggal_pengembalian_approve->format('d/m/Y H:i') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Informasi Tambahan --}}
            @if($pinjam->keterangan)
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-info-circle text-blue-500 text-sm mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-blue-700">Informasi Tambahan</p>
                            <p class="text-sm text-blue-600 mt-1">{{ $pinjam->keterangan }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex gap-3 mt-6">
                <a href="{{ route('siswa.pengembalian.index') }}" class="flex-1 text-center py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                </a>
                
                @if($pinjam->status_pengembalian == 'pending')
                    <form action="{{ route('siswa.pengembalian.cancel', $pinjam->id) }}" method="POST" class="flex-1"
                          onsubmit="return confirm('Batalkan request pengembalian buku {{ $pinjam->buku->judul }}?\n\nRequest yang dibatalkan tidak dapat dipulihkan.')">
                        @csrf
                        <button type="submit" class="w-full py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                            <i class="fa-solid fa-times mr-2"></i> Batalkan Request
                        </button>
                    </form>
                @endif

                @if($pinjam->status_pengembalian == 'ditolak')
                    <a href="{{ route('siswa.pengembalian.index') }}" class="flex-1 text-center py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Ajukan Lagi
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection