<?php

namespace App\Http\Controllers;

use App\Models\SuratPenawaranHarga;
use App\Models\Perusahaan;
use App\Models\Barang;
use App\Models\PenawaranItem;
use Illuminate\Http\Request;
use PDF;

class SuratPenawaranHargaController extends Controller
{


    // Cetak PDF penawaran harga
    public function pdf($id)
{
    $surat = SuratPenawaranHarga::with(['perusahaan', 'items.barang'])->findOrFail($id);
    $pdf = PDF::loadView('penawaran.pdf', compact('surat'))->setPaper('A4');
    return $pdf->stream('Penawaran_' . $surat->nomor . '.pdf'); // tampil di browser
}

}
