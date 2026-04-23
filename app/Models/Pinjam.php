<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pinjam extends Model
{
    public const DENDA_TERLAMBAT_PER_HARI = 1000;
    public const DENDA_HILANG_DEFAULT = 50000;
    public const MAX_PINJAM_AKTIF = 3; // Maksimal pinjam 3 buku

    protected $table = 'pinjams';
    protected $fillable = [
        'kode_pinjam', 'user_id', 'buku_id', 'tgl_pinjam',
        'tgl_kembali_rencana', 'tgl_kembali_aktual',
        'status', 'denda', 'status_denda', 'keterangan', 'admin_id',
        // KOLOM BARU UNTUK APPROVAL PENGEMBALIAN
        'status_pengembalian',
        'tanggal_pengembalian_approve',
        'alasan_penolakan_pengembalian',
        'disetujui_oleh',
        // KOLOM BARU UNTUK APPROVE DENDA
        'bukti_pembayaran',
        'keterangan_denda',
        'tanggal_bayar',
        'tanggal_approve',
        'approver_id'
    ];

    protected $casts = [
        'tgl_pinjam'           => 'date',
        'tgl_kembali_rencana'  => 'date',
        'tgl_kembali_aktual'   => 'date',
        'denda'                => 'decimal:2',
        'tanggal_pengembalian_approve' => 'datetime',
        'tanggal_bayar'        => 'datetime',
        'tanggal_approve'      => 'datetime',
    ];

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // RELASI KE ADMIN YANG MENYETUJUI PENGEMBALIAN
    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // RELASI KE ADMIN YANG MENYETUJUI DENDA
    public function dendaApprover()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // Accessor: hitung denda otomatis (Rp 1.000/hari) untuk terlambat
    // Dan denda manual untuk rusak/hilang
    public function getDendaHitungAttribute(): float
    {
        // Jika status hilang, pakai denda manual (minimal 50rb)
        if ($this->status === 'hilang') {
            return max((float) $this->denda, (float) self::DENDA_HILANG_DEFAULT);
        }

        // Jika status rusak, pakai denda manual
        if ($this->status === 'rusak') {
            return (float) $this->denda;
        }

        // Jika masih dipinjam dan terlambat, hitung otomatis
        if ($this->status === 'dipinjam') {
            $batas = $this->tgl_kembali_rencana;
            $sekarang = Carbon::today();
            if ($sekarang->gt($batas)) {
                $hari = $sekarang->diffInDays($batas);
                return $hari * self::DENDA_TERLAMBAT_PER_HARI;
            }
        }

        return (float) $this->denda;
    }

    public function getTerlambatAttribute(): bool
    {
        return $this->status === 'dipinjam'
            && Carbon::today()->gt($this->tgl_kembali_rencana);
    }

    public function getSisaHariAttribute(): int
    {
        if ($this->status !== 'dipinjam') return 0;
        $diff = Carbon::today()->diffInDays($this->tgl_kembali_rencana, false);
        return (int) $diff;
    }

    // HITUNG HARI TERLAMBAT (POSITIF JIKA TERLAMBAT)
    public function getHariTerlambatAttribute(): int
    {
        if (!$this->terlambat) return 0;
        return abs($this->sisa_hari);
    }

    // CEK APAKAH SUDAH REQUEST PENGEMBALIAN
    public function getIsRequestReturnAttribute(): bool
    {
        return $this->status_pengembalian === 'pending';
    }

    // CEK APAKAH PENGEMBALIAN SUDAH DISETUJUI
    public function getIsReturnApprovedAttribute(): bool
    {
        return $this->status_pengembalian === 'disetujui';
    }

    // CEK APAKAH PENGEMBALIAN DITOLAK
    public function getIsReturnRejectedAttribute(): bool
    {
        return $this->status_pengembalian === 'ditolak';
    }

    // ========== METHOD UNTUK STATUS DENDA ==========
    
    // Cek apakah denda perlu dibayar
    public function hasDenda(): bool
    {
        return $this->denda > 0 && $this->status_denda !== 'lunas';
    }

    // Cek apakah denda masih menunggu approve
    public function isDendaMenunggu(): bool
    {
        return $this->status_denda === 'menunggu';
    }

    // Cek apakah denda sudah lunas
    public function isDendaLunas(): bool
    {
        return $this->status_denda === 'lunas';
    }

    // Cek apakah denda belum lunas
    public function isDendaBelumLunas(): bool
    {
        return $this->denda > 0 && $this->status_denda === 'belum_lunas';
    }

    // Approve denda oleh admin
    public function approveDenda($adminId, $keterangan = null): bool
    {
        if ($this->status_denda !== 'menunggu') {
            return false;
        }

        $this->status_denda = 'lunas';
        $this->tanggal_approve = Carbon::now();
        $this->approver_id = $adminId;
        $this->keterangan_denda = $keterangan ?? 'Denda disetujui admin';
        
        return $this->save();
    }

    // Tolak denda oleh admin
    public function rejectDenda($adminId, $alasan): bool
    {
        if ($this->status_denda !== 'menunggu') {
            return false;
        }

        $this->status_denda = 'belum_lunas';
        $this->keterangan_denda = $alasan;
        $this->approver_id = $adminId;
        $this->tanggal_bayar = null;
        
        return $this->save();
    }

    // Bayar denda oleh siswa (menunggu konfirmasi admin)
    public function bayarDenda($keterangan = null): bool
    {
        if ($this->denda <= 0) {
            return false;
        }

        if ($this->status_denda !== 'belum_lunas') {
            return false;
        }

        $this->status_denda = 'menunggu';
        $this->tanggal_bayar = Carbon::now();
        $this->keterangan_denda = $keterangan ?? 'Pembayaran denda menunggu konfirmasi admin';
        
        return $this->save();
    }

    // Batalkan pembayaran denda
    public function batalBayarDenda(): bool
    {
        if ($this->status_denda !== 'menunggu') {
            return false;
        }

        $this->status_denda = 'belum_lunas';
        $this->keterangan_denda = null;
        $this->tanggal_bayar = null;
        
        return $this->save();
    }

    // Generate kode pinjam unik
    public static function generateKode(): string
    {
        $prefix = 'PJM-' . date('Ymd') . '-';
        $last = self::where('kode_pinjam', 'like', $prefix . '%')
                    ->latest('id')->first();
        $seq = $last ? (intval(substr($last->kode_pinjam, -4)) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    // ========== METHOD UNTUK VALIDASI LIMIT PINJAM ==========
    
    /**
     * Hitung jumlah pinjaman aktif user (menunggu + dipinjam)
     */
    public static function countAktifByUser($userId): int
    {
        return self::where('user_id', $userId)
            ->whereIn('status', ['menunggu', 'dipinjam'])
            ->count();
    }

    /**
     * Cek apakah user masih bisa pinjam (belum mencapai limit 3)
     */
    public static function canPinjam($userId): bool
    {
        return self::countAktifByUser($userId) < self::MAX_PINJAM_AKTIF;
    }

    /**
     * Hitung sisa kuota pinjam user
     */
    public static function sisaKuota($userId): int
    {
        $aktif = self::countAktifByUser($userId);
        return max(0, self::MAX_PINJAM_AKTIF - $aktif);
    }

    /**
     * Cek apakah user memiliki denda yang belum dibayar (belum_lunas atau menunggu)
     */
    public static function hasDendaBelumLunas($userId): bool
    {
        return self::where('user_id', $userId)
            ->where('denda', '>', 0)
            ->whereIn('status_denda', ['belum_lunas', 'menunggu'])
            ->exists();
    }

    /**
     * Cek apakah user memiliki denda yang menunggu konfirmasi admin
     */
    public static function hasDendaMenunggu($userId): bool
    {
        return self::where('user_id', $userId)
            ->where('denda', '>', 0)
            ->where('status_denda', 'menunggu')
            ->exists();
    }

    /**
     * Hitung total denda yang belum dibayar user (belum_lunas + menunggu)
     */
    public static function totalDendaBelumLunas($userId): float
    {
        return (float) self::where('user_id', $userId)
            ->where('denda', '>', 0)
            ->whereIn('status_denda', ['belum_lunas', 'menunggu'])
            ->sum('denda');
    }

    /**
     * Hitung total denda yang menunggu konfirmasi
     */
    public static function totalDendaMenunggu($userId): float
    {
        return (float) self::where('user_id', $userId)
            ->where('denda', '>', 0)
            ->where('status_denda', 'menunggu')
            ->sum('denda');
    }

    // ========== METHOD UNTUK ADMIN (MANUAL DENDA) ==========
    
    /**
     * Set status hilang dengan denda manual
     */
    public function setHilang($adminId, $dendaManual = null): bool
    {
        if (!in_array($this->status, ['dipinjam', 'menunggu'])) {
            return false;
        }

        $this->status = 'hilang';
        $this->admin_id = $adminId;
        $this->denda = $dendaManual ?? self::DENDA_HILANG_DEFAULT;
        $this->status_denda = 'belum_lunas';
        
        // Kurangi stok buku (karena hilang)
        if ($this->buku->stok_tersedia > 0) {
            $this->buku->decrement('stok_tersedia');
        }
        
        return $this->save();
    }

    /**
     * Set status rusak dengan denda manual
     */
    public function setRusak($adminId, $dendaManual): bool
    {
        if (!in_array($this->status, ['dipinjam', 'menunggu'])) {
            return false;
        }

        $this->status = 'rusak';
        $this->admin_id = $adminId;
        $this->denda = $dendaManual;
        $this->status_denda = 'belum_lunas';
        
        return $this->save();
    }

    /**
     * Approve peminjaman (admin setujui)
     */
    public function approve($adminId): bool
    {
        if ($this->status !== 'menunggu') {
            return false;
        }

        $this->status = 'dipinjam';
        $this->admin_id = $adminId;
        
        // Kurangi stok buku
        $this->buku->decrement('stok_tersedia');
        
        return $this->save();
    }

    /**
     * Tolak peminjaman
     */
    public function reject($adminId, $keterangan = null): bool
    {
        if ($this->status !== 'menunggu') {
            return false;
        }

        $this->status = 'ditolak';
        $this->admin_id = $adminId;
        if ($keterangan) {
            $this->keterangan = $keterangan;
        }
        
        return $this->save();
    }

    /**
     * Proses pengembalian buku (denda otomatis jika terlambat)
     * METHOD LAMA - TANPA APPROVAL
     */
    public function kembalikan($adminId): bool
    {
        if ($this->status !== 'dipinjam') {
            return false;
        }

        $this->tgl_kembali_aktual = Carbon::today();
        $this->status = 'dikembalikan';
        $this->admin_id = $adminId;
        
        // Hitung denda otomatis jika terlambat
        if ($this->terlambat) {
            $hariTerlambat = Carbon::today()->diffInDays($this->tgl_kembali_rencana);
            $this->denda = $hariTerlambat * self::DENDA_TERLAMBAT_PER_HARI;
            $this->status_denda = 'belum_lunas';
        } else {
            $this->status_denda = 'lunas';
        }
        
        // Tambah stok buku kembali
        $this->buku->increment('stok_tersedia');
        
        return $this->save();
    }

    /**
     * METHOD BARU: Request pengembalian oleh siswa
     */
    public function requestReturn(): bool
    {
        if ($this->status !== 'dipinjam') {
            return false;
        }

        // Jika sudah request, tidak bisa request lagi
        if ($this->status_pengembalian === 'pending') {
            return false;
        }

        $this->status_pengembalian = 'pending';
        $this->keterangan = 'Request pengembalian buku - ' . now();
        
        return $this->save();
    }

    /**
     * METHOD BARU: Batalkan request pengembalian oleh siswa
     */
    public function cancelReturnRequest(): bool
    {
        if ($this->status !== 'dipinjam') {
            return false;
        }

        // Hanya bisa batalkan jika statusnya pending
        if ($this->status_pengembalian !== 'pending') {
            return false;
        }

        $this->status_pengembalian = null;
        $this->keterangan = 'Request pengembalian dibatalkan - ' . now();
        
        return $this->save();
    }

    /**
     * METHOD BARU: Approve pengembalian oleh admin
     */
    public function approveReturn($adminId): bool
    {
        if ($this->status !== 'dipinjam') {
            return false;
        }

        // Hanya bisa approve jika statusnya pending
        if ($this->status_pengembalian !== 'pending') {
            return false;
        }

        $this->tgl_kembali_aktual = Carbon::today();
        $this->status = 'dikembalikan';
        $this->status_pengembalian = 'disetujui';
        $this->admin_id = $adminId;
        $this->disetujui_oleh = $adminId;
        $this->tanggal_pengembalian_approve = Carbon::now();
        
        // Hitung denda otomatis jika terlambat
        if ($this->terlambat) {
            $hariTerlambat = Carbon::today()->diffInDays($this->tgl_kembali_rencana);
            $this->denda = $hariTerlambat * self::DENDA_TERLAMBAT_PER_HARI;
            $this->status_denda = 'belum_lunas';
        } else {
            $this->status_denda = 'lunas';
        }
        
        // Tambah stok buku kembali
        $this->buku->increment('stok_tersedia');
        
        $this->keterangan = 'Pengembalian disetujui oleh admin - ' . now();
        
        return $this->save();
    }

    /**
     * METHOD BARU: Tolak pengembalian oleh admin
     */
    public function rejectReturn($adminId, $alasan): bool
    {
        if ($this->status !== 'dipinjam') {
            return false;
        }

        // Hanya bisa tolak jika statusnya pending
        if ($this->status_pengembalian !== 'pending') {
            return false;
        }

        $this->status_pengembalian = 'ditolak';
        $this->disetujui_oleh = $adminId;
        $this->alasan_penolakan_pengembalian = $alasan;
        $this->keterangan = 'Pengembalian ditolak: ' . $alasan;
        
        return $this->save();
    }

    // ========== SCOPES ==========
    
    /**
     * Scope untuk pinjaman aktif (menunggu + dipinjam)
     */
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['menunggu', 'dipinjam']);
    }

    /**
     * Scope untuk pinjaman yang sudah selesai
     */
    public function scopeSelesai($query)
    {
        return $query->whereIn('status', ['dikembalikan', 'ditolak', 'hilang', 'rusak']);
    }

    /**
     * Scope untuk pinjaman terlambat
     */
    public function scopeTerlambat($query)
    {
        return $query->where('status', 'dipinjam')
            ->where('tgl_kembali_rencana', '<', Carbon::today());
    }

    /**
     * Scope untuk pinjaman yang memiliki denda belum lunas atau menunggu
     */
    public function scopeDendaBelumLunas($query)
    {
        return $query->where('denda', '>', 0)
            ->whereIn('status_denda', ['belum_lunas', 'menunggu']);
    }

    /**
     * Scope untuk pinjaman yang memiliki denda menunggu konfirmasi
     */
    public function scopeDendaMenunggu($query)
    {
        return $query->where('denda', '>', 0)
            ->where('status_denda', 'menunggu');
    }

    /**
     * Scope untuk pinjaman yang memiliki denda sudah lunas
     */
    public function scopeDendaLunas($query)
    {
        return $query->where('denda', '>', 0)
            ->where('status_denda', 'lunas');
    }

    /**
     * SCOPE BARU: Request pengembalian yang pending
     */
    public function scopePendingReturn($query)
    {
        return $query->where('status', 'dipinjam')
            ->where('status_pengembalian', 'pending');
    }

    /**
     * SCOPE BARU: Request pengembalian yang sudah disetujui
     */
    public function scopeApprovedReturn($query)
    {
        return $query->where('status_pengembalian', 'disetujui');
    }

    /**
     * SCOPE BARU: Request pengembalian yang ditolak
     */
    public function scopeRejectedReturn($query)
    {
        return $query->where('status_pengembalian', 'ditolak');
    }
}