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
        // Kita menggunakan Schema::table() karena hanya mengubah tabel yang sudah ada
        Schema::table('faktur_penjualans', function (Blueprint $table) {
            // Menambahkan kolom 'status' setelah kolom 'total'
            // Defaultnya adalah 'unpaid' (Belum Lunas)
            $table->string('status')->default('unpaid')->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faktur_penjualans', function (Blueprint $table) {
            // Logika untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn('status');
        });
    }
};