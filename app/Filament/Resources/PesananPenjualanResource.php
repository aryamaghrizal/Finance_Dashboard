<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PesananPenjualanResource\Pages;
use App\Models\Barang;
use App\Models\Perusahaan; // <-- Pastikan ini di-import
use App\Models\PesananPenjualan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PesananPenjualanResource extends Resource
{
    protected static ?string $model = PesananPenjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Penjualan';
    protected static ?string $pluralModelLabel = 'Pesanan Penjualan';
    protected static ?string $modelLabel = 'Pesanan Penjualan (SO)';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pesanan')
                    ->schema([
                        Forms\Components\Select::make('perusahaan_id')
                            ->relationship('perusahaan', 'nama')
                            ->searchable()
                            ->required()
                            // --- PERUBAHAN 1: Membuat form reaktif ---
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                // Fungsi ini akan mengisi alamat secara otomatis saat perusahaan dipilih
                                if (blank($state)) {
                                    $set('alamat_perusahaan', null);
                                    return;
                                }
                                $perusahaan = Perusahaan::find($state);
                                $set('alamat_perusahaan', $perusahaan?->alamat);
                            }),
                        
                        // --- PERUBAHAN 2: Menambahkan field alamat ---
                        Forms\Components\Textarea::make('alamat_perusahaan')
                            ->label('Alamat Perusahaan (Bisa Diedit)')
                            ->rows(3)
                            ->placeholder('Alamat akan terisi otomatis setelah memilih perusahaan.'),

                        // Sisa field di section ini tetap sama
                        Forms\Components\TextInput::make('po_no')->label('Nomor PO Pelanggan'),
                        Forms\Components\DatePicker::make('tanggal_pesanan')->required()->default(now()),
                        Forms\Components\DatePicker::make('tanggal_pengiriman')->required()->default(now()->addDays(1)),
                        Forms\Components\TextInput::make('syarat_pembayaran'),
                        Forms\Components\TextInput::make('ekspedisi'),
                        Forms\Components\TextInput::make('fob'),
                        Forms\Components\TextInput::make('penjual'),
                    ])->columns(2),

                // Sisa form (Repeater dan Section Total) tidak berubah
                Forms\Components\Section::make('Detail Barang')
                    ->schema([
                        Forms\Components\Repeater::make('items')
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
                                        $total = ((int)$get('kuantitas') * (float)$get('harga')) - (float)$get('diskon');
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
                        Forms\Components\TextInput::make('diskon_total')->label('Diskon Keseluruhan')->numeric()->default(0),
                        Forms\Components\TextInput::make('biaya_lain')->numeric()->default(0),
                        Forms\Components\Toggle::make('ppn')->label('Sertakan PPN 11%'),
                        Forms\Components\Textarea::make('keterangan')->columnSpanFull(),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        // Method table() Anda tidak perlu diubah.
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_surat')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('perusahaan.nama')->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pesanan')->date()->sortable(),
                Tables\Columns\TextColumn::make('total')->money('IDR')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Cetak')
                    ->icon('heroicon-o-printer')
                    ->url(fn (PesananPenjualan $record): string => route('pesanan.cetak', $record))
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
            'index' => Pages\ListPesananPenjualans::route('/'),
            'create' => Pages\CreatePesananPenjualan::route('/create'),
            'edit' => Pages\EditPesananPenjualan::route('/{record}/edit'),
        ];
    }
}
