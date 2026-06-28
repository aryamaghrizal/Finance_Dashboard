<?php

namespace App\Filament\Resources\SuratPenawaranHargaResource\Pages;

use App\Filament\Resources\SuratPenawaranHargaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratPenawaranHargas extends ListRecords
{
    protected static string $resource = SuratPenawaranHargaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
