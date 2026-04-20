<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Component;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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
                                    ->live(onBlur: true)
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
                                MarkdownEditor::make('story_md')
                                    ->label('Story (Markdown)')
                                    ->toolbarButtons([
                                        'bold',
                                        'bulletList',
                                        'heading',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'undo',
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Related')
                            ->schema([
                                CheckboxList::make('artisans')
                                    ->relationship('artisans', 'name')
                                    ->searchable()
                                    ->bulkToggleable()
                                    ->columns(2)
                                    ->gridDirection('row')
                                    ->columnSpanFull(),
                                CheckboxList::make('occasions')
                                    ->relationship('occasions', 'name')
                                    ->searchable()
                                    ->bulkToggleable()
                                    ->columns(2)
                                    ->gridDirection('row')
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Images')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('images')
                                    ->collection('images')
                                    ->multiple()
                                    ->image()
                                    ->imageEditor()
                                    ->reorderable()
                                    ->maxFiles(12)
                                    ->helperText('Drag to reorder. First image appears as the product cover.')
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
                                            ->live()
                                            ->required(),
                                        TextInput::make('quantity')
                                            ->numeric()
                                            ->required()
                                            ->default(1)
                                            ->live(onBlur: true)
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
                                Placeholder::make('bom_cost_preview')
                                    ->label('Estimated BOM cost')
                                    ->content(function (Get $get): string {
                                        $metrics = self::bomMetrics($get('productComponents'), $get('price_inr'));

                                        return '₹'.number_format($metrics['cost'], 2);
                                    }),
                                Placeholder::make('bom_margin_preview')
                                    ->label('Current gross margin')
                                    ->content(function (Get $get): string {
                                        $metrics = self::bomMetrics($get('productComponents'), $get('price_inr'));

                                        if ($metrics['cost'] <= 0 || $metrics['price'] <= 0) {
                                            return 'Add components + price to view margin';
                                        }

                                        return number_format($metrics['margin_pct'], 1).'%';
                                    }),
                                Placeholder::make('bom_suggested_price')
                                    ->label('Suggested retail price')
                                    ->content(function (Get $get): string {
                                        $metrics = self::bomMetrics($get('productComponents'), $get('price_inr'));

                                        if ($metrics['cost'] <= 0) {
                                            return 'Add component costs to estimate';
                                        }

                                        return '₹'.number_format($metrics['suggested_price'], 2).' (at ~55% margin)';
                                    }),
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

    /**
     * @return array{cost: float, price: float, margin_pct: float, suggested_price: float}
     */
    private static function bomMetrics(mixed $rows, mixed $price): array
    {
        $items = is_array($rows) ? $rows : [];
        $componentIds = collect($items)
            ->pluck('component_id')
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $costMap = $componentIds->isEmpty()
            ? collect()
            : Component::query()
                ->whereIn('id', $componentIds)
                ->pluck('unit_cost_inr', 'id')
                ->map(fn ($value) => (float) $value);

        $bomCost = 0.0;
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $cid = isset($item['component_id']) && is_numeric($item['component_id']) ? (int) $item['component_id'] : null;
            $qty = isset($item['quantity']) && is_numeric($item['quantity']) ? (float) $item['quantity'] : 0.0;
            if (! $cid || $qty <= 0) {
                continue;
            }
            $unitCost = (float) ($costMap[$cid] ?? 0.0);
            $bomCost += $unitCost * $qty;
        }

        $priceValue = is_numeric($price) ? (float) $price : 0.0;
        $marginPct = $priceValue > 0 ? (($priceValue - $bomCost) / $priceValue) * 100 : 0.0;
        $suggestedAt55 = $bomCost > 0 ? $bomCost / 0.45 : 0.0;

        return [
            'cost' => round($bomCost, 2),
            'price' => round($priceValue, 2),
            'margin_pct' => round($marginPct, 2),
            'suggested_price' => round($suggestedAt55, 2),
        ];
    }
}
