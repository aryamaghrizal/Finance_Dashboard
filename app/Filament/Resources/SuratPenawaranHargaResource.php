<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuratPenawaranHargaResource\Pages;
use App\Models\Barang;
use App\Models\SuratPenawaranHarga;
use Filament\Forms;
use Filament\Forms\Form; // Pastikan Form di-import
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SuratPenawaranHargaResource extends Resource
{
    protected static ?string $model = SuratPenawaranHarga::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Penjualan';
    protected static ?string $pluralModelLabel = 'Penawaran Harga';
    protected static ?string $modelLabel = 'Penawaran Harga';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Surat')
                    ->schema([
                        Forms\Components\Select::make('perusahaan_id')
                            ->relationship('perusahaan', 'nama')
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('pembayaran')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('ppn')
                            ->label('Termasuk PPN 11%'),
                        Forms\Components\TextInput::make('biaya_lain')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Textarea::make('keterangan')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Barang')
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
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('kuantitas')->numeric()->required()->default(1),
                                Forms\Components\TextInput::make('harga')->numeric()->required(),
                                Forms\Components\TextInput::make('diskon')->numeric()->default(0)->suffix('%'),
                            ])
                            ->columns(5)
                            ->addActionLabel('Tambah Barang')
                            ->cloneable()
                            // --- PERBAIKAN UTAMA DI BARIS INI ---
                            ->deleteAction(
                                fn () => Forms\Components\Actions\Action::make('delete') // Gunakan Action dari Forms
                                    ->label('Hapus Item')
                                    ->requiresConfirmation()
                                    ->color('danger')
                                    ->icon('heroicon-o-trash')
                            )
                            ->reorderable(false)
                    ])
            ]);
    }

    // ... method table() dan method lainnya tetap sama ...
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('perusahaan.nama')->searchable(),
                Tables\Columns\TextColumn::make('tanggal')->date()->sortable(),
                Tables\Columns\IconColumn::make('ppn')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Cetak')
                    ->icon('heroicon-o-printer')
                    ->url(fn (SuratPenawaranHarga $record): string => route('penawaran.pdf', $record)) // Pastikan route 'penawaran.pdf' ada
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListSuratPenawaranHargas::route('/'),
            'create' => Pages\CreateSuratPenawaranHarga::route('/create'),
            'edit' => Pages\EditSuratPenawaranHarga::route('/{record}/edit'),
        ];
    }
}