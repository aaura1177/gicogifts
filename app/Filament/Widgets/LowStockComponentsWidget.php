<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Components\ComponentResource;
use App\Models\Component;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LowStockComponentsWidget extends TableWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Low stock components')
            ->description('Stock at or below reorder threshold')
            ->query(
                Component::query()
                    ->whereNotNull('reorder_threshold')
                    ->whereColumn('stock_on_hand', '<=', 'reorder_threshold')
                    ->orderBy('stock_on_hand')
            )
            ->columns([
                TextColumn::make('name')->label('Component')->searchable(),
                TextColumn::make('sku')->label('SKU'),
                TextColumn::make('stock_on_hand')->label('Stock')->sortable(),
                TextColumn::make('reorder_threshold')->label('Threshold')->sortable(),
            ])
            ->recordUrl(fn (Component $record): string => ComponentResource::getUrl('edit', ['record' => $record]))
            ->paginated([10, 25]);
    }
}
