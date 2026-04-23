@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan data perpustakaan hari ini')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6">

    {{-- Header Welcome --}}
    <div class="mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-sky-400 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-chart-line text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="text-white/80 text-xs">Dashboard Admin</p>
                        <p class="text-white font-semibold text-sm">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
                    </div>
                </div>
                <div class="bg-white/20 rounded-lg px-3 py-1">
                    <p class="text-white text-xs font-medium">Perpustakaan Digital</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xl font-bold text-blue-600">{{ $stats['total_buku'] }}</p>
                    <p class="text-[10px] text-gray-500">Total Buku</p>
                </div>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-book text-blue-600 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xl font-bold text-green-600">{{ $stats['total_anggota'] }}</p>
                    <p class="text-[10px] text-gray-500">Total Anggota</p>
                </div>
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-users text-green-600 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xl font-bold text-amber-500">{{ $stats['dipinjam'] }}</p>
                    <p class="text-[10px] text-gray-500">Dipinjam</p>
                </div>
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-book-open text-amber-500 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xl font-bold text-red-500">{{ $stats['terlambat'] }}</p>
                    <p class="text-[10px] text-gray-500">Terlambat</p>
                </div>
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-clock text-red-500 text-sm"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-purple-600">Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}</p>
                    <p class="text-[10px] text-gray-500">Total Denda</p>
                </div>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-money-bill text-purple-600 text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        
        {{-- Grafik --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800 text-sm">Grafik Peminjaman 6 Bulan</h3>
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
            </div>
            <canvas id="grafikPeminjaman" height="80"></canvas>
        </div>

        {{-- Buku Terpopuler --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h3 class="font-semibold text-gray-800 text-sm mb-3">Buku Terpopuler</h3>
            <div class="space-y-2">
                @forelse($buku_populer as $i => $buku)
                    <div class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition">
                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold
                            {{ $i === 0 ? 'bg-amber-400 text-white' : ($i === 1 ? 'bg-gray-300 text-gray-700' : 'bg-gray-100 text-gray-500') }}">
                            {{ $i + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-800 truncate">{{ $buku->judul }}</p>
                            <p class="text-[10px] text-gray-400">{{ $buku->pinjams_count }} x dipinjam</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-4">Belum ada data</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 bg-gradient-to-r from-blue-500 to-sky-400 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-white text-xs"></i>
                <h3 class="font-semibold text-white text-sm">Transaksi Terbaru</h3>
            </div>
            <a href="{{ route('admin.transaksi.index') }}" class="text-white/80 hover:text-white text-xs transition">Lihat semua →</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-2 px-3 text-[10px] font-semibold text-gray-500">Kode</th>
                        <th class="text-left py-2 px-3 text-[10px] font-semibold text-gray-500">Peminjam</th>
                        <th class="text-left py-2 px-3 text-[10px] font-semibold text-gray-500">Buku</th>
                        <th class="text-left py-2 px-3 text-[10px] font-semibold text-gray-500">Tgl Pinjam</th>
                        <th class="text-center py-2 px-3 text-[10px] font-semibold text-gray-500">Status</th>
                        <th class="text-right py-2 px-3 text-[10px] font-semibold text-gray-500">Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pinjam_terbaru as $p)
                        @php
                            $terlambatStatus = $p->status === 'dipinjam' && \Carbon\Carbon::today()->gt($p->tgl_kembali_rencana);
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-2 px-3 font-mono text-[11px] text-gray-500">{{ $p->kode_pinjam }}</td>
                            <td class="py-2 px-3 text-xs font-medium text-gray-800">{{ $p->user->name }}</td>
                            <td class="py-2 px-3 text-xs text-gray-600 max-w-xs truncate">{{ $p->buku->judul }}</td>
                            <td class="py-2 px-3 text-xs text-gray-500">{{ $p->tgl_pinjam->format('d/m/Y') }}</td>
                            <td class="py-2 px-3 text-center">
                                @if($p->status === 'menunggu')
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-amber-100 text-amber-700">Menunggu</span>
                                @elseif($p->status === 'dipinjam')
                                    @if($terlambatStatus)
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-100 text-red-700">Terlambat</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-blue-100 text-blue-700">Dipinjam</span>
                                    @endif
                                @elseif($p->status === 'dikembalikan')
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-green-100 text-green-700">Kembali</span>
                                @elseif($p->status === 'ditolak')
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-gray-200 text-gray-700">Ditolak</span>
                                @elseif($p->status === 'hilang')
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-100 text-red-700">Hilang</span>
                                @elseif($p->status === 'rusak')
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-orange-100 text-orange-700">Rusak</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-gray-100 text-gray-600">{{ ucfirst($p->status) }}</span>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-right text-xs font-medium {{ $p->denda > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                {{ $p->denda > 0 ? 'Rp '.number_format($p->denda,0,',','.') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-400 text-xs">Belum ada transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('grafikPeminjaman');
const grafik = @json($grafik);
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: grafik.map(g => g.bulan),
        datasets: [{
            label: 'Jumlah Peminjaman',
            data: grafik.map(g => g.jumlah),
            backgroundColor: '#3b82f6',
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: '#f1f5f9' } },
            x: { ticks: { font: { size: 10 } }, grid: { display: false } }
        }
    }
});
</script>
@endpush
@endsection