<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenawaranItem extends Model
{
    protected $fillable = [
        'surat_penawaran_harga_id', 'barang_id', 'kuantitas', 'harga', 'diskon'
    ];

    protected $appends = ['total'];

    public function surat()
    {
        return $this->belongsTo(SuratPenawaranHarga::class, 'surat_penawaran_harga_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function getTotalAttribute()
    {
        $total = $this->harga * $this->kuantitas;
        if ($this->diskon) {
            $total -= ($total * ($this->diskon / 100));
        }
        return $total;
    }
}

