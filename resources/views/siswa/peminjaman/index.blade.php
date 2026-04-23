@extends('layouts.app')
@section('title', 'Pinjam Buku')
@section('page-title', 'Pinjam Buku')
@section('page-subtitle', 'Cari dan pinjam buku yang tersedia (Maksimal 3 buku aktif)')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- Alert Limit Peminjaman --}}
    @if(isset($activePinjamCount) && $activePinjamCount >= 3)
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6 flex items-start gap-3">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-ban text-red-500 text-xl"></i>
            </div>
            <div>
                <h4 class="font-semibold text-red-800">Batas Peminjaman Tercapai!</h4>
                <p class="text-sm text-red-700 mt-0.5">
                    Anda sudah memiliki <strong>{{ $activePinjamCount }}</strong> request/pinjaman aktif dari maksimal <strong>3 buku</strong>.
                    Silakan tunggu approval atau kembalikan buku yang sedang dipinjam.
                </p>
                <a href="{{ route('siswa.peminjaman.riwayat') }}" class="inline-flex items-center gap-1 mt-2 text-sm text-red-700 font-medium hover:text-red-800">
                    <i class="fa-solid fa-arrow-right"></i> Lihat Riwayat Peminjaman
                </a>
            </div>
        </div>
    @elseif(isset($activePinjamCount) && $activePinjamCount > 0)
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-3 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clock text-blue-500"></i>
                    <span class="text-sm text-blue-800">
                        Anda memiliki <strong>{{ $activePinjamCount }}</strong> request/pinjaman aktif dari <strong>3</strong> buku
                    </span>
                </div>
                <div class="w-32 bg-blue-200 rounded-full h-1.5">
                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ ($activePinjamCount / 3) * 100 }}%"></div>
                </div>
            </div>
        </div>
    @endif

    {{-- Filter Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Cari Buku</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Judul atau pengarang..." 
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-200">
                </div>
            </div>
            <div class="w-48">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Kategori</label>
                <select name="kategori_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-400">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-sky-400 text-white rounded-xl text-sm font-semibold hover:from-blue-600 transition shadow-sm">
                Cari
            </button>
            @if(request()->hasAny(['search','kategori_id']))
                <a href="{{ route('siswa.peminjaman.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Books Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($bukus as $buku)
            @php 
                $sudahPinjam = in_array($buku->id, $dipinjamIds);
                $bisaPinjam = ($activePinjamCount < 3) && !$sudahPinjam && ($buku->stok_tersedia > 0);
                $todayDate = date('Y-m-d');
                $maxDate = date('Y-m-d', strtotime('+7 days'));
            @endphp
            <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-200">
                
                {{-- Book Cover Area --}}
                <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center overflow-hidden">
                    @if($buku->sampul && Storage::disk('public')->exists($buku->sampul))
                        <img src="{{ Storage::url($buku->sampul) }}" 
                             alt="Sampul {{ $buku->judul }}"
                             class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-105">
                    @else
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-book text-gray-300 text-6xl"></i>
                            <span class="text-xs text-gray-400 mt-2">Tidak ada sampul</span>
                        </div>
                    @endif
                    
                    {{-- Badge --}}
                    <div class="absolute top-3 right-3">
                        @if($buku->stok_tersedia < 1)
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-500 text-white shadow-sm">HABIS</span>
                        @elseif($sudahPinjam)
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-500 text-white shadow-sm">MENUNGGU/DIPINJAM</span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-green-500 text-white shadow-sm">TERSEDIA</span>
                        @endif
                    </div>
                    
                    {{-- Stok Indicator --}}
                    <div class="absolute bottom-3 left-3 bg-black/50 backdrop-blur-sm px-2 py-0.5 rounded-full">
                        <span class="text-white text-[10px] font-medium">Stok {{ $buku->stok_tersedia }}/{{ $buku->stok }}</span>
                    </div>
                </div>

                {{-- Book Info --}}
                <div class="p-4">
                    <h4 class="font-bold text-gray-800 text-sm leading-tight mb-1 line-clamp-2">{{ $buku->judul }}</h4>
                    <p class="text-xs text-gray-500">{{ $buku->pengarang }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">{{ $buku->penerbit }} - {{ $buku->tahun_terbit }}</p>
                    
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">{{ $buku->kategori->nama_kategori }}</span>
                            @if($buku->rak)
                                <span class="text-gray-400 text-[10px]">Rak {{ $buku->rak }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Action Button --}}
                    @if($sudahPinjam)
                        <div class="mt-4 w-full py-2.5 bg-amber-50 text-amber-600 rounded-xl text-sm font-medium text-center">
                            <i class="fa-solid fa-hourglass-half mr-1"></i> Menunggu/Dipinjam
                        </div>
                    @elseif($buku->stok_tersedia < 1)
                        <div class="mt-4 w-full py-2.5 bg-gray-100 text-gray-400 rounded-xl text-sm font-medium text-center">
                            <i class="fa-solid fa-ban mr-1"></i> Stok Habis
                        </div>
                    @elseif($activePinjamCount >= 3)
                        <div class="mt-4 w-full py-2.5 bg-red-50 text-red-500 rounded-xl text-sm font-medium text-center cursor-not-allowed">
                            <i class="fa-solid fa-lock mr-1"></i> Batas Pinjam (3 Buku)
                        </div>
                    @else
                        <form action="{{ route('siswa.peminjaman.store') }}" method="POST" class="mt-4 space-y-3">
                            @csrf
                            <input type="hidden" name="buku_id" value="{{ $buku->id }}">
                            
                            {{-- Tanggal Peminjaman --}}
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1 uppercase tracking-wide">
                                    <i class="fa-regular fa-calendar mr-1"></i> Tanggal Pinjam <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tgl_pinjam" 
                                       value="{{ old('tgl_pinjam', $todayDate) }}"
                                       min="{{ $todayDate }}"
                                       max="{{ $maxDate }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-200 transition">
                                <p class="text-[9px] text-gray-400 mt-1">
                                    <i class="fa-regular fa-clock mr-1"></i> Bisa pilih tanggal hari ini hingga {{ date('d/m/Y', strtotime($maxDate)) }}
                                </p>
                            </div>
                            
                            <button type="submit" class="w-full py-2.5 bg-gradient-to-r from-blue-500 to-sky-400 text-white rounded-xl text-sm font-semibold hover:from-blue-600 transition shadow-sm">
                                <i class="fa-solid fa-paper-plane mr-1"></i> Ajukan Peminjaman
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fa-solid fa-book text-gray-300 text-3xl"></i>
                </div>
                <p class="text-gray-400 font-medium">Tidak ada buku yang ditemukan</p>
                <p class="text-xs text-gray-400 mt-1">Coba ubah kata kunci pencarian</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $bukus->links() }}
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('input[name="tgl_pinjam"]').forEach((dateInput, index) => {
        dateInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            if (!isNaN(selectedDate.getTime())) {
                const returnDate = new Date(selectedDate);
                returnDate.setDate(returnDate.getDate() + 7);
                const day = String(returnDate.getDate()).padStart(2, '0');
                const month = String(returnDate.getMonth() + 1).padStart(2, '0');
                const year = returnDate.getFullYear();
                const previewSpan = document.getElementById(`previewKembali_${index}`);
                if (previewSpan) previewSpan.innerText = `${day}/${month}/${year}`;
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .pagination {
        display: flex;
        justify-content: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .pagination .page-item {
        list-style: none;
    }
    .pagination .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
        font-size: 13px;
        font-weight: 500;
        border-radius: 10px;
        text-decoration: none;
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
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection