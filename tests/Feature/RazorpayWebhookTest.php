<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RazorpayWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_razorpay_webhook_rejects_bad_signature(): void
    {
        config([
            'services.razorpay.key_id' => 'rzp_test',
            'services.razorpay.key_secret' => 'secret',
            'services.razorpay.webhook_secret' => 'whsec_test',
        ]);

        $this->postJson('/webhooks/razorpay', [
            'event' => 'payment.captured',
            'payload' => ['payment' => ['entity' => []]],
        ], [
            'X-Razorpay-Signature' => 'deadbeef',
        ])->assertStatus(400);
    }
}
