<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuratJalanResource\Pages;
use App\Models\Barang;
use App\Models\SuratJalan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SuratJalanResource extends Resource
{
    protected static ?string $model = SuratJalan::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Penjualan';
    protected static ?string $pluralModelLabel = 'Surat Jalan';
    protected static ?string $modelLabel = 'Surat Jalan (DO)';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Surat Jalan')
                    ->schema([
                        Forms\Components\Select::make('perusahaan_id')
                            ->relationship('perusahaan', 'nama')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('po_no')
                            ->label('Nomor PO Terkait'),
                        Forms\Components\DatePicker::make('tanggal')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('ekspedisi'),
                        Forms\Components\Textarea::make('keterangan')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Barang Dikirim')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('barang_id')
                                    ->label('Barang')
                                    ->options(Barang::query()->limit(50)->pluck('nama', 'id')) // Memuat 50 barang pertama sebagai opsi awal
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $search): array => Barang::where('nama', 'like', "%{$search}%")->orWhere('kode', 'like', "%{$search}%")->limit(50)->pluck('nama', 'id')->all())
                                    ->getOptionLabelUsing(fn ($value): ?string => Barang::find($value)?->nama)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('harga', Barang::find($state)?->harga ?? 0))
                                    ->columnSpan(3),
                                Forms\Components\TextInput::make('kuantitas')->numeric()->required()->default(1),
                                Forms\Components\TextInput::make('satuan')->required()->default('Pcs'),
                            ])
                            ->columns(4)
                            ->addActionLabel('Tambah Barang')
                            ->cloneable()
                            ->reorderable(false)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('perusahaan.nama')->searchable(),
                Tables\Columns\TextColumn::make('tanggal')->date()->sortable(),
                Tables\Columns\TextColumn::make('po_no')->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Cetak')
                ->icon('heroicon-o-printer')
                ->url(fn (SuratJalan $record): string => route('suratjalan.cetak', $record)) // Menggunakan route 'suratjalan.cetak'
                ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuratJalans::route('/'),
            'create' => Pages\CreateSuratJalan::route('/create'),
            'edit' => Pages\EditSuratJalan::route('/{record}/edit'),
        ];
    }
}