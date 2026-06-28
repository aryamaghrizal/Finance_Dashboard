<?php

namespace App\Filament\Resources\FakturPenjualanResource\Pages;

use App\Filament\Resources\FakturPenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFakturPenjualans extends ListRecords
{
    protected static string $resource = FakturPenjualanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
