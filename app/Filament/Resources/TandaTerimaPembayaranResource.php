<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TandaTerimaPembayaranResource\Pages;
use App\Models\FakturPenjualan;
use App\Models\TandaTerimaPembayaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class TandaTerimaPembayaranResource extends Resource
{
    protected static ?string $model = TandaTerimaPembayaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationGroup = 'Faktur';
    protected static ?string $pluralModelLabel = 'Tanda Terima Pembayaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pembayaran')
                    ->schema([
                        Forms\Components\Select::make('perusahaan_id')
                            ->relationship('perusahaan', 'nama')
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('faktur_penjualan_ids', null)),
                        
                        Forms\Components\DatePicker::make('tanggal_pembayaran')->required()->default(now()),
                        
                        Forms\Components\TextInput::make('pembayaran') // Nama field sudah benar: 'pembayaran'
                            ->label('Jumlah Uang yang Diterima')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->live(),
                    ])->columns(2),

                Forms\Components\Section::make('Alokasi Pembayaran ke Faktur')
                    ->schema([
                        Forms\Components\Select::make('faktur_penjualan_ids')
                            ->label('Pilih Faktur yang Dibayar')
                            ->multiple()
                            ->options(function (Get $get): Collection {
                                return FakturPenjualan::where('perusahaan_id', $get('perusahaan_id'))
                                    ->where('status', '!=', 'paid') 
                                    ->pluck('nomor_surat', 'id'); // Tetap gunakan nomor_surat sesuai permintaan Anda
                            })
                            ->live(),

                        Forms\Components\Placeholder::make('detail_pembayaran')
                            ->label('Rincian Kalkulasi')
                            ->content(function (Get $get) {
                                $selectedInvoiceIds = $get('faktur_penjualan_ids');
                                if (empty($selectedInvoiceIds)) {
                                    return 'Pilih faktur untuk melihat rincian.';
                                }
                                $selectedInvoices = FakturPenjualan::whereIn('id', $selectedInvoiceIds)->get();
                                $totalTagihan = $selectedInvoices->sum('total');
                                $jumlahBayar = (float)$get('pembayaran');
                                $sisa = $jumlahBayar - $totalTagihan;
                                $status = $sisa >= 0 ? 'LEBIH BAYAR' : 'SISA TAGIHAN';

                                return new \Illuminate\Support\HtmlString(
                                    'Total Tagihan Dipilih: <strong>Rp ' . number_format($totalTagihan) . '</strong><br>' .
                                    'Jumlah Bayar: <strong>Rp ' . number_format($jumlahBayar) . '</strong><br>' .
                                    $status . ': <strong>Rp ' . number_format(abs($sisa)) . '</strong>'
                                );
                            }),
                    ]),
                
                Forms\Components\Section::make('Informasi Tambahan (Opsional)')
                    ->schema([
                        Forms\Components\TextInput::make('bank'),
                        Forms\Components\TextInput::make('nomor_cek'),
                        Forms\Components\DatePicker::make('tanggal_cek'),
                        Forms\Components\Textarea::make('keterangan')->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_pembayaran')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('perusahaan.nama')->searchable(),
                Tables\Columns\TextColumn::make('tanggal_pembayaran')->date()->sortable(),
                Tables\Columns\TextColumn::make('pembayaran')->money('IDR')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Cetak')
                    ->icon('heroicon-o-printer')
                    ->url(fn(TandaTerimaPembayaran $record): string => route('tanda-terima-pembayaran.cetak', $record))
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
            'index' => Pages\ListTandaTerimaPembayarans::route('/'),
            'create' => Pages\CreateTandaTerimaPembayaran::route('/create'),
            'edit' => Pages\EditTandaTerimaPembayaran::route('/{record}/edit'),
        ];
    }    
}