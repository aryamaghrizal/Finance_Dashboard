<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faktur_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faktur_penjualan_id')->constrained('faktur_penjualans')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs');
            $table->integer('kuantitas');
            $table->decimal('harga', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_items');
    }
};