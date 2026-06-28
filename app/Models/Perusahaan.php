<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perusahaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'email',
        'npwp',
    ];

    /**
     * Mendefinisikan relasi one-to-many ke PesananPenjualan.
     * Satu perusahaan bisa memiliki banyak pesanan penjualan.
     */
    public function pesananPenjualans(): HasMany
    {
        return $this->hasMany(PesananPenjualan::class);
    }

    /**
     * Mendefinisikan relasi one-to-many ke SuratPenawaranHarga.
     * Satu perusahaan bisa memiliki banyak surat penawaran.
     */
    public function suratPenawaranHargas(): HasMany
    {
        return $this->hasMany(SuratPenawaranHarga::class);
    }

    /**
     * Mendefinisikan relasi one-to-many ke SuratJalan.
     * Satu perusahaan bisa memiliki banyak surat jalan.
     */
    public function suratJalans(): HasMany
    {
        return $this->hasMany(SuratJalan::class);
    }
}