<?php
namespace App\Filament\Resources\TandaTerimaPembayaranResource\Pages;

use App\Filament\Resources\TandaTerimaPembayaranResource;
use App\Models\FakturPenjualan;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateTandaTerimaPembayaran extends CreateRecord
{
    protected static string $resource = TandaTerimaPembayaranResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $fakturIds = $data['faktur_penjualan_ids'] ?? [];
            unset($data['faktur_penjualan_ids']);

            if (function_exists('terbilang')) {
                $data['terbilang'] = ucwords(terbilang($data['pembayaran']));
            }
            
            // 1. Buat record Tanda Terima Pembayaran
            $tandaTerima = static::getModel()::create($data);

            // 2. Alokasikan pembayaran ke faktur yang dipilih
            $sisaPembayaran = (float)$data['pembayaran'];

            foreach($fakturIds as $fakturId) {
                if ($sisaPembayaran <= 0) {
                    break; // Berhenti jika uang pembayaran sudah habis dialokasikan
                }

                $faktur = FakturPenjualan::find($fakturId);
                
                // --- LOGIKA PERBAIKAN DIMULAI DI SINI ---

                // Hitung total yang sudah pernah dibayarkan untuk faktur ini dari Tanda Terima lain
                $sudahDibayar = $faktur->tandaTerimaPembayarans()->sum('faktur_penjualan_tanda_terima_pembayaran.jumlah_dibayar_untuk_faktur_ini');
                
                // Hitung sisa tagihan yang sebenarnya
                $sisaTagihanFaktur = $faktur->total - $sudahDibayar;

                // Lewati faktur ini jika ternyata sudah lunas
                if ($sisaTagihanFaktur <= 0) {
                    continue;
                }

                $dibayarUntukFakturIni = 0;

                if ($sisaPembayaran >= $sisaTagihanFaktur) {
                    // Jika pembayaran saat ini cukup atau lebih untuk melunasi sisa tagihan
                    $dibayarUntukFakturIni = $sisaTagihanFaktur;
                    $faktur->status = 'paid';
                } else {
                    // Jika pembayaran saat ini hanya cukup untuk sebagian dari sisa tagihan
                    $dibayarUntukFakturIni = $sisaPembayaran;
                    $faktur->status = 'partial';
                }
                
                $faktur->save();

                // Simpan jumlah yang dibayarkan untuk faktur ini ke tabel pivot
                $tandaTerima->fakturPenjualans()->attach($fakturId, ['jumlah_dibayar_untuk_faktur_ini' => $dibayarUntukFakturIni]);
                
                // Kurangi sisa uang yang akan dialokasikan ke faktur berikutnya
                $sisaPembayaran -= $dibayarUntukFakturIni;
            }
            
            return $tandaTerima;
        });
    }
}