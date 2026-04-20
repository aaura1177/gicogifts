<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'shipments';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('shiprocket_order_id')
                    ->default(null),
                TextInput::make('shiprocket_shipment_id')
                    ->default(null),
                TextInput::make('awb_code')
                    ->default(null),
                TextInput::make('courier_name')
                    ->default(null),
                TextInput::make('status')
                    ->default(null),
                TextInput::make('tracking_url')
                    ->url()
                    ->default(null),
                DatePicker::make('expected_delivery'),
                DatePicker::make('actual_delivery'),
                TextInput::make('label_pdf_url')
                    ->url()
                    ->default(null),
                TextInput::make('manifest_pdf_url')
                    ->url()
                    ->default(null),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('courier_name')
            ->columns([
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
                    ->url(fn ($state) => filled($state) ? (string) $state : null)
                    ->openUrlInNewTab(),
                TextColumn::make('expected_delivery')
                    ->date()
                    ->sortable(),
                TextColumn::make('actual_delivery')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
