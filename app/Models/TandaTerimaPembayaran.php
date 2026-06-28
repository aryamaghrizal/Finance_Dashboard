<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TandaTerimaPembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pembayaran',
        'perusahaan_id',
        'tanggal_pembayaran',
        'pembayaran',
        'bank',
        'tanggal_cek',
        'nomor_cek',
        'terbilang',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'tanggal_cek' => 'date',
    ];

    /**
     * Otomatis buat nomor jika kosong.
     */
    protected static function booted(): void
    {
        static::creating(function (self $tandaTerima) {
            if (!$tandaTerima->nomor_pembayaran) {
                $year = now()->year;
                $month = now()->format('m');
                $count = self::whereYear('created_at', $year)->whereMonth('created_at', $month)->count() + 1;
                $tandaTerima->nomor_pembayaran = "TTP.$year.$month." . str_pad($count, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function fakturPenjualans()
    {
        return $this->belongsToMany(FakturPenjualan::class, 'faktur_penjualan_tanda_terima_pembayaran');
    }
}