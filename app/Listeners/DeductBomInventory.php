<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\Component;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class DeductBomInventory
{
    public function handle(OrderPaid $event): void
    {
        $order = $event->order->load(['items.product.components']);

        DB::transaction(function () use ($order): void {
            foreach ($order->items as $item) {
                $product = $item->product;
                if (! $product || ! $product->is_box) {
                    continue;
                }

                foreach ($product->components as $component) {
                    $qty = (float) $component->pivot->quantity * (int) $item->quantity;
                    if ($qty <= 0) {
                        continue;
                    }

                    $row = Component::query()->whereKey($component->id)->lockForUpdate()->first();
                    if (! $row) {
                        continue;
                    }

                    $deductUnits = (int) max(0, round($qty));
                    $newStock = max(0, (int) $row->stock_on_hand - $deductUnits);
                    $row->update(['stock_on_hand' => $newStock]);

                    InventoryMovement::query()->create([
                        'component_id' => $row->id,
                        'type' => 'order',
                        'qty_change' => -1 * $deductUnits,
                        'reference_type' => $order::class,
                        'reference_id' => $order->id,
                        'note' => 'Order '.$order->order_number.' item '.$item->product_name,
                    ]);
                }
            }
        });
    }
}
