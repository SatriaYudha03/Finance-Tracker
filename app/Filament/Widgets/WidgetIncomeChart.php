<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class WidgetIncomeChart extends ChartWidget
{
    protected static ?string $heading = 'Income';
    protected static string $color = 'success';

    protected function getData(): array
    {
        // Menggunakan Trend::model() dengan nama model Transaction dan menerapkan scope expanses di dalam query
        $data = Trend::query(Transaction::whereHas('category', function ($query) {
            $query->where('is_expanse', false); // Filter berdasarkan kategori pengeluaran
        }))
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perDay()
        ->sum('amount');

    return [
        'datasets' => [
            [
                'label' => 'Pemasukan',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
