<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FakturPenjualanResource\Pages;
use App\Models\Barang;
use App\Models\FakturPenjualan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FakturPenjualanResource extends Resource
{
    protected static ?string $model = FakturPenjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Faktur';
    protected static ?string $pluralModelLabel = 'Faktur Penjualan';
    protected static ?string $modelLabel = 'Faktur Penjualan (SI)';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Faktur')
                    ->schema([
                        Forms\Components\Select::make('perusahaan_id')
                            ->relationship('perusahaan', 'nama')
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('po_no')
                            ->label('Nomor PO Pelanggan'),
                        Forms\Components\DatePicker::make('tanggal_pesanan')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('tanggal_pengiriman')
                            ->required()
                            ->default(now()->addDays(1)),
                        Forms\Components\TextInput::make('syarat_pembayaran'),
                        Forms\Components\TextInput::make('ekspedisi'),
                        Forms\Components\TextInput::make('fob'),
                        Forms\Components\Select::make('mata_uang')
                            ->options(['IDR' => 'Indonesia Rupiah', 'USD' => 'USD', 'RM' =>'Ringgit Malaysia'])->required()->default('IDR'),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Barang')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            // ->relationship()
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
                                Forms\Components\TextInput::make('kuantitas')->numeric()->required()->default(1)->live(),
                                Forms\Components\TextInput::make('harga')->numeric()->required()->live(),
                                Forms\Components\TextInput::make('diskon')->numeric()->default(0)->live(),
                                Forms\Components\Placeholder::make('total_harga')
                                    ->label('Subtotal Item')
                                    ->content(function ($get) {
                                        $total = ((int) $get('kuantitas') * (float) $get('harga')) - (float) $get('diskon');
                                        return 'Rp ' . number_format($total);
                                    }),
                            ])
                            ->columns(7)
                            ->addActionLabel('Tambah Barang')
                            ->cloneable()
                            ->reorderable(false),
                    ]),
                Forms\Components\Section::make('Total & Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('diskon_total')
                            ->label('Diskon Keseluruhan')
                            ->numeric()->default(0),
                        Forms\Components\TextInput::make('biaya_lain')
                            ->numeric()->default(0),
                        Forms\Components\Toggle::make('ppn')
                            ->label('Sertakan PPN 11%'),
                        Forms\Components\Textarea::make('keterangan')
                            ->columnSpanFull(),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_surat')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('perusahaan.nama')->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pesanan')->date()->sortable(),
                Tables\Columns\TextColumn::make('total')->money('IDR')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'warning' => 'unpaid',
                    'info' => 'partial',
                    'success' => 'paid',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Cetak')
                    ->icon('heroicon-o-printer')
                    ->url(fn(FakturPenjualan $record): string => route('faktur.cetak', $record))
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
            'index' => Pages\ListFakturPenjualans::route('/'),
            'create' => Pages\CreateFakturPenjualan::route('/create'),
            'edit' => Pages\EditFakturPenjualan::route('/{record}/edit'),
        ];
    }
}