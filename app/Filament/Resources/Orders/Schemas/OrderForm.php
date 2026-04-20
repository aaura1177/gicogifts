<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('subtotal_inr')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('shipping_inr')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('discount_inr')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('gst_inr')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('total_inr')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Select::make('shipping_address_id')
                    ->relationship('shippingAddress', 'name')
                    ->default(null),
                Select::make('billing_address_id')
                    ->relationship('billingAddress', 'name')
                    ->default(null),
                TextInput::make('razorpay_order_id')
                    ->default(null),
                TextInput::make('razorpay_payment_id')
                    ->default(null),
                TextInput::make('stripe_payment_intent_id')
                    ->default(null),
                TextInput::make('payment_gateway')
                    ->default(null),
                TextInput::make('coupon_code')
                    ->default(null),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('shipping_snapshot')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('is_gift')
                    ->required(),
                Textarea::make('gift_message')
                    ->default(null)
                    ->columnSpanFull(),
                DateTimePicker::make('paid_at'),
                DateTimePicker::make('packed_at'),
                DateTimePicker::make('shipped_at'),
                DateTimePicker::make('delivered_at'),
            ]);
    }
}
