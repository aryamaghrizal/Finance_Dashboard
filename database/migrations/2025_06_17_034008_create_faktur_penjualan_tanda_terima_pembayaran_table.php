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
        Schema::create('faktur_penjualan_tanda_terima_pembayaran', function (Blueprint $table) {
            $table->id();

            // Foreign Key ke Tanda Terima Pembayaran dengan nama kustom pendek
            $table->foreignId('tanda_terima_pembayaran_id');
            $table->foreign('tanda_terima_pembayaran_id', 'faktur_ttp_ttp_id_foreign')
                  ->references('id')
                  ->on('tanda_terima_pembayarans')
                  ->cascadeOnDelete();

            // Foreign Key ke Faktur Penjualan dengan nama kustom pendek
            $table->foreignId('faktur_penjualan_id');
            $table->foreign('faktur_penjualan_id', 'faktur_ttp_fp_id_foreign')
                  ->references('id')
                  ->on('faktur_penjualans')
                  ->cascadeOnDelete();
            
            // --- KOLOM PENTING YANG HILANG, KINI DITAMBAHKAN KEMBALI ---
            $table->decimal('jumlah_dibayar_untuk_faktur_ini', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faktur_penjualan_tanda_terima_pembayaran');
    }
};