<?php

namespace App\Filament\Resources\PesananPenjualanResource\Pages;

use App\Filament\Resources\PesananPenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPesananPenjualans extends ListRecords
{
    protected static string $resource = PesananPenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
