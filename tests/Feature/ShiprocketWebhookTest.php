<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiprocketWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_shiprocket_webhook_requires_token_when_configured(): void
    {
        config(['services.shiprocket.webhook_token' => 'expected-token']);

        $this->postJson('/webhooks/shiprocket', [
            'shipment_id' => '999',
            'current_status' => 'Shipped',
        ])->assertStatus(401);
    }

    public function test_shiprocket_webhook_updates_shipment_and_marks_order_shipped(): void
    {
        config(['services.shiprocket.webhook_token' => '']);

        $order = Order::query()->create([
            'order_number' => 'GG-TEST-'.strtoupper(substr(uniqid(), -10)),
            'email' => 'buyer@example.com',
            'status' => 'paid',
            'subtotal_inr' => 500,
            'shipping_inr' => 99,
            'discount_inr' => 0,
            'gst_inr' => 0,
            'total_inr' => 599,
            'paid_at' => now(),
        ]);

        Shipment::query()->create([
            'order_id' => $order->id,
            'shiprocket_order_id' => '111',
            'shiprocket_shipment_id' => '222',
            'awb_code' => null,
            'courier_name' => null,
            'status' => 'new',
            'tracking_url' => null,
        ]);

        $this->postJson('/webhooks/shiprocket', [
            'shipment_id' => '222',
            'awb' => 'SRTEST123',
            'current_status' => 'Shipped',
        ])->assertOk();

        $this->assertDatabaseHas('shipments', [
            'order_id' => $order->id,
            'awb_code' => 'SRTEST123',
            'status' => 'Shipped',
        ]);

        $order->refresh();
        $this->assertNotNull($order->shipped_at);
    }
}
