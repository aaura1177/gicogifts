<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayService
{
    public function isConfigured(): bool
    {
        $k = config('services.razorpay.key_id');
        $s = config('services.razorpay.key_secret');

        return is_string($k) && $k !== '' && is_string($s) && $s !== '';
    }

    /**
     * @return array{id: string, amount: int, currency: string}
     */
    public function createOrder(Order $order): array
    {
        $amountPaise = (int) round((float) $order->total_inr * 100);

        if (app()->environment('testing')) {
            return [
                'id' => 'order_test_'.Str::lower(Str::random(10)),
                'amount' => $amountPaise,
                'currency' => 'INR',
            ];
        }

        $api = new Api(config('services.razorpay.key_id'), config('services.razorpay.key_secret'));
        $rp = $api->order->create([
            'receipt' => $order->order_number,
            'amount' => $amountPaise,
            'currency' => 'INR',
            'notes' => [
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
            ],
        ]);

        return [
            'id' => $rp['id'],
            'amount' => (int) $rp['amount'],
            'currency' => (string) $rp['currency'],
        ];
    }

    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): bool
    {
        try {
            $api = new Api(config('services.razorpay.key_id'), config('services.razorpay.key_secret'));
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ]);

            return true;
        } catch (SignatureVerificationError) {
            return false;
        }
    }

    public function verifyWebhookSignature(string $rawBody, string $signatureHeader): void
    {
        $secret = config('services.razorpay.webhook_secret');
        if (! is_string($secret) || $secret === '') {
            throw new \RuntimeException('Razorpay webhook secret is not configured.');
        }

        $api = new Api(config('services.razorpay.key_id'), config('services.razorpay.key_secret'));
        $api->utility->verifyWebhookSignature($rawBody, $signatureHeader, $secret);
    }

    /**
     * Refund a captured Razorpay payment.
     *
     * @return array<string, mixed>
     */
    public function refundPayment(string $paymentId, ?int $amountPaise = null, array $notes = []): array
    {
        if (app()->environment('testing')) {
            return [
                'id' => 'rfnd_test_'.Str::lower(Str::random(10)),
                'entity' => 'refund',
                'payment_id' => $paymentId,
                'amount' => $amountPaise,
                'status' => 'processed',
                'notes' => $notes,
            ];
        }

        $api = new Api(config('services.razorpay.key_id'), config('services.razorpay.key_secret'));
        $params = ['notes' => $notes];
        if (is_int($amountPaise) && $amountPaise > 0) {
            $params['amount'] = $amountPaise;
        }

        /** @var array<string, mixed> $refund */
        $refund = $api->payment->fetch($paymentId)->refund($params)->toArray();

        return $refund;
    }
}
