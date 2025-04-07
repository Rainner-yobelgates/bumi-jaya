<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProfitMonthlyCart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Pendapatan';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $revenueData = Transaction::selectRaw('EXTRACT(MONTH FROM created_at) AS month, SUM(total_price) AS total')
        ->whereYear('created_at', date('Y'))
        ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
        ->orderBy('month')
        ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $revenueArray = array_fill(0, 12, 0);

        foreach ($revenueData as $item) {
            $revenueArray[$item->month - 1] = (float) $item->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pendapatan',
                    'data' => $revenueArray,
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
