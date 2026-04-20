<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Facades\URL;
use Stripe\Checkout\Session;
use Stripe\Refund;
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

    /**
     * Payment intent id stored on the order after webhook, or resolved from the Checkout Session id on the payment row.
     */
    public function resolvePaymentIntentId(Order $order): ?string
    {
        $direct = $order->stripe_payment_intent_id;
        if (is_string($direct) && $direct !== '') {
            return $direct;
        }

        $payment = $order->payments()
            ->where('gateway', 'stripe')
            ->where('status', 'captured')
            ->latest()
            ->first();

        $sessionId = $payment?->transaction_id;
        if (! is_string($sessionId) || $sessionId === '' || ! str_starts_with($sessionId, 'cs_')) {
            return null;
        }

        return $this->getPaymentIntentIdFromCheckoutSession($sessionId);
    }

    public function getPaymentIntentIdFromCheckoutSession(string $checkoutSessionId): ?string
    {
        if (app()->environment('testing')) {
            return 'pi_test_'.$checkoutSessionId;
        }

        StripeClient::setApiKey((string) config('services.stripe.secret'));
        $session = Session::retrieve($checkoutSessionId);
        $piRaw = $session->payment_intent ?? null;
        if (is_string($piRaw) && $piRaw !== '') {
            return $piRaw;
        }
        if (is_object($piRaw) && isset($piRaw->id) && is_string($piRaw->id)) {
            return $piRaw->id;
        }

        return null;
    }

    /**
     * Refund a captured Stripe Checkout payment (via its PaymentIntent).
     *
     * @return array<string, mixed>
     */
    public function refundPaymentIntent(string $paymentIntentId, ?int $amountPaise = null, array $metadata = []): array
    {
        if (app()->environment('testing')) {
            return [
                'id' => 're_test_'.uniqid(),
                'object' => 'refund',
                'status' => 'succeeded',
                'payment_intent' => $paymentIntentId,
                'amount' => $amountPaise ?? 0,
                'metadata' => $metadata,
            ];
        }

        StripeClient::setApiKey((string) config('services.stripe.secret'));

        $params = [
            'payment_intent' => $paymentIntentId,
        ];
        if ($metadata !== []) {
            $params['metadata'] = collect($metadata)
                ->mapWithKeys(fn ($v, $k): array => [(string) $k => is_scalar($v) || $v === null ? (string) $v : json_encode($v)])
                ->all();
        }
        if (is_int($amountPaise) && $amountPaise > 0) {
            $params['amount'] = $amountPaise;
        }

        $refund = Refund::create($params);

        return $refund->toArray();
    }
}
