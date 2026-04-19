<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->default(null),
                Select::make('region_id')
                    ->relationship('region', 'name')
                    ->default(null),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('subtitle')
                    ->default(null),
                Textarea::make('story_md')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('short_description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('price_inr')
                    ->required()
                    ->numeric(),
                TextInput::make('compare_at_price_inr')
                    ->numeric()
                    ->default(null),
                Toggle::make('is_box')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                TextInput::make('hsn_code')
                    ->default(null),
                TextInput::make('gst_rate')
                    ->required()
                    ->numeric()
                    ->default(5.0),
                TextInput::make('weight_grams')
                    ->numeric()
                    ->default(null),
                TextInput::make('length_cm')
                    ->numeric()
                    ->default(null),
                TextInput::make('width_cm')
                    ->numeric()
                    ->default(null),
                TextInput::make('height_cm')
                    ->numeric()
                    ->default(null),
                TextInput::make('meta_title')
                    ->default(null),
                TextInput::make('meta_description')
                    ->default(null),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('published_at'),
            ]);
    }
}
