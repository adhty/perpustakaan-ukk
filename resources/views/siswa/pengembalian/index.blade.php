@extends('layouts.app')
@section('title', 'Pengembalian Buku')
@section('page-title', 'Pengembalian Buku')
@section('page-subtitle', 'Ajukan pengembalian buku yang sedang Anda pinjam')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6">
    
    {{-- Header Info --}}
    <div class="mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-sky-400 rounded-2xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-white/80 text-xs">Formulir Pengembalian</p>
                        <p class="text-white font-semibold text-sm">Ajukan Pengembalian Buku</p>
                    </div>
                </div>
                <div class="bg-white/20 rounded-lg px-3 py-1">
                    <p class="text-white text-xs font-medium">{{ $pinjams->count() }} Buku Dipinjam</p>
                </div>
            </div>
        </div>
    </div>

    @if($pinjams->isEmpty() && (!isset($pendingReturns) || $pendingReturns->isEmpty()) && (!isset($approvedReturns) || $approvedReturns->isEmpty()))
        {{-- Empty State --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 text-center py-16">
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-rotate-left text-gray-300 text-3xl"></i>
                </div>
                <p class="text-gray-400 font-medium">Tidak ada buku yang perlu dikembalikan</p>
                <p class="text-sm text-gray-400 mt-1">Anda tidak sedang meminjam buku apapun</p>
                <a href="{{ route('siswa.peminjaman.index') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-sky-400 text-white text-sm rounded-lg hover:shadow-md transition">
                    Pinjam Buku Sekarang
                </a>
            </div>
        </div>
    @else
        {{-- Buku yang bisa di-request pengembalian --}}
        @if($pinjams->isNotEmpty())
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-book-open text-blue-500"></i>
                    <h3 class="font-semibold text-gray-800">Buku Yang Sedang Dipinjam</h3>
                    <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $pinjams->count() }}</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($pinjams as $p)
                        @php
                            $terlambat = \Carbon\Carbon::today()->gt($p->tgl_kembali_rencana);
                            $sisa      = \Carbon\Carbon::today()->diffInDays($p->tgl_kembali_rencana, false);
                            $denda     = $terlambat ? abs($sisa) * 1000 : 0;
                        @endphp
                        <div class="bg-white rounded-2xl shadow-sm border {{ $terlambat ? 'border-red-200 bg-red-50/30' : 'border-gray-100' }} overflow-hidden hover:shadow-md transition">
                            
                            {{-- Card Header --}}
                            <div class="px-4 py-3 {{ $terlambat ? 'bg-red-500/10 border-b border-red-100' : 'bg-gradient-to-r from-blue-500 to-sky-400' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-receipt text-white text-xs"></i>
                                        <span class="text-white text-xs font-medium">Kode: {{ $p->buku->kode_buku }}</span>
                                    </div>
                                    @if($terlambat)
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-red-500 text-white">Terlambat</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-500 text-white">Aktif</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Card Body --}}
                            <div class="p-4">
                                <div class="flex gap-4 mb-4">
                                    <div class="w-16 h-20 bg-gradient-to-br from-blue-400 to-sky-300 rounded-xl flex items-center justify-center shadow-sm">
                                        <i class="fa-solid fa-book text-white text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-800 text-sm leading-tight">{{ $p->buku->judul }}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $p->buku->pengarang }}</p>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">
                                                {{ $p->buku->penerbit }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Date Info --}}
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <p class="text-xs text-gray-400 mb-1">Tanggal Pinjam</p>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $p->tgl_pinjam->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="{{ $terlambat ? 'bg-red-50' : 'bg-gray-50' }} rounded-lg p-3">
                                        <p class="text-xs {{ $terlambat ? 'text-red-500' : 'text-gray-400' }} mb-1">Batas Kembali</p>
                                        <p class="font-semibold {{ $terlambat ? 'text-red-700' : 'text-gray-800' }} text-sm">{{ $p->tgl_kembali_rencana->format('d/m/Y') }}</p>
                                    </div>
                                </div>

                                {{-- Status Info --}}
                                @if($terlambat)
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                                        <div class="flex items-start gap-2">
                                            <i class="fa-solid fa-circle-exclamation text-red-500 text-sm mt-0.5"></i>
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-red-700">Terlambat {{ abs($sisa) }} hari</p>
                                                <p class="text-xs text-red-600 mt-1">
                                                    Denda: <strong>Rp {{ number_format($denda, 0, ',', '.') }}</strong>
                                                    <span class="block text-red-500/70 text-[10px] mt-0.5">*Rp 1.000 per hari</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-regular fa-clock text-green-600 text-sm"></i>
                                            <p class="text-sm text-green-700">
                                                Sisa waktu: <strong>{{ $sisa }} hari lagi</strong>
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Action Button --}}
                                <form action="{{ route('siswa.pengembalian.store', $p->id) }}" method="POST"
                                      onsubmit="return confirm('Ajukan pengembalian buku {{ $p->buku->judul }}?{{ $terlambat ? ' Denda: Rp ' . number_format($denda, 0, ',', '.') : '' }}\n\nPengembalian akan diproses setelah disetujui admin.')">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 rounded-lg font-semibold text-sm transition flex items-center justify-center gap-2 {{ $terlambat ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-gradient-to-r from-blue-500 to-sky-400 hover:from-blue-600 hover:to-sky-500 text-white shadow-sm' }}">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        {{ $terlambat ? 'Ajukan Pengembalian (Denda)' : 'Ajukan Pengembalian' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Request Pengembalian yang Menunggu Approval --}}
        @if(isset($pendingReturns) && $pendingReturns->isNotEmpty())
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-hourglass-half text-yellow-500"></i>
                    <h3 class="font-semibold text-gray-800">Menunggu Persetujuan Admin</h3>
                    <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full">{{ $pendingReturns->count() }}</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($pendingReturns as $p)
                        @php
                            $terlambat = \Carbon\Carbon::today()->gt($p->tgl_kembali_rencana);
                            $sisa = \Carbon\Carbon::today()->diffInDays($p->tgl_kembali_rencana, false);
                        @endphp
                        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl overflow-hidden">
                            <div class="bg-yellow-100 px-4 py-2 border-b border-yellow-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-clock text-yellow-600 text-sm"></i>
                                        <span class="text-yellow-700 text-xs font-medium">Menunggu Approval</span>
                                    </div>
                                    <span class="text-xs text-yellow-600">{{ $p->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex gap-3">
                                    <div class="w-12 h-16 bg-yellow-200 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-book text-yellow-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 text-sm">{{ $p->buku->judul }}</h4>
                                        <p class="text-xs text-gray-600 mt-1">Kode: {{ $p->buku->kode_buku }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Request dikirim: {{ $p->updated_at->format('d/m/Y H:i') }}</p>
                                        @if($terlambat)
                                            <p class="text-xs text-red-500 mt-1">Terlambat {{ abs($sisa) }} hari</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <a href="{{ route('siswa.pengembalian.show', $p->id) }}" class="flex-1 text-center py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm transition">
                                        <i class="fa-solid fa-eye mr-1"></i> Detail
                                    </a>
                                    <form action="{{ route('siswa.pengembalian.cancel', $p->id) }}" method="POST" class="flex-1"
                                          onsubmit="return confirm('Batalkan request pengembalian buku {{ $p->buku->judul }}?')">
                                        @csrf
                                        <button type="submit" class="w-full py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm transition">
                                            <i class="fa-solid fa-times mr-1"></i> Batalkan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Riwayat Pengembalian Disetujui --}}
        @if(isset($approvedReturns) && $approvedReturns->isNotEmpty())
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-check-circle text-green-500"></i>
                    <h3 class="font-semibold text-gray-800">Riwayat Pengembalian Disetujui</h3>
                    <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">{{ $approvedReturns->count() }}</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($approvedReturns as $p)
                        <div class="bg-green-50 border border-green-200 rounded-2xl p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex gap-3">
                                    <div class="w-12 h-16 bg-green-200 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-book text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $p->buku->judul }}</p>
                                        <p class="text-xs text-gray-600 mt-1">Dikembalikan: {{ $p->tanggal_pengembalian_approve ? $p->tanggal_pengembalian_approve->format('d/m/Y H:i') : '-' }}</p>
                                        @if($p->denda > 0)
                                            <p class="text-xs text-red-600 mt-1">Denda: Rp {{ number_format($p->denda, 0, ',', '.') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <i class="fa-solid fa-check-circle text-green-500 text-xl"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Riwayat Pengembalian Ditolak --}}
        @if(isset($rejectedReturns) && $rejectedReturns->isNotEmpty())
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <i class="fa-solid fa-times-circle text-red-500"></i>
                    <h3 class="font-semibold text-gray-800">Riwayat Pengembalian Ditolak</h3>
                    <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full">{{ $rejectedReturns->count() }}</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($rejectedReturns as $p)
                        <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex gap-3">
                                    <div class="w-12 h-16 bg-red-200 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-book text-red-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $p->buku->judul }}</p>
                                        <p class="text-xs text-gray-600 mt-1">Ditolak: {{ $p->updated_at->format('d/m/Y H:i') }}</p>
                                        @if($p->alasan_penolakan_pengembalian)
                                            <p class="text-xs text-red-600 mt-1">Alasan: {{ $p->alasan_penolakan_pengembalian }}</p>
                                        @endif
                                    </div>
                                </div>
                                <i class="fa-solid fa-times-circle text-red-500 text-xl"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        {{-- Summary Info Total Denda --}}
        @php
            $totalDenda = $pinjams->sum(function($p) {
                $terlambat = \Carbon\Carbon::today()->gt($p->tgl_kembali_rencana);
                $sisa = \Carbon\Carbon::today()->diffInDays($p->tgl_kembali_rencana, false);
                return $terlambat ? abs($sisa) * 1000 : 0;
            });
        @endphp
        @if($totalDenda > 0)
            <div class="mt-5 bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-center">
                <p class="text-sm text-yellow-700">
                    <i class="fa-solid fa-info-circle mr-1"></i>
                    Total denda yang akan dikenakan: <strong>Rp {{ number_format($totalDenda, 0, ',', '.') }}</strong>
                </p>
            </div>
        @endif
    @endif
</div>
@endsection