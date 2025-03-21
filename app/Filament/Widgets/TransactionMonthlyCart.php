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
        $data = Transaction::whereYear('created_at', date('Y')) 
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->get();
        
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        $transactionData = array_fill(0, 12, 0);
        foreach ($data as $item) {
            $transactionData[$item->month - 1] = $item->total;
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
