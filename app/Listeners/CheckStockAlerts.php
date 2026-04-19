<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\Component;
use App\Models\StockAlertsLog;
use Illuminate\Support\Facades\Http;

class CheckStockAlerts
{
    public function handle(OrderPaid $event): void
    {
        $order = $event->order->load(['items.product.components']);

        $componentIds = collect();
        foreach ($order->items as $item) {
            $product = $item->product;
            if (! $product || ! $product->is_box) {
                continue;
            }
            $componentIds = $componentIds->merge($product->components->pluck('id'));
        }

        foreach ($componentIds->unique() as $cid) {
            $c = Component::query()->find($cid);
            if (! $c) {
                continue;
            }
            if ((int) $c->stock_on_hand > (int) $c->reorder_threshold) {
                continue;
            }

            StockAlertsLog::query()->create([
                'component_id' => $c->id,
                'level_at_alert' => (int) $c->stock_on_hand,
                'notified_at' => now(),
            ]);

            $url = config('services.slack.webhook_url');
            if (is_string($url) && $url !== '') {
                Http::asJson()->post($url, [
                    'text' => 'Low stock: '.$c->name.' (SKU '.$c->sku.') is at '.$c->stock_on_hand.' (threshold '.$c->reorder_threshold.').',
                ]);
            }
        }
    }
}
