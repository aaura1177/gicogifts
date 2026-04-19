<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Revenue (last 30 days)';

    protected ?string $description = 'Paid order totals by day';

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 2,
    ];

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn (int $i): Carbon => now()->subDays($i)->startOfDay());
        $since = $days->first();

        $totals = [];
        foreach (
            Order::query()
                ->where('status', 'paid')
                ->whereNotNull('paid_at')
                ->where('paid_at', '>=', $since)
                ->get(['paid_at', 'total_inr']) as $row
        ) {
            $key = $row->paid_at->toDateString();
            $totals[$key] = ($totals[$key] ?? 0) + (float) $row->total_inr;
        }

        $labels = $days->map(fn (Carbon $d): string => $d->format('M j'))->all();
        $values = $days->map(function (Carbon $d) use ($totals): float {
            return (float) ($totals[$d->toDateString()] ?? 0);
        })->all();

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (₹)',
                    'data' => $values,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
