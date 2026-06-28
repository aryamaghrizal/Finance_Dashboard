<?php

namespace App\Filament\Resources\SuratPenawaranHargaResource\Pages;

use App\Filament\Resources\SuratPenawaranHargaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuratPenawaranHarga extends EditRecord
{
    protected static string $resource = SuratPenawaranHargaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
