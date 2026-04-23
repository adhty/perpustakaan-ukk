<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom status dari ENUM menjadi VARCHAR
        Schema::table('pinjams', function (Blueprint $table) {
            $table->string('status', 20)->default('dipinjam')->change();
        });
        
        // Update data yang ada
        DB::table('pinjams')->where('status', 'menunggu')->update(['status' => 'dipinjam']);
        DB::table('pinjams')->where('status', 'dikembalikan')->update(['status' => 'rusak']);
        DB::table('pinjams')->where('status', 'ditolak')->update(['status' => 'rusak']);
    }

    public function down(): void
    {
        // Kembalikan ke ENUM (opsional)
        Schema::table('pinjams', function (Blueprint $table) {
            $table->enum('status', [
                'menunggu',
                'dipinjam',
                'dikembalikan',
                'ditolak',
                'hilang',
                'rusak'
            ])->default('menunggu')->change();
        });
    }
};