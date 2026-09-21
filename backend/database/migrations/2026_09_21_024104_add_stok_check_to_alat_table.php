<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalisasi data lama ke lowercase sebelum constraint diterapkan
        DB::table('alat')->update([
            'status_kondisi' => DB::raw('LOWER(status_kondisi)'),
        ]);

        // Kembalikan stok negatif (data korup dari bug double-decrement) ke 0
        DB::table('alat')->where('stok', '<', 0)->update(['stok' => 0]);

        // Sintaks CHECK beda: SQLite tidak bisa ALTER ADD CONSTRAINT dan rebuild tabel
        // (rename swap) merusak FK reference dari detail_pinjam di dalam transaksi.
        // SQLite cuma dipakai untuk test → guard stokCukup() + lockForUpdate di service
        // sudah jadi pertahanan. CHECK constraint asli cuma dipasang di MySQL (produksi).
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE alat ADD CONSTRAINT alat_stok_check CHECK (stok >= 0)');
        DB::statement(
            'ALTER TABLE alat ADD CONSTRAINT alat_status_kondisi_check '.
            'CHECK (status_kondisi IN (\'baik\', \'rusak\', \'perbaikan\'))'
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE alat DROP CONSTRAINT alat_status_kondisi_check');
        DB::statement('ALTER TABLE alat DROP CONSTRAINT alat_stok_check');
    }
};
