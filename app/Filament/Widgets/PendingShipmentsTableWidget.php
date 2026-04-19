<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingShipmentsTableWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Paid orders — no AWB yet')
            ->description('Needs Shiprocket label / pickup')
            ->query(
                Order::query()
                    ->where('status', 'paid')
                    ->where(function (Builder $q): void {
                        $q->whereDoesntHave('shipments')
                            ->orWhereHas('shipments', function (Builder $s): void {
                                $s->where(function (Builder $inner): void {
                                    $inner->whereNull('awb_code')->orWhere('awb_code', '');
                                });
                            });
                    })
                    ->orderByDesc('paid_at')
            )
            ->columns([
                TextColumn::make('order_number')->label('Order')->searchable(),
                TextColumn::make('email')->limit(28),
                TextColumn::make('paid_at')->dateTime()->sortable(),
                TextColumn::make('total_inr')->money('INR')->label('Total'),
            ])
            ->recordUrl(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record]))
            ->paginated([5, 10]);
    }
}
