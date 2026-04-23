@extends('layouts.app')
@section('title', 'Detail Request Pengembalian')
@section('page-title', 'Detail Request Pengembalian')
@section('page-subtitle', 'Lihat detail request pengembalian buku dari siswa')

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

            {{-- Informasi Peminjam --}}
            <div class="border-b border-gray-100 pb-4 mb-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-user text-blue-500"></i>
                    Informasi Peminjam
                </h3>
                <div class="flex gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-sky-300 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-user-graduate text-white text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800">{{ $pinjam->user->name }}</h4>
                        <p class="text-sm text-gray-600 mt-1">Kelas: {{ $pinjam->user->kelas ?? 'Tidak tersedia' }}</p>
                        <p class="text-sm text-gray-600">Email: {{ $pinjam->user->email }}</p>
                        <p class="text-sm text-gray-600">No. Telp: {{ $pinjam->user->no_telp ?? '-' }}</p>
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
                    <div class="w-16 h-20 bg-gradient-to-br from-blue-400 to-sky-300 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-book text-white text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800">{{ $pinjam->buku->judul }}</h4>
                        <p class="text-sm text-gray-600 mt-1">Pengarang: {{ $pinjam->buku->pengarang }}</p>
                        <p class="text-sm text-gray-600">Penerbit: {{ $pinjam->buku->penerbit }}</p>
                        <p class="text-sm text-gray-600">Kode Buku: {{ $pinjam->buku->kode_buku }}</p>
                        <p class="text-sm text-gray-600">Stok Tersedia: {{ $pinjam->buku->stok_tersedia }}</p>
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
                            <p class="text-lg font-bold text-red-700 mt-1">Rp {{ number_format($denda, 0, ',', '.') }}</p>
                            <p class="text-xs text-red-500 mt-2">
                                <i class="fa-solid fa-info-circle mr-1"></i>
                                Denda akan dikenakan jika Anda menyetujui pengembalian ini
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
            @if($pinjam->status_pengembalian == 'pending')
                <div class="flex gap-3 mt-6">
                    <a href="{{ route('admin.pengembalian-approval.index') }}" class="flex-1 text-center py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                    </a>
                    <form action="{{ route('admin.pengembalian-approval.approve', $pinjam->id) }}" method="POST" class="flex-1"
                          onsubmit="return confirm('Setujui pengembalian buku {{ $pinjam->buku->judul }} oleh {{ $pinjam->user->name }}?{{ $terlambat ? ' Denda: Rp ' . number_format($denda, 0, ',', '.') : '' }}')">
                        @csrf
                        <button type="submit" class="w-full py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg transition">
                            <i class="fa-solid fa-check mr-2"></i> Setujui
                        </button>
                    </form>
                    <button type="button" onclick="showRejectModal()" class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                        <i class="fa-solid fa-times mr-2"></i> Tolak
                    </button>
                </div>

                {{-- Modal Tolak --}}
                <div id="rejectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
                    <div class="bg-white rounded-xl max-w-md w-full mx-4">
                        <div class="bg-red-500 px-6 py-3 rounded-t-xl">
                            <h3 class="text-white font-semibold">Tolak Pengembalian</h3>
                        </div>
                        <form action="{{ route('admin.pengembalian-approval.reject', $pinjam->id) }}" method="POST" class="p-6">
                            @csrf
                            <p class="text-gray-600 mb-3">Berikan alasan penolakan:</p>
                            <textarea name="alasan_penolakan" rows="4" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Masukkan alasan penolakan..." required></textarea>
                            <div class="flex gap-3 mt-4">
                                <button type="button" onclick="hideRejectModal()" class="flex-1 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition">Batal</button>
                                <button type="submit" class="flex-1 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">Tolak</button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="flex gap-3 mt-6">
                    <a href="{{ route('admin.pengembalian-approval.index') }}" class="flex-1 text-center py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function showRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal').classList.add('flex');
    }
    function hideRejectModal() {
        document.getElementById('rejectModal').classList.remove('flex');
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endsection