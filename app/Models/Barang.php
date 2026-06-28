<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'harga',
        'stok',
    ];

    /**
     * Mendefinisikan relasi one-to-many ke PenawaranItem.
     * Satu barang bisa ada di banyak item penawaran.
     */
    public function penawaranItems(): HasMany
    {
        return $this->hasMany(PenawaranItem::class);
    }

    /**
     * Mendefinisikan relasi one-to-many ke PesananItem.
     * Satu barang bisa ada di banyak item pesanan.
     */
    public function pesananItems(): HasMany
    {
        return $this->hasMany(PesananItem::class);
    }

    /**
     * Mendefinisikan relasi one-to-many ke SuratJalanItem.
     * Satu barang bisa ada di banyak item surat jalan.
     */
    public function suratJalanItems(): HasMany
    {
        return $this->hasMany(SuratJalanItem::class);
    }
}