<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pinjams', function (Blueprint $table) {
            $table->enum('status_denda', ['belum_lunas', 'lunas'])->default('belum_lunas')->after('denda');
        });
    }

    public function down(): void
    {
        Schema::table('pinjams', function (Blueprint $table) {
            $table->dropColumn('status_denda');
        });
    }
};