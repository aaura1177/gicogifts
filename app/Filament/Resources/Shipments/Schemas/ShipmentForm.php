<?php

namespace App\Filament\Resources\Shipments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required(),
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
}
