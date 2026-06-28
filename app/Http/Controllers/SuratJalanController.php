<?php
namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\SuratJalanItem;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Perusahaan;
class SuratJalanController extends Controller
{
    

    public function cetak($id)
{
    $surat = SuratJalan::with(['items.barang'])->findOrFail($id);
    $pdf = Pdf::loadView('surat-jalan.pdf', compact('surat'))->setPaper('A4', 'portrait');
    return $pdf->stream('surat-jalan-' . $surat->nomor . '.pdf');
}

}
