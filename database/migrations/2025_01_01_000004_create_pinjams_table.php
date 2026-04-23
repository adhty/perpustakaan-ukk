<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjams', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pinjam', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('buku_id')->constrained('bukus')->restrictOnDelete();
            $table->date('tgl_pinjam');
            $table->date('tgl_kembali_rencana');
            $table->date('tgl_kembali_aktual')->nullable();
            $table->enum('status', [
                'menunggu',
                'dipinjam',
                'dikembalikan',
                'ditolak',
                'hilang',
                'rusak'
            ])->default('menunggu');
            $table->decimal('denda', 10, 2)->default(0);
            $table->enum('status_denda', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->text('keterangan')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('user_id');
            $table->index('buku_id');
            $table->index('status');
            $table->index('kode_pinjam');
            $table->index('tgl_pinjam');
            $table->index('tgl_kembali_rencana');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjams');
    }
};