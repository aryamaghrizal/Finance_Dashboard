<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesananPenjualan;
use App\Models\PesananItem;
use App\Models\Perusahaan;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use PDF;

class PesananPenjualanController extends Controller
{
    public function cetak($id)
    {
        $pesanan = PesananPenjualan::with('perusahaan', 'items.barang')->findOrFail($id);
        $pdf = PDF::loadView('pesanan.pdf', compact('pesanan'));
        return $pdf->stream("PesananPenjualan-{$pesanan->nomor_surat}.pdf");
    }
}
