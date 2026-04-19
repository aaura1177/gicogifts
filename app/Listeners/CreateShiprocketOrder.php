<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\Shipment;
use App\Services\Shipping\ShiprocketService;
use Illuminate\Support\Facades\Log;

/**
 * Push paid domestic orders to Shiprocket (build plan §8).
 */
class CreateShiprocketOrder
{
    public function handle(OrderPaid $event, ShiprocketService $shiprocket): void
    {
        $order = $event->order->loadMissing('items.product');

        $snapshot = is_array($order->shipping_snapshot) ? $order->shipping_snapshot : [];
        $country = strtoupper((string) ($snapshot['country'] ?? 'IN'));
        if ($country !== 'IN') {
            return;
        }

        if (! $shiprocket->isConfigured()) {
            Log::info('CreateShiprocketOrder: Shiprocket credentials missing, skipping', ['order_id' => $order->id]);

            return;
        }

        try {
            $data = $shiprocket->createShipmentForOrder($order);
            if ($data === null) {
                Log::warning('CreateShiprocketOrder: no shipment data returned', ['order_id' => $order->id]);

                return;
            }

            $row = ['order_id' => $order->id];
            foreach ([
                'shiprocket_order_id' => $data['shiprocket_order_id'],
                'shiprocket_shipment_id' => $data['shiprocket_shipment_id'],
                'awb_code' => $data['awb_code'],
                'courier_name' => $data['courier_name'],
                'status' => $data['status'],
                'tracking_url' => $data['tracking_url'],
            ] as $key => $value) {
                if ($value !== null && $value !== '') {
                    $row[$key] = $value;
                }
            }

            Shipment::query()->updateOrCreate(['order_id' => $order->id], $row);
        } catch (\Throwable $e) {
            Log::error('CreateShiprocketOrder failed', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
