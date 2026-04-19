<?php

namespace App\Filament\Resources\Shipments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.id')
                    ->searchable(),
                TextColumn::make('shiprocket_order_id')
                    ->searchable(),
                TextColumn::make('shiprocket_shipment_id')
                    ->searchable(),
                TextColumn::make('awb_code')
                    ->searchable(),
                TextColumn::make('courier_name')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('tracking_url')
                    ->searchable(),
                TextColumn::make('expected_delivery')
                    ->date()
                    ->sortable(),
                TextColumn::make('actual_delivery')
                    ->date()
                    ->sortable(),
                TextColumn::make('label_pdf_url')
                    ->searchable(),
                TextColumn::make('manifest_pdf_url')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
