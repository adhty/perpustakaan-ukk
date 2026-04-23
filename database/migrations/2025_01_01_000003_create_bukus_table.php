<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_buku', 30)->unique();
            $table->string('judul', 200);
            $table->string('pengarang', 100);
            $table->string('penerbit', 100);
            $table->year('tahun_terbit');
            $table->foreignId('kategori_id')->constrained('kategoris')->restrictOnDelete();
            $table->integer('stok')->default(1);
            $table->integer('stok_tersedia')->default(1);
            $table->string('isbn', 30)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('sampul', 255)->nullable();
            $table->string('rak', 20)->nullable()->comment('Lokasi rak buku');
            $table->timestamps();

            $table->index('kode_buku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukus');
    }
};
