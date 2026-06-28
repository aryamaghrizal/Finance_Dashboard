<?php

namespace App\Filament\Resources\FakturPenjualanResource\Pages;

use App\Filament\Resources\FakturPenjualanResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateFakturPenjualan extends CreateRecord
{
    protected static string $resource = FakturPenjualanResource::class;
     protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $itemsData = $data['items'] ?? [];
            unset($data['items']);

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
            
            $faktur = static::getModel()::create($data);

            foreach ($itemsData as $itemData) {
                $kuantitas = (int)($itemData['kuantitas'] ?? 0);
                $harga = (float)($itemData['harga'] ?? 0);
                $diskon = (float)($itemData['diskon'] ?? 0);

                $faktur->items()->create([
                    'barang_id' => $itemData['barang_id'],
                    'kuantitas' => $kuantitas,
                    'harga' => $harga,
                    'diskon' => $diskon,
                    'total_harga' => ($kuantitas * $harga) - $diskon,
                ]);
            }
            
            return $faktur;
        });
    }

}

