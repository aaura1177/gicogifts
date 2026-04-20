<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->default(null),
                TextInput::make('product_name')
                    ->required(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('unit_price_inr')
                    ->required()
                    ->numeric(),
                TextInput::make('line_total_inr')
                    ->required()
                    ->numeric(),
                TextInput::make('hsn_code')
                    ->default(null),
                TextInput::make('gst_rate')
                    ->required()
                    ->numeric()
                    ->default(0.0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                TextColumn::make('product.name')
                    ->searchable(),
                TextColumn::make('product_name')
                    ->searchable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price_inr')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('line_total_inr')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('hsn_code')
                    ->searchable(),
                TextColumn::make('gst_rate')
                    ->numeric()
                    ->sortable(),
            ]);
    }
}
