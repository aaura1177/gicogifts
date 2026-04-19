<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Facades\URL;
use Stripe\Checkout\Session;
use Stripe\Stripe as StripeClient;

class StripePaymentService
{
    public function isConfigured(): bool
    {
        $s = config('services.stripe.secret');

        return is_string($s) && $s !== '';
    }

    /**
     * Hosted Checkout for international customers (same INR total).
     *
     * @return array{url: string, session_id: string}
     */
    public function createCheckoutSession(Order $order): array
    {
        $amountPaise = (int) round((float) $order->total_inr * 100);

        if (app()->environment('testing')) {
            return [
                'url' => URL::route('checkout.success', ['order' => $order->id]).'?session_id=cs_test',
                'session_id' => 'cs_test_'.uniqid(),
            ];
        }

        StripeClient::setApiKey((string) config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'payment',
            'success_url' => URL::route('checkout.success', ['order' => $order->id]).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => URL::route('checkout.show'),
            'customer_email' => $order->email,
            'metadata' => [
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
            ],
            'line_items' => [
                [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => 'inr',
                        'unit_amount' => $amountPaise,
                        'product_data' => [
                            'name' => 'GicoGifts — '.$order->order_number,
                            'description' => 'Artisan gift order',
                        ],
                    ],
                ],
            ],
        ]);

        return [
            'url' => $session->url,
            'session_id' => $session->id,
        ];
    }
}
