<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class TodayStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $start = now()->startOfDay();
        $end = now()->endOfDay();

        $ordersToday = Order::query()
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$start, $end])
            ->count();

        $revenueToday = (float) Order::query()
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$start, $end])
            ->sum('total_inr');

        $pendingPack = Order::query()
            ->where('status', 'paid')
            ->whereNull('packed_at')
            ->count();

        return [
            Stat::make('Orders paid today', Number::format($ordersToday))
                ->description('Payment captured today')
                ->icon(Heroicon::OutlinedShoppingBag),
            Stat::make('Revenue today', '₹'.Number::format($revenueToday, maxPrecision: 0))
                ->description('Total INR from paid orders')
                ->icon(Heroicon::OutlinedBanknotes),
            Stat::make('Awaiting pack', Number::format($pendingPack))
                ->description('Paid, not marked packed')
                ->icon(Heroicon::OutlinedCube),
        ];
    }
}
