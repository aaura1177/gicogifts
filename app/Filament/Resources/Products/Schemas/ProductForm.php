<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('tabs')
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(null),
                                Select::make('region_id')
                                    ->relationship('region', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(null),
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Usually matches URL; generated from name on model save if you use slugs elsewhere.'),
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->required()
                                    ->maxLength(64),
                                TextInput::make('subtitle')
                                    ->maxLength(255)
                                    ->default(null),
                                TextInput::make('price_inr')
                                    ->required()
                                    ->numeric()
                                    ->prefix('₹'),
                                TextInput::make('compare_at_price_inr')
                                    ->numeric()
                                    ->prefix('₹')
                                    ->default(null),
                                Toggle::make('is_box')
                                    ->label('Gift box (BOM)')
                                    ->helperText('When on, use the BOM tab to attach components.')
                                    ->required(),
                                Toggle::make('is_active')
                                    ->required(),
                                Toggle::make('is_featured')
                                    ->required(),
                            ])
                            ->columns(2),
                        Tab::make('Story')
                            ->schema([
                                Textarea::make('short_description')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Textarea::make('story_md')
                                    ->label('Story (Markdown)')
                                    ->rows(12)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('BOM')
                            ->hidden(fn (Get $get): bool => ! (bool) $get('is_box'))
                            ->schema([
                                Repeater::make('productComponents')
                                    ->relationship()
                                    ->schema([
                                        Select::make('component_id')
                                            ->relationship('component', 'name', fn ($query) => $query->orderBy('name'))
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        TextInput::make('quantity')
                                            ->numeric()
                                            ->required()
                                            ->default(1)
                                            ->minValue(0.01),
                                        TextInput::make('notes')
                                            ->maxLength(255)
                                            ->default(null),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('Add component')
                                    ->reorderable(false)
                                    ->collapsible(),
                            ]),
                        Tab::make('Shipping & tax')
                            ->schema([
                                TextInput::make('weight_grams')
                                    ->numeric()
                                    ->suffix('g')
                                    ->default(null),
                                TextInput::make('length_cm')
                                    ->numeric()
                                    ->suffix('cm')
                                    ->default(null),
                                TextInput::make('width_cm')
                                    ->numeric()
                                    ->suffix('cm')
                                    ->default(null),
                                TextInput::make('height_cm')
                                    ->numeric()
                                    ->suffix('cm')
                                    ->default(null),
                                TextInput::make('hsn_code')
                                    ->maxLength(32)
                                    ->default(null),
                                TextInput::make('gst_rate')
                                    ->required()
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(5.0),
                            ])
                            ->columns(2),
                        Tab::make('SEO & publishing')
                            ->schema([
                                TextInput::make('meta_title')
                                    ->maxLength(255)
                                    ->default(null),
                                Textarea::make('meta_description')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                TextInput::make('sort_order')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                DateTimePicker::make('published_at'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
