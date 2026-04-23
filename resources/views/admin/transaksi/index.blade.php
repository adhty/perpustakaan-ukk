@extends('layouts.app')
@section('title', 'Manajemen Transaksi')
@section('page-title', 'Manajemen Transaksi')
@section('page-subtitle', 'Kelola semua transaksi peminjaman buku')

@section('content')
<div class="card mb-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-48">
            <label class="label">Cari</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fa-solid fa-search text-sm"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode, nama, judul buku..." class="input pl-9">
            </div>
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
                <option value="rusak" {{ request('status') === 'rusak' ? 'selected' : '' }}>Rusak</option>
            </select>
        </div>
        <div class="w-44">
            <label class="label">Tanggal</label>
            <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="input">
        </div>
        <div class="w-44">
            <label class="label">Bulan</label>
            <input type="month" name="bulan" value="{{ request('bulan') }}" class="input">
        </div>
        <button type="submit" class="btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
        @if(request()->hasAny(['search','status','tanggal','bulan']))
            <a href="{{ route('admin.transaksi.index') }}" class="btn-ghost">Reset</a>
        @endif
        <div class="flex-1"></div>
        <a href="{{ route('admin.transaksi.create') }}" class="btn-accent"><i class="fa-solid fa-plus"></i> Tambah Transaksi</a>
        <a href="{{ route('admin.laporan') }}" class="btn-ghost"><i class="fa-solid fa-chart-bar"></i> Laporan</a>
    </form>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 rounded-l-xl">Kode</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500">Peminjam</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500">Buku</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500">Tgl Pinjam</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500">Batas Kembali</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-gray-500">Status</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-gray-500">Status Denda</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-gray-500 rounded-r-xl">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($transaksis as $t)
                    @php $terlambat = $t->status === 'dipinjam' && \Carbon\Carbon::today()->gt($t->tgl_kembali_rencana); @endphp
                    <tr class="hover:bg-gray-50/50 {{ $terlambat ? 'bg-red-50/30' : '' }} {{ $t->status === 'menunggu' ? 'bg-amber-50/30' : '' }} transition">
                        <td class="py-3 px-4 font-mono text-xs text-gray-500">{{ $t->kode_pinjam }}</td>
                        <td class="py-3 px-4">
                            <p class="font-semibold text-gray-800">{{ $t->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $t->user->kelas }}</p>
                        </td>
                        <td class="py-3 px-4 text-gray-700 max-w-xs">
                            <p class="truncate font-medium">{{ $t->buku->judul }}</p>
                        </td>
                        <td class="py-3 px-4 text-gray-500 text-xs">{{ $t->tgl_pinjam->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 text-xs {{ $terlambat ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                            {{ $t->tgl_kembali_rencana->format('d/m/Y') }}
                            @if($terlambat) <span class="badge bg-red-100 text-red-700 ml-1">Terlambat</span> @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($t->status === 'menunggu')
                                <span class="badge bg-amber-100 text-amber-700">Menunggu</span>
                            @elseif($t->status === 'dipinjam')
                                <span class="badge bg-blue-100 text-blue-700">Dipinjam</span>
                            @elseif($t->status === 'dikembalikan')
                                <span class="badge bg-green-100 text-green-700">Dikembalikan</span>
                            @elseif($t->status === 'ditolak')
                                <span class="badge bg-gray-200 text-gray-700">Ditolak</span>
                            @elseif($t->status === 'hilang')
                                <span class="badge bg-red-100 text-red-700">Hilang</span>
                            @elseif($t->status === 'rusak')
                                <span class="badge bg-orange-100 text-orange-700">Rusak</span>
                            @else
                                <span class="badge bg-gray-100 text-gray-600">{{ ucfirst($t->status) }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
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
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.transaksi.show', $t) }}" class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.transaksi.edit', $t) }}" class="w-8 h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition" title="Edit">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </a>
                                @if($t->status === 'menunggu')
                                    <form action="{{ route('admin.transaksi.approve', $t->id) }}" method="POST"
                                          onsubmit="return confirm('Setujui request peminjaman ini?')">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition" title="Approve">
                                            <i class="fa-solid fa-check text-xs"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.transaksi.reject', $t->id) }}" method="POST"
                                          onsubmit="return confirm('Tolak request peminjaman ini?')">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition" title="Tolak">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($t->status === 'dipinjam')
                                    <form action="{{ route('admin.transaksi.kembalikan', $t->id) }}" method="POST"
                                          onsubmit="return confirm('Konfirmasi pengembalian buku ini?')">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition" title="Kembalikan">
                                            <i class="fa-solid fa-rotate-left text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.transaksi.destroy', $t) }}" method="POST"
                                      onsubmit="return confirm('Hapus transaksi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-16 text-gray-400">
                        <i class="fa-solid fa-arrow-right-arrow-left text-4xl mb-3 block opacity-30"></i>
                        <p>Belum ada transaksi</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $transaksis->links() }}</div>
</div>
@endsection