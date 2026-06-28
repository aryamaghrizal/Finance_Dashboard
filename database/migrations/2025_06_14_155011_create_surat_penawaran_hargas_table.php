<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_penawaran_hargas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();
            $table->foreignId('perusahaan_id')->constrained('perusahaans');
            $table->date('tanggal');
            $table->string('pembayaran');
            $table->boolean('ppn')->default(false);
            $table->decimal('biaya_lain', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_penawaran_hargas');
    }
};