<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penawaran_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_penawaran_harga_id')->constrained('surat_penawaran_hargas')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs');
            $table->integer('kuantitas');
            $table->decimal('harga', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penawaran_items');
    }
};