<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use App\Models\Transaction;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class WidgetIncomeChart extends ChartWidget
{
    protected static ?string $heading = 'Income';
    protected static string $color = 'success';
    use InteractsWithPageFilters;

    protected function getData(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        // Menggunakan Trend::model() dengan nama model Transaction dan menerapkan scope expanses di dalam query
        $data = Trend::query(Transaction::whereHas('category', function ($query) {
            $query->where('is_expanse', false); // Filter berdasarkan kategori pengeluaran
        }))
        ->between(
            start: $startDate,
            end: $endDate,
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
