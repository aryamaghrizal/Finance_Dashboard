<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PesananPenjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_surat',
        'perusahaan_id',
        'tanggal_pesanan',
        'tanggal_pengiriman',
        'syarat_pembayaran',
        'fob',
        'ekspedisi',
        'po_no',
        'penjual',
        'subtotal',
        'diskon_total',
        'ppn',
        'biaya_lain',
        'total',
        'terbilang',
        'keterangan',
    ];

    /**
     * Otomatis buat nomor surat jika kosong.
     */
    protected static function booted(): void
    {
        static::creating(function (self $pesanan) {
            if (!$pesanan->nomor_surat) {
                $year = now()->year;
                $month = now()->format('m');
                $count = self::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
                $no_urut = str_pad($count + 1, 5, '0', STR_PAD_LEFT);
                $pesanan->nomor_surat = "SO.$year.$month.$no_urut";
            }
        });
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PesananItem::class);
    }
}