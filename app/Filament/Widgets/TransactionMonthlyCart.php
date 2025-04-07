<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TransactionMonthlyCart extends ChartWidget
{
    protected static ?string $heading = 'Transaksi bulanan pada tahun ini';
    protected static ?int $sort = 1;
    protected function getData(): array
    {
        $transactions = Transaction::whereYear('created_at', now()->year)->get();

        $monthlyCounts = $transactions->groupBy(function ($item) {
            return $item->created_at->format('n'); // bulan 1-12
        })->map(function ($group) {
            return $group->count();
        });

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $transactionData = array_fill(0, 12, 0);

        foreach ($monthlyCounts as $month => $count) {
            $transactionData[$month - 1] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $transactionData,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
