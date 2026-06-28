<?php

namespace App\Filament\Resources\PesananPenjualanResource\Pages;

use App\Filament\Resources\PesananPenjualanResource;
use App\Models\Perusahaan; // <-- Tambahkan ini
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreatePesananPenjualan extends CreateRecord
{
    protected static string $resource = PesananPenjualanResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            
            // --- LOGIKA BARU UNTUK UPDATE ALAMAT ---
            if (!empty($data['perusahaan_id']) && isset($data['alamat_perusahaan'])) {
                $perusahaan = Perusahaan::find($data['perusahaan_id']);
                // Hanya update jika alamatnya berbeda untuk efisiensi
                if ($perusahaan && $perusahaan->alamat !== $data['alamat_perusahaan']) {
                    $perusahaan->alamat = $data['alamat_perusahaan'];
                    $perusahaan->save();
                }
            }
            // --- AKHIR LOGIKA BARU ---

            $itemsData = $data['items'] ?? [];
            
            // Hapus field sementara agar tidak mencoba disimpan ke tabel pesanan
            unset($data['items']);
            unset($data['alamat_perusahaan']);

            // Sisa kode Anda yang sudah ada untuk kalkulasi dan penyimpanan
            $subtotal = collect($itemsData)->sum(function ($item) {
                return ((int)($item['kuantitas'] ?? 0) * (float)($item['harga'] ?? 0)) - (float)($item['diskon'] ?? 0);
            });
            $ppn = !empty($data['ppn']) ? $subtotal * 0.11 : 0;
            $total = $subtotal - (float)($data['diskon_total'] ?? 0) + $ppn + (float)($data['biaya_lain'] ?? 0);
            
            $data['subtotal'] = $subtotal;
            $data['ppn'] = $ppn;
            $data['total'] = $total;
            if (function_exists('terbilang')) {
                $data['terbilang'] = ucwords(terbilang($total));
            }
            
            $pesanan = static::getModel()::create($data);

            foreach ($itemsData as $itemData) {
                $kuantitas = (int)($itemData['kuantitas'] ?? 0);
                $harga = (float)($itemData['harga'] ?? 0);
                $diskon = (float)($itemData['diskon'] ?? 0);

                $pesanan->items()->create([
                    'barang_id' => $itemData['barang_id'],
                    'kuantitas' => $kuantitas,
                    'harga' => $harga,
                    'diskon' => $diskon,
                    'total_harga' => ($kuantitas * $harga) - $diskon,
                ]);
            }
            
            return $pesanan;
        });
    }
}
