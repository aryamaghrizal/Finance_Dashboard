<?php

namespace App\Filament\Resources\PesananPenjualanResource\Pages;

use App\Filament\Resources\PesananPenjualanResource;
use App\Models\Perusahaan; // <-- Tambahkan ini
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditPesananPenjualan extends EditRecord
{
    protected static string $resource = PesananPenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    // Fungsi ini mengisi form dengan data alamat saat halaman edit dibuka
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['perusahaan_id'])) {
            $perusahaan = Perusahaan::find($data['perusahaan_id']);
            if ($perusahaan) {
                $data['alamat_perusahaan'] = $perusahaan->alamat;
            }
        }
    
        return $data;
    }

    // Fungsi ini menangani penyimpanan saat tombol "Save changes" diklik
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            if (!empty($data['perusahaan_id']) && isset($data['alamat_perusahaan'])) {
                $perusahaan = Perusahaan::find($data['perusahaan_id']);
                if ($perusahaan && $perusahaan->alamat !== $data['alamat_perusahaan']) {
                    $perusahaan->alamat = $data['alamat_perusahaan'];
                    $perusahaan->save();
                }
            }
            
            // Hapus field sementara sebelum update record utama
            unset($data['alamat_perusahaan']);
            
            // Update record pesanan utama
            $record->update($data);

            return $record;
        });
    }
}