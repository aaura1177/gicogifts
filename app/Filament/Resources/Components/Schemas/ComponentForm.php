<?php

namespace App\Filament\Resources\Components\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ComponentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                TextInput::make('unit_cost_inr')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('stock_on_hand')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('reorder_threshold')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('supplier_name')
                    ->default(null),
                TextInput::make('supplier_contact')
                    ->default(null),
                TextInput::make('hsn_code')
                    ->default(null),
            ]);
    }
}
