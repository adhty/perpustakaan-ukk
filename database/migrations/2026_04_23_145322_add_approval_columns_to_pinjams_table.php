<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pinjams', function (Blueprint $table) {
            // Approval Pengembalian
            if (!Schema::hasColumn('pinjams', 'status_pengembalian')) {
                $table->enum('status_pengembalian', ['pending', 'disetujui', 'ditolak'])->nullable()->after('status');
            }
            if (!Schema::hasColumn('pinjams', 'tanggal_pengembalian_approve')) {
                $table->timestamp('tanggal_pengembalian_approve')->nullable()->after('status_pengembalian');
            }
            if (!Schema::hasColumn('pinjams', 'alasan_penolakan_pengembalian')) {
                $table->text('alasan_penolakan_pengembalian')->nullable()->after('tanggal_pengembalian_approve');
            }
            if (!Schema::hasColumn('pinjams', 'disetujui_oleh')) {
                $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete()->after('alasan_penolakan_pengembalian');
            }

            // Approval Denda
            if (!Schema::hasColumn('pinjams', 'bukti_pembayaran')) {
                $table->string('bukti_pembayaran')->nullable()->after('status_denda');
            }
            if (!Schema::hasColumn('pinjams', 'keterangan_denda')) {
                $table->text('keterangan_denda')->nullable()->after('bukti_pembayaran');
            }
            if (!Schema::hasColumn('pinjams', 'tanggal_bayar')) {
                $table->timestamp('tanggal_bayar')->nullable()->after('keterangan_denda');
            }
            if (!Schema::hasColumn('pinjams', 'tanggal_approve')) {
                $table->timestamp('tanggal_approve')->nullable()->after('tanggal_bayar');
            }
            if (!Schema::hasColumn('pinjams', 'approver_id')) {
                $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete()->after('tanggal_approve');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pinjams', function (Blueprint $table) {
            // Drop foreign keys first if they exist
            if (Schema::hasColumn('pinjams', 'disetujui_oleh')) {
                $table->dropForeign(['disetujui_oleh']);
            }
            if (Schema::hasColumn('pinjams', 'approver_id')) {
                $table->dropForeign(['approver_id']);
            }
            
            $table->dropColumn([
                'status_pengembalian',
                'tanggal_pengembalian_approve',
                'alasan_penolakan_pengembalian',
                'disetujui_oleh',
                'bukti_pembayaran',
                'keterangan_denda',
                'tanggal_bayar',
                'tanggal_approve',
                'approver_id'
            ]);
        });
    }
};
