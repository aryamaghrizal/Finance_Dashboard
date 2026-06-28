<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $pluralModelLabel = 'Daftar Barang';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kategori')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('harga')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('stok')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Tambahkan baris di bawah ini
            ->contentGrid([
                'md' => 2, // 2 kolom di layar medium
                'xl' => 3, // 3 kolom di layar besar
            ])
            ->recordUrl(null) // Nonaktifkan klik per baris karena tidak relevan di card
            ->columns([
                // Kolom ini sekarang tidak akan ditampilkan, tapi diperlukan oleh Filament
                Tables\Columns\TextColumn::make('kode'),
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\TextColumn::make('kategori'),
                Tables\Columns\TextColumn::make('harga'),
                
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
                
            ])
            
            
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            // 'edit' => Pages\EditBarang::route('/{record}/edit'), // Komentari ini
            'view' => Pages\ViewBarang::route('/{record}'), // Ganti dengan View
        ];
    }
}