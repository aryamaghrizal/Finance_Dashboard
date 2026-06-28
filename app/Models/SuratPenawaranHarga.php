<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPenawaranHarga extends Model
{
    protected $fillable = [
        'nomor', 'tanggal', 'pembayaran', 'ppn', 'perusahaan_id', 'biaya_lain','keterangan',

    ];

    protected $casts = [
        'ppn' => 'boolean',
        'tanggal' => 'date',
    ];

    // --- TAMBAHKAN KODE DI BAWAH INI ---
    protected static function booted(): void
    {
        static::creating(function (self $surat) {
            // Logika dari SuratPenawaranHargaController@store
            if (!$surat->nomor) {
                $count = self::whereMonth('tanggal', date('m'))->whereYear('tanggal', date('Y'))->count() + 1;
                $nomor = 'SQ.' . date('Y.m') . '.' . str_pad($count, 5, '0', STR_PAD_LEFT);
                $surat->nomor = $nomor;
            }
        });
    }
    // --- SAMPAI SINI ---

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function items()
    {
        return $this->hasMany(PenawaranItem::class, 'surat_penawaran_harga_id');
    }

    public function subtotal()
    {
        return $this->items->sum(function ($item) {
            return $item->total;
        });
    }

    public function total()
    {
        $subtotal = $this->subtotal();
        $diskon = 0; // Jika ada diskon global, tambahkan di field
        $ppn = $this->ppn ? ($subtotal * 0.11) : 0;
        $biayaLain = $this->biaya_lain ?? 0;
        return $subtotal - $diskon + $ppn + $biayaLain;
    }
}