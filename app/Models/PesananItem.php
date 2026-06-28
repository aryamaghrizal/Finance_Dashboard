<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesananItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_penjualan_id',
        'barang_id',
        'kuantitas',
        'harga',
        'diskon',
        'total_harga',
    ];

    public function pesanan()
    {
        return $this->belongsTo(PesananPenjualan::class, 'pesanan_penjualan_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}