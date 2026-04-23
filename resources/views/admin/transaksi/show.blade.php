@extends('layouts.app')
@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')
@section('page-subtitle', $transaksi->kode_pinjam)

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="flex items-start justify-between mb-6">
            <div>
                <p class="font-mono text-xs text-gray-400 mb-1">{{ $transaksi->kode_pinjam }}</p>
                @if($transaksi->status === 'menunggu')
                    <span class="badge bg-amber-100 text-amber-700 text-sm">Menunggu Persetujuan</span>
                @elseif($transaksi->status === 'dipinjam')
                    <span class="badge bg-blue-100 text-blue-700 text-sm">Sedang Dipinjam</span>
                @elseif($transaksi->status === 'ditolak')
                    <span class="badge bg-gray-200 text-gray-700 text-sm">Ditolak</span>
                @elseif($transaksi->status === 'hilang')
                    <span class="badge bg-red-100 text-red-700 text-sm">Buku Hilang</span>
                @elseif($transaksi->status === 'rusak')
                    <span class="badge bg-orange-100 text-orange-700 text-sm">Buku Rusak</span>
                @else
                    <span class="badge bg-green-100 text-green-700 text-sm">Sudah Dikembalikan</span>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.transaksi.edit', $transaksi) }}" class="btn-ghost">
                    <i class="fa-solid fa-pen"></i> Edit
                </a>
                @if($transaksi->status === 'dipinjam')
                    <form action="{{ route('admin.transaksi.kembalikan', $transaksi->id) }}" method="POST"
                          onsubmit="return confirm('Konfirmasi pengembalian buku ini?')">
                        @csrf
                        <button type="submit" class="btn-accent">
                            <i class="fa-solid fa-rotate-left"></i> Proses Pengembalian
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div class="space-y-4">
                <h4 class="font-semibold text-gray-700 text-sm border-b pb-2">Data Peminjam</h4>
                @foreach(['Nama' => $transaksi->user->name, 'NIS' => $transaksi->user->nis, 'Kelas' => $transaksi->user->kelas, 'No. HP' => ($transaksi->user->no_hp ?? '-')] as $k => $v)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">{{ $k }}</span>
                        <span class="font-medium text-gray-800">{{ $v }}</span>
                    </div>
                @endforeach
            </div>

            <div class="space-y-4">
                <h4 class="font-semibold text-gray-700 text-sm border-b pb-2">Data Buku</h4>
                @foreach(['Judul' => $transaksi->buku->judul, 'Pengarang' => $transaksi->buku->pengarang, 'Kategori' => $transaksi->buku->kategori->nama_kategori, 'Kode' => $transaksi->buku->kode_buku] as $k => $v)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">{{ $k }}</span>
                        <span class="font-medium text-gray-800 text-right max-w-32 truncate">{{ $v }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded-xl space-y-3 text-sm">
            <h4 class="font-semibold text-gray-700 border-b border-gray-200 pb-2">Detail Peminjaman</h4>
            @foreach([
                'Tanggal Pinjam'   => $transaksi->tgl_pinjam->isoFormat('D MMMM Y'),
                'Batas Kembali'    => $transaksi->tgl_kembali_rencana->isoFormat('D MMMM Y'),
                'Tgl Kembali Aktual' => ($transaksi->tgl_kembali_aktual ? $transaksi->tgl_kembali_aktual->isoFormat('D MMMM Y') : '-'),
                'Diproses oleh'    => ($transaksi->admin ? $transaksi->admin->name : 'Siswa'),
            ] as $k => $v)
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ $k }}</span>
                    <span class="font-medium text-gray-800">{{ $v }}</span>
                </div>
            @endforeach
            
            {{-- Denda --}}
            <div class="flex justify-between pt-2">
                <span class="font-semibold text-gray-700">Total Denda</span>
                <span class="font-bold {{ $transaksi->denda > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ $transaksi->denda > 0 ? 'Rp ' . number_format($transaksi->denda,0,',','.') : 'Tidak ada denda' }}
                </span>
            </div>
            
            {{-- Status Denda (TAMBAHKAN) --}}
            @if($transaksi->denda > 0)
                <div class="flex justify-between border-t border-gray-200 pt-2 mt-1">
                    <span class="font-semibold text-gray-700">Status Denda</span>
                    <span>
                        @if($transaksi->status_denda === 'lunas')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <i class="fa-solid fa-check-circle text-[9px]"></i> LUNAS
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                <i class="fa-solid fa-exclamation-circle text-[9px]"></i> BELUM LUNAS
                            </span>
                        @endif
                    </span>
                </div>
            @endif
        </div>

        @if($transaksi->keterangan)
            <div class="mt-4 p-3 bg-blue-50 rounded-xl text-sm text-blue-800">
                <span class="font-semibold">Keterangan:</span> {{ $transaksi->keterangan }}
            </div>
        @endif

        <div class="mt-5 flex gap-3">
            <a href="{{ route('admin.transaksi.index') }}" class="btn-ghost">← Kembali</a>
        </div>
    </div>
</div>
@endsection