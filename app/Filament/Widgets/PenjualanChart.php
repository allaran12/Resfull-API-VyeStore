<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Penjualan;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class PenjualanChart extends ChartWidget
{
    protected static ?string $heading = 'Chart Penjualan Bulanan';

    protected static ?string $pollingInterval = '10s';

    protected function getFilters(): ?array
{
    return [
        'today' => 'Today',
        'week' => 'Last week',
        'month' => 'Last month',
        'year' => 'This year',
    ];
}


    protected function getData(): array
    {
        $activeFilter = $this->filter;

        
        $data = Trend::model(Penjualan::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        // Mengubah tanggal menjadi nama bulan
        $labels = $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('F'));

        return [
            'datasets' => [
                [
                    'label' => 'Transaksi',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
