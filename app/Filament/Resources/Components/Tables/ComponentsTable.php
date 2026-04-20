<?php

namespace App\Filament\Resources\Components\Tables;

use App\Models\Component;
use App\Models\InventoryMovement;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ComponentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('unit_cost_inr')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stock_on_hand')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reorder_threshold')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('supplier_name')
                    ->searchable(),
                TextColumn::make('supplier_contact')
                    ->searchable(),
                TextColumn::make('hsn_code')
                    ->searchable(),
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
                    BulkAction::make('bulkRestock')
                        ->label('Bulk restock')
                        ->icon(Heroicon::OutlinedArrowUpCircle)
                        ->form([
                            TextInput::make('qty')
                                ->label('Quantity to add')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->default(1)
                                ->helperText('Applied to each selected component.'),
                            Textarea::make('note')
                                ->label('Note')
                                ->rows(2)
                                ->maxLength(500)
                                ->placeholder('Optional note stored on inventory movements'),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $qty = (float) $data['qty'];
                            if ($qty <= 0) {
                                return;
                            }
                            $note = trim((string) ($data['note'] ?? ''));
                            $movementNote = $note !== '' ? $note : 'Bulk restock (admin)';

                            DB::transaction(function () use ($records, $qty, $movementNote): void {
                                foreach ($records as $component) {
                                    /** @var Component $component */
                                    $locked = Component::query()->whereKey($component->getKey())->lockForUpdate()->first();
                                    if (! $locked) {
                                        continue;
                                    }
                                    $locked->increment('stock_on_hand', $qty);
                                    InventoryMovement::query()->create([
                                        'component_id' => $locked->id,
                                        'type' => 'adjustment',
                                        'qty_change' => $qty,
                                        'reference_type' => null,
                                        'reference_id' => null,
                                        'note' => $movementNote,
                                    ]);
                                }
                            });

                            Notification::make()
                                ->title('Stock updated')
                                ->body('Added '.(string) $qty.' unit(s) per selected component.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
