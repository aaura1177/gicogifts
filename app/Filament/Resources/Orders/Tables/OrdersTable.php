<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('subtotal_inr')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('shipping_inr')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_inr')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gst_inr')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_inr')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('shippingAddress.name')
                    ->searchable(),
                TextColumn::make('billingAddress.name')
                    ->searchable(),
                TextColumn::make('razorpay_order_id')
                    ->searchable(),
                TextColumn::make('razorpay_payment_id')
                    ->searchable(),
                TextColumn::make('stripe_payment_intent_id')
                    ->searchable(),
                TextColumn::make('payment_gateway')
                    ->searchable(),
                TextColumn::make('coupon_code')
                    ->searchable(),
                IconColumn::make('is_gift')
                    ->boolean(),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('packed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('shipped_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('delivered_at')
                    ->dateTime()
                    ->sortable(),
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
