<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pinjams', function (Blueprint $table) {
            if (!Schema::hasColumn('pinjams', 'status_denda')) {
                $table->string('status_denda')->default('belum_lunas');
            }
            if (!Schema::hasColumn('pinjams', 'tanggal_bayar')) {
                $table->timestamp('tanggal_bayar')->nullable();
            }
            if (!Schema::hasColumn('pinjams', 'tanggal_approve')) {
                $table->timestamp('tanggal_approve')->nullable();
            }
            if (!Schema::hasColumn('pinjams', 'approver_id')) {
                $table->unsignedBigInteger('approver_id')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('pinjams', function (Blueprint $table) {
            $table->dropColumn(['status_denda', 'tanggal_bayar', 'tanggal_approve', 'approver_id']);
        });
    }
};