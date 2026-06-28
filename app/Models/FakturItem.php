<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FakturItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'faktur_penjualan_id',
        'barang_id',
        'kuantitas',
        'harga',
        'diskon',
        'total_harga',
    ];

    public function pesanan()
    {
        return $this->belongsTo(FakturPenjualan::class, 'faktur_penjualan_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}