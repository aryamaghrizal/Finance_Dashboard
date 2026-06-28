<?php

namespace App\Filament\Resources\TandaTerimaPembayaranResource\Pages;

use App\Filament\Resources\TandaTerimaPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTandaTerimaPembayaran extends EditRecord
{
    protected static string $resource = TandaTerimaPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
