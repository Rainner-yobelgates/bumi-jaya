<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use App\Models\Transaction;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        $getItem = Item::count();
        $getTransaction = Transaction::whereYear('created_at', date('Y'))->count();
        $getYearIncome = Transaction::whereYear('created_at', date('Y'))->sum('total');
        return [
            Stat::make('Jumlah Barang', $getItem)->description('Jumlah semua barang')->descriptionIcon('heroicon-m-information-circle', IconPosition::Before),
            Stat::make('Jumlah Transaksi', $getTransaction)->description('Jumlah Transaksi tahun ini')->descriptionIcon('heroicon-m-information-circle', IconPosition::Before),
            Stat::make('Jumlah Pendapatan', number_format($getYearIncome))->description("Jumlah pendapatan tahun ini")->descriptionIcon('heroicon-m-information-circle', IconPosition::Before),
        ];
    }
}
