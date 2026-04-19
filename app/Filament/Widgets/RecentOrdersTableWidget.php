<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentOrdersTableWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent orders')
            ->query(Order::query()->latest())
            ->columns([
                TextColumn::make('order_number')->label('Order')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('total_inr')->money('INR')->label('Total'),
                TextColumn::make('updated_at')->since()->label('Updated'),
            ])
            ->recordUrl(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record]))
            ->paginated([5, 10]);
    }
}
