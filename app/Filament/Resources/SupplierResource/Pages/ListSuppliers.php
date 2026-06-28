<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use App\Imports\SupplierImport; // <-- Ganti Importer
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('import')
                ->label('Import Data Pemasok') // Ubah label
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('attachment')
                        ->label('Upload File CSV')
                        ->required()
                        ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                ])
                ->action(function (array $data) {
                    $file = $data['attachment'];
                    Excel::import(new SupplierImport, $file); // <-- Ganti Importer
                    \Filament\Notifications\Notification::make()
                        ->title('Data Pemasok Berhasil Diimpor') // Ubah pesan
                        ->success()
                        ->send();
                })
        ];
    }
}