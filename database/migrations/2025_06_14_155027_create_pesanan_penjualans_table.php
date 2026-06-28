<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan_penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->foreignId('perusahaan_id')->constrained('perusahaans');
            $table->date('tanggal_pesanan');
            $table->date('tanggal_pengiriman')->nullable();
            $table->string('syarat_pembayaran')->nullable();
            $table->string('fob')->nullable();
            $table->string('ekspedisi')->nullable();
            $table->string('po_no')->nullable();
            $table->string('penjual')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('diskon_total', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('biaya_lain', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('terbilang')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_penjualans');
    }
};