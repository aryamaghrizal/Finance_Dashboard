<?php
namespace App\Filament\Resources\PerusahaanResource\Pages;

use App\Filament\Resources\PerusahaanResource;
use App\Imports\PerusahaanImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class ListPerusahaans extends ListRecords
{
    protected static string $resource = PerusahaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('import')
                ->label('Import Pelanggan')
                ->color('success')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    FileUpload::make('attachment')
                        ->label('File CSV / XLSX')
                        ->required()
                        ->disk('local')
                        ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']) 
                ])
                ->action(function (array $data) {
                    $file = $data['attachment'];
                    
                    // Memberitahu Excel untuk mencari file di disk 'local'
                    Excel::import(new PerusahaanImport, $file, 'local');
                    
                    Notification::make()
                        ->title('Data Pelanggan Berhasil Diimpor')
                        ->success()
                        ->send();
                })
        ];
    }
}
