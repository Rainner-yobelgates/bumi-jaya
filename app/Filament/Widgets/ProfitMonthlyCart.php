<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProfitMonthlyCart extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $revenueData = Transaction::whereYear('created_at', date('Y')) 
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_price) as total'))
            ->groupBy('month')
            ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        $revenueArray = array_fill(0, 12, 0);

        foreach ($revenueData as $item) {
            $revenueArray[$item->month - 1] = $item->total;
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
