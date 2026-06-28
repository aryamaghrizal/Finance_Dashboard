<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor',
        'tanggal',
        'po_no',
        'ekspedisi',
        'perusahaan_id',
        'keterangan',
    ];

    // --- TAMBAHKAN KODE DI BAWAH INI ---
    protected static function booted(): void
    {
        static::creating(function (self $surat) {
            // Logika dari SuratJalanController@store
            if (!$surat->nomor) {
                $now = Carbon::now();
                $prefix = 'DO.' . $now->format('Y.m') . '.';
                $count = self::whereYear('tanggal', $now->year)->whereMonth('tanggal', $now->month)->count() + 1;
                $surat->nomor = $prefix . str_pad($count, 5, '0', STR_PAD_LEFT);
            }
        });
    }
    // --- SAMPAI SINI ---

    public function items()
    {
        return $this->hasMany(SuratJalanItem::class);
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }
}   