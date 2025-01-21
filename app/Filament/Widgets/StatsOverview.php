<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $pemasukan = Transaction::incomes()
                    ->whereBetween('date_transaction', [$startDate, $endDate])
                    ->sum('amount');
        $pengeluaran = Transaction::expanses()
                    ->whereBetween('date_transaction', [$startDate, $endDate])
                    ->sum('amount');
        return [
            Stat::make('Total Pemasukan', $pemasukan),
            Stat::make('Total Pengeluaran', $pengeluaran),
            Stat::make('Selisih', $pemasukan - $pengeluaran),
        ];
    }
}
