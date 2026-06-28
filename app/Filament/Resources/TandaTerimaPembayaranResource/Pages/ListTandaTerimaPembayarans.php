<?php

namespace App\Filament\Resources\TandaTerimaPembayaranResource\Pages;

use App\Filament\Resources\TandaTerimaPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTandaTerimaPembayarans extends ListRecords
{
    protected static string $resource = TandaTerimaPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
