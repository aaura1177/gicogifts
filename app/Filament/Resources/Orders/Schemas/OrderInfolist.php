<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order')
                    ->schema([
                        TextEntry::make('order_number'),
                        TextEntry::make('status'),
                        TextEntry::make('payment_gateway')
                            ->placeholder('—'),
                        TextEntry::make('subtotal_inr')
                            ->money('INR'),
                        TextEntry::make('shipping_inr')
                            ->money('INR'),
                        TextEntry::make('discount_inr')
                            ->money('INR'),
                        TextEntry::make('gst_inr')
                            ->money('INR'),
                        TextEntry::make('total_inr')
                            ->money('INR'),
                        TextEntry::make('coupon_code')
                            ->placeholder('—'),
                        TextEntry::make('paid_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('packed_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('shipped_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('delivered_at')
                            ->dateTime()
                            ->placeholder('—'),
                    ])
                    ->columns(2),
                Section::make('Customer')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Account')
                            ->placeholder('Guest'),
                        TextEntry::make('email'),
                        TextEntry::make('phone')
                            ->placeholder('—'),
                        IconEntry::make('is_gift')
                            ->boolean(),
                        TextEntry::make('gift_message')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Payment references')
                    ->schema([
                        TextEntry::make('razorpay_order_id')
                            ->placeholder('—'),
                        TextEntry::make('razorpay_payment_id')
                            ->placeholder('—'),
                        TextEntry::make('stripe_payment_intent_id')
                            ->placeholder('—'),
                    ])
                    ->columns(2)
                    ->collapsed(),
                Section::make('Shipping address (snapshot)')
                    ->schema([
                        CodeEntry::make('shipping_snapshot')
                            ->label('')
                            ->hiddenLabel()
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }
}
