<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $table = 'bukus';

    protected $fillable = [
        'kode_buku',
        'judul',
        'pengarang',
        'penerbit',
        'tahun_terbit',
        'kategori_id',
        'stok',
        'stok_tersedia',
        'isbn',
        'deskripsi',
        'sampul',
        'rak',
    ];

    /**
     * =========================
     * RELATION
     * =========================
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function pinjams()
    {
        return $this->hasMany(Pinjam::class, 'buku_id');
    }

    /**
     * =========================
     * ACCESSOR
     * =========================
     */
    public function getTersediaAttribute(): bool
    {
        return $this->stok_tersedia > 0;
    }

    /**
     * =========================
     * SCOPE SEARCH
     * =========================
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('judul', 'like', "%{$keyword}%")
              ->orWhere('pengarang', 'like', "%{$keyword}%")
              ->orWhere('kode_buku', 'like', "%{$keyword}%")
              ->orWhere('isbn', 'like', "%{$keyword}%");
        });
    }

    /**
     * =========================
     * AUTO FORMAT (OPTIONAL UPGRADE)
     * =========================
     */

    // Otomatis trim input biar bersih
    public function setJudulAttribute($value)
    {
        $this->attributes['judul'] = trim($value);
    }

    public function setPengarangAttribute($value)
    {
        $this->attributes['pengarang'] = trim($value);
    }

    public function setPenerbitAttribute($value)
    {
        $this->attributes['penerbit'] = trim($value);
    }

    public function setKodeBukuAttribute($value)
    {
        $this->attributes['kode_buku'] = strtoupper(trim($value));
    }

    /**
     * =========================
     * HELPER
     * =========================
     */

    // Status teks (biar gampang di blade)
    public function getStatusAttribute(): string
    {
        return $this->stok_tersedia > 0 ? 'Tersedia' : 'Habis';
    }
}