@extends('layouts.app')
@section('title', 'Laporan Transaksi')
@section('page-title', 'Laporan Transaksi')
@section('page-subtitle', 'Cetak dan ekspor laporan peminjaman buku')

@section('content')
<div class="card mb-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="w-48">
            <label class="label">Tanggal</label>
            <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="input">
        </div>
        <div class="w-48">
            <label class="label">Bulan</label>
            <input type="month" name="bulan" value="{{ request('bulan') }}" class="input">
        </div>
        <div class="w-44">
            <label class="label">Status</label>
            <select name="status" class="input">
                <option value="">Semua Status</option>
                <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="dipinjam" {{ request('status') === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="dikembalikan" {{ request('status') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                <option value="hilang" {{ request('status') === 'hilang' ? 'selected' : '' }}>Hilang</option>
            </select>
        </div>
        <button type="submit" class="btn-primary"><i class="fa-solid fa-filter"></i> Tampilkan</button>
        <button type="button" onclick="window.print()" class="btn-ghost"><i class="fa-solid fa-print"></i> Cetak</button>
    </form>
</div>

<div class="card" id="print-area">
    <div class="print:block hidden text-center mb-6">
        <h2 class="text-xl font-bold">LAPORAN PEMINJAMAN BUKU</h2>
        <p class="text-sm text-gray-500">Perpustakaan Digital SMKN 1 – Dicetak: {{ now()->isoFormat('D MMMM Y') }}</p>
    </div>

    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">Total: <span class="font-bold text-gray-800">{{ $transaksis->count() }}</span> transaksi</p>
        @if($total_denda > 0)
            <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-2 text-sm text-red-700">
                Total Denda: <span class="font-bold">Rp {{ number_format($total_denda, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left py-2.5 px-3 text-xs font-semibold text-gray-500">No</th>
                    <th class="text-left py-2.5 px-3 text-xs font-semibold text-gray-500">Kode</th>
                    <th class="text-left py-2.5 px-3 text-xs font-semibold text-gray-500">Peminjam</th>
                    <th class="text-left py-2.5 px-3 text-xs font-semibold text-gray-500">Buku</th>
                    <th class="text-left py-2.5 px-3 text-xs font-semibold text-gray-500">Tgl Pinjam</th>
                    <th class="text-left py-2.5 px-3 text-xs font-semibold text-gray-500">Tgl Kembali</th>
                    <th class="text-center py-2.5 px-3 text-xs font-semibold text-gray-500">Status</th>
                    <th class="text-right py-2.5 px-3 text-xs font-semibold text-gray-500">Denda</th>
                    <th class="text-center py-2.5 px-3 text-xs font-semibold text-gray-500">Status Denda</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transaksis as $i => $t)
                    <tr>
                        <td class="py-2 px-3 text-gray-400">{{ $i + 1 }}</td>
                        <td class="py-2 px-3 font-mono text-xs text-gray-500">{{ $t->kode_pinjam }}</td>
                        <td class="py-2 px-3">
                            <p class="font-medium">{{ $t->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $t->user->kelas }}</p>
                        </td>
                        <td class="py-2 px-3 max-w-xs truncate">{{ $t->buku->judul }}</td>
                        <td class="py-2 px-3 text-xs">{{ $t->tgl_pinjam->format('d/m/Y') }}</td>
                        <td class="py-2 px-3 text-xs">{{ ($t->tgl_kembali_aktual ?? $t->tgl_kembali_rencana)->format('d/m/Y') }}</td>
                        <td class="py-2 px-3 text-center">
                            @if($t->status === 'menunggu')
                                <span class="badge bg-amber-100 text-amber-700">Menunggu</span>
                            @elseif($t->status === 'dipinjam')
                                <span class="badge bg-blue-100 text-blue-700">Dipinjam</span>
                            @elseif($t->status === 'dikembalikan')
                                <span class="badge bg-green-100 text-green-700">Dikembalikan</span>
                            @elseif($t->status === 'ditolak')
                                <span class="badge bg-gray-200 text-gray-700">Ditolak</span>
                            @else
                                <span class="badge bg-red-100 text-red-700">Hilang</span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-right text-xs font-medium {{ $t->denda > 0 ? 'text-red-600' : 'text-gray-400' }}">
                            {{ $t->denda > 0 ? 'Rp '.number_format($t->denda,0,',','.') : '-' }}
                        </td>
                        <td class="py-2 px-3 text-center">
                            @if($t->denda > 0)
                                @if($t->status_denda === 'lunas')
                                    <span class="badge bg-green-100 text-green-700">
                                        <i class="fa-solid fa-check-circle text-[10px] mr-0.5"></i> Lunas
                                    </span>
                                @else
                                    <span class="badge bg-red-100 text-red-700">
                                        <i class="fa-solid fa-exclamation-circle text-[10px] mr-0.5"></i> Belum Lunas
                                    </span>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center py-12 text-gray-400">Tidak ada data untuk filter ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    aside, header, form, .btn-ghost, .btn-primary { display: none !important; }
    main { padding: 0 !important; }
    .card { box-shadow: none; border: 1px solid #e5e7eb; }
}
</style>
@endsection