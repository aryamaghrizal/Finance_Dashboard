<?php

namespace App\Filament\Widgets;

use App\Models\Perusahaan;
use App\Models\PesananPenjualan;
use App\Models\SuratPenawaranHarga;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class FinanceStats extends BaseWidget
{
    protected static ?int $sort = 1; // Atur urutan widget di dasbor

    protected function getStats(): array
    {
        // --- LOGIKA UNTUK STATISTIK DINAMIS ---

        // 1. Logika untuk Penjualan Bulan Ini vs Bulan Lalu
        $salesThisMonth = PesananPenjualan::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        $salesLastMonth = PesananPenjualan::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('total');

        $percentageChange = 0;
        if ($salesLastMonth > 0) {
            $percentageChange = (($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100;
        } elseif ($salesThisMonth > 0) {
            $percentageChange = 100; // Jika bulan lalu 0, dan bulan ini ada penjualan
        }

        $increase = $percentageChange >= 0;
        $descriptionIcon = $increase ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        $descriptionColor = $increase ? 'success' : 'danger';
        $descriptionText = sprintf(
            '%s %.2f%% dari bulan lalu',
            $increase ? 'Naik' : 'Turun',
            abs($percentageChange)
        );

        // 2. Logika untuk Tingkat Konversi
        $totalPenawaran = SuratPenawaranHarga::count();
        $totalPesanan = PesananPenjualan::count();
        $conversionRate = ($totalPenawaran > 0) ? ($totalPesanan / $totalPenawaran) * 100 : 0;


        // --- DEKLARASI WIDGET STATS ---

        return [
            Stat::make('Penjualan Bulan Ini', 'Rp ' . number_format($salesThisMonth))
                ->description($descriptionText)
                ->descriptionIcon($descriptionIcon)
                ->color($descriptionColor),

            Stat::make('Tingkat Konversi (Penawaran -> SO)', number_format($conversionRate, 2) . '%')
                ->description('Dari ' . $totalPenawaran . ' penawaran menjadi ' . $totalPesanan . ' penjualan')
                ->color('success'),
            
            Stat::make('Total Penjualan (SO)', 'Rp ' . number_format(PesananPenjualan::sum('total')))
                ->description('Total dari semua Pesanan Penjualan')
                ->color('primary'),

            Stat::make('Jumlah Pelanggan', Perusahaan::count())
                ->description('Total perusahaan yang terdaftar')
                ->color('primary'),
        ];
    }
}