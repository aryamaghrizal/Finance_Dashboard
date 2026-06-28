<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FakturPenjualan;
use App\Models\PesananItem;
use App\Models\Perusahaan;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use PDF;

class FakturPenjualanController extends Controller
{
    public function cetak($id)
    {
        $faktur = FakturPenjualan::with('perusahaan', 'items.barang')->findOrFail($id);
        $pdf = PDF::loadView('faktur.pdf', compact('faktur'));
        return $pdf->stream("FakturPenjualan-{$faktur->nomor_surat}.pdf");
    }
}
