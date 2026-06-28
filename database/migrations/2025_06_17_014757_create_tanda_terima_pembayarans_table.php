<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tanda_terima_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->foreignId('perusahaan_id')->constrained('perusahaans');
            $table->date('tanggal_pembayaran');
            $table->decimal('pembayaran', 15, 2)->comment('Total uang yang diterima');
            
            // Informasi Cek/Giro (opsional)
            $table->string('bank')->nullable();
            $table->date('tanggal_cek')->nullable();
            $table->string('nomor_cek')->nullable();

            $table->string('terbilang')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanda_terima_pembayarans');
    }
};