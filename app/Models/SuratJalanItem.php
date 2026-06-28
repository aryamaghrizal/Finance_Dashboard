<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_jalan_id',
        'barang_id',
        'kuantitas',
        'satuan',
    ];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
