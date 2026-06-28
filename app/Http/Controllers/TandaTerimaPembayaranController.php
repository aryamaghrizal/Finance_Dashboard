<?php

namespace App\Http\Controllers;

use App\Models\TandaTerimaPembayaran;
use Barryvdh\DomPDF\Facade\Pdf;

class TandaTerimaPembayaranController extends Controller
{
    /**
     * Fungsi ini akan dipanggil untuk mencetak PDF.
     *
     * @param  int  $id ID dari Tanda Terima Pembayaran yang akan dicetak.
     * @return \Illuminate\Http\Response
     */
    public function cetak($id)
    {
        // 1. Ambil data Tanda Terima dari database beserta relasinya
        // Eager load 'perusahaan' dan 'fakturPenjualans' untuk efisiensi
        $tandaTerima = TandaTerimaPembayaran::with(['perusahaan', 'fakturPenjualans'])->findOrFail($id);
        
        // 2. Siapkan nama file untuk PDF
        $filename = "TandaTerima-" . $tandaTerima->nomor_pembayaran . ".pdf";

        // 3. Muat view Blade dari Canvas ('tanda-terima-pembayaran.pdf')
        // dan kirimkan variabel $tandaTerima ke dalamnya
        $pdf = PDF::loadView('tanda-terima.pdf', compact('tandaTerima'));
        
        // 4. Tampilkan PDF di browser agar bisa di-preview atau di-download
        return $pdf->stream($filename);
    }
}
