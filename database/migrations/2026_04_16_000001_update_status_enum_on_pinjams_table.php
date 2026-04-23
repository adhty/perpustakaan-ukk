<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE pinjams SET status = 'dipinjam' WHERE status = 'terlambat'");

        DB::statement(
            "ALTER TABLE pinjams MODIFY COLUMN status ENUM('menunggu','dipinjam','dikembalikan','ditolak','hilang') NOT NULL DEFAULT 'menunggu'"
        );
    }

    public function down(): void
    {
        DB::statement("UPDATE pinjams SET status = 'dipinjam' WHERE status IN ('menunggu','ditolak','hilang')");

        DB::statement(
            "ALTER TABLE pinjams MODIFY COLUMN status ENUM('dipinjam','dikembalikan','terlambat') NOT NULL DEFAULT 'dipinjam'"
        );
    }
};
