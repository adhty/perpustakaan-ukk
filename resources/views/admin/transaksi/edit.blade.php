@extends('layouts.app')
@section('title', 'Edit Transaksi')
@section('page-title', 'Edit Transaksi')
@section('page-subtitle', 'Kelola transaksi dan denda kerusakan')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-5 p-4 bg-gray-50 rounded-xl text-sm">
            <p class="font-semibold text-gray-700">{{ $transaksi->kode_pinjam }}</p>
            <p class="text-gray-600">{{ $transaksi->user->name }} - {{ $transaksi->buku->judul }}</p>
        </div>

        <form action="{{ route('admin.transaksi.update', $transaksi) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Status *</label>
                    <select name="status" id="statusSelect" class="input" required>
                        <option value="hilang" {{ old('status', $transaksi->status) === 'hilang' ? 'selected' : '' }}>Hilang</option>
                        <option value="rusak" {{ old('status', $transaksi->status) === 'rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Hanya tersedia status Hilang atau Rusak</p>
                </div>
                
                {{-- Informasi Denda Otomatis (Readonly) --}}
                <div>
                    <label class="label">Denda Otomatis (Rp)</label>
                    <input type="text" id="dendaOtomatis" class="input bg-gray-100 cursor-not-allowed" readonly>
                    <input type="hidden" name="denda" id="dendaHidden">
                    <p class="text-xs text-blue-600 mt-1">
                        <i class="fa-solid fa-info-circle"></i> Denda otomatis: Hilang = Rp 40.000 | Rusak = Rp 20.000
                    </p>
                </div>

                {{-- TAMPILKAN STATUS DENDA SAAT INI --}}
                @if($transaksi->denda > 0)
                <div class="col-span-2">
                    <div class="bg-gray-50 rounded-xl p-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-700">Status Denda Saat Ini:</span>
                            @if($transaksi->status_denda === 'lunas')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    <i class="fa-solid fa-check-circle text-[9px]"></i> LUNAS
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    <i class="fa-solid fa-exclamation-circle text-[9px]"></i> BELUM LUNAS
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Denda: <strong class="text-red-600">Rp {{ number_format($transaksi->denda, 0, ',', '.') }}</strong></p>
                    </div>
                </div>
                @endif
                
                <div class="col-span-2">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-sm text-blue-800">
                        <p class="font-semibold mb-1"><i class="fa-solid fa-clock"></i> Informasi Waktu:</p>
                        <p>📅 Tanggal Pinjam: <strong>{{ $transaksi->tgl_pinjam->format('d/m/Y') }}</strong></p>
                        <p>⏰ Batas Pengembalian: <strong>{{ $transaksi->tgl_kembali_rencana->format('d/m/Y') }}</strong></p>
                        @if($transaksi->tgl_kembali_aktual)
                            <p>✅ Tanggal Kembali Aktual: <strong>{{ $transaksi->tgl_kembali_aktual->format('d/m/Y') }}</strong></p>
                        @endif
                    </div>
                </div>

                <div class="col-span-2">
                    <label class="label">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="input resize-none" placeholder="Catatan admin tentang kondisi buku, kerusakan, atau informasi tambahan lainnya">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                <p class="font-semibold mb-1"><i class="fa-solid fa-circle-info mr-1"></i>Aturan Sistem:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>✅ Transaksi hanya dapat diubah statusnya menjadi <strong>HILANG</strong> atau <strong>RUSAK</strong></li>
                    <li>✅ Denda OTOMATIS: <strong class="text-red-600">Hilang = Rp 40.000</strong> | <strong class="text-red-600">Rusak = Rp 20.000</strong></li>
                    <li>✅ Admin TIDAK BISA mengubah nominal denda</li>
                    <li>✅ Tanggal pinjam, batas kembali, dan tanggal kembali aktual TIDAK BISA diubah admin</li>
                    <li>✅ Status menunggu, dipinjam, dikembalikan, ditolak telah dihapus dari sistem</li>
                </ul>
            </div>

            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.transaksi.index') }}" class="btn-ghost">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('statusSelect');
        const dendaOtomatisInput = document.getElementById('dendaOtomatis');
        const dendaHidden = document.getElementById('dendaHidden');
        
        // Fungsi untuk mengatur denda otomatis berdasarkan status
        function setDendaOtomatis() {
            const status = statusSelect.value;
            let nominal = 0;
            let displayText = '';
            
            if (status === 'hilang') {
                nominal = 40000;
                displayText = 'Rp 40.000 (Hilang)';
            } else if (status === 'rusak') {
                nominal = 20000;
                displayText = 'Rp 20.000 (Rusak)';
            }
            
            dendaOtomatisInput.value = displayText;
            dendaHidden.value = nominal;
        }
        
        // Set initial value
        setDendaOtomatis();
        
        // Update saat status berubah
        statusSelect.addEventListener('change', setDendaOtomatis);
    });
</script>
@endsection