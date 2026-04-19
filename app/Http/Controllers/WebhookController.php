<?php

namespace App\Http\Controllers;

use App\Events\OrderDelivered;
use App\Events\OrderShipped;
use App\Models\Order;
use App\Models\Shipment;
use App\Services\OrderPaymentFinalizer;
use App\Services\Payments\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Errors\SignatureVerificationError;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook as StripeWebhook;

class WebhookController extends Controller
{
    public function razorpay(Request $request, RazorpayService $razorpay, OrderPaymentFinalizer $finalizer): Response
    {
        $raw = $request->getContent();
        $sig = (string) $request->header('X-Razorpay-Signature', '');

        try {
            $razorpay->verifyWebhookSignature($raw, $sig);
        } catch (SignatureVerificationError|\RuntimeException $e) {
            Log::warning('Razorpay webhook signature failed', ['message' => $e->getMessage()]);

            return response('Invalid signature', 400);
        }

        $payload = json_decode($raw, true);
        if (! is_array($payload)) {
            return response('Invalid payload', 400);
        }

        $event = (string) ($payload['event'] ?? '');

        if ($event !== 'payment.captured') {
            return response('Ignored', 200);
        }

        $paymentEntity = $payload['payload']['payment']['entity'] ?? null;
        if (! is_array($paymentEntity)) {
            return response('Bad payload', 400);
        }

        $rpOrderId = (string) ($paymentEntity['order_id'] ?? '');
        $payId = (string) ($paymentEntity['id'] ?? '');
        if ($rpOrderId === '' || $payId === '') {
            return response('Missing ids', 400);
        }

        $order = Order::query()->where('razorpay_order_id', $rpOrderId)->first();
        if (! $order) {
            Log::warning('Razorpay webhook: unknown order', ['razorpay_order_id' => $rpOrderId]);

            return response('Order not found', 404);
        }

        $finalizer->markPaid(
            $order,
            'razorpay',
            $payId,
            $payload,
            razorpayPaymentId: $payId,
            stripePaymentIntentId: null,
        );

        return response('OK', 200);
    }

    public function stripe(Request $request, OrderPaymentFinalizer $finalizer): Response
    {
        $secret = config('services.stripe.webhook_secret');
        if (! is_string($secret) || $secret === '') {
            Log::warning('Stripe webhook secret missing');

            return response('Not configured', 503);
        }

        $payload = $request->getContent();
        $sigHeader = (string) $request->header('Stripe-Signature', '');

        try {
            $event = StripeWebhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException|\UnexpectedValueException $e) {
            Log::warning('Stripe webhook signature failed', ['message' => $e->getMessage()]);

            return response('Invalid signature', 400);
        }

        if ($event->type !== 'checkout.session.completed') {
            return response('Ignored', 200);
        }

        /** @var \Stripe\Checkout\Session $session */
        $session = $event->data->object;
        $orderId = (int) ($session->metadata->order_id ?? 0);
        if ($orderId <= 0) {
            return response('Missing order', 400);
        }

        $order = Order::query()->find($orderId);
        if (! $order) {
            return response('Order not found', 404);
        }

        $piRaw = $session->payment_intent ?? null;
        $pi = is_string($piRaw) ? $piRaw : (is_object($piRaw) && isset($piRaw->id) ? (string) $piRaw->id : null);

        $finalizer->markPaid(
            $order,
            'stripe',
            (string) $session->id,
            $event->toArray(),
            razorpayPaymentId: null,
            stripePaymentIntentId: $pi,
        );

        return response('OK', 200);
    }

    public function shiprocket(Request $request): Response
    {
        $expected = config('services.shiprocket.webhook_token');
        if (is_string($expected) && $expected !== '') {
            $token = (string) $request->header('X-Shiprocket-Token', '');
            if (! hash_equals($expected, $token)) {
                Log::warning('Shiprocket webhook: invalid token');

                return response('Unauthorized', 401);
            }
        }

        $payload = $request->all();
        if ($payload === [] && $request->getContent() !== '') {
            $decoded = json_decode($request->getContent(), true);
            $payload = is_array($decoded) ? $decoded : [];
        }

        $awb = $this->shiprocketPickString($payload, ['awb', 'awb_code', 'airway_bill_number', 'AWB', 'awb_no']);
        $srShipmentId = $this->shiprocketPickString($payload, ['shipment_id', 'sr_shipment_id', 'shipmentId']);
        $statusRaw = $this->shiprocketPickString($payload, [
            'current_status', 'shipment_status', 'status', 'current_status_id', 'awb_assigned_status',
        ]);

        if (($awb === null || $awb === '') && ($srShipmentId === null || $srShipmentId === '')) {
            Log::info('Shiprocket webhook: no AWB or shipment id');

            return response('OK', 200);
        }

        $shipment = null;
        if ($awb !== null && $awb !== '') {
            $shipment = Shipment::query()->where('awb_code', $awb)->first();
        }
        if (! $shipment && $srShipmentId !== null && $srShipmentId !== '') {
            $shipment = Shipment::query()->where('shiprocket_shipment_id', $srShipmentId)->first();
        }

        if (! $shipment) {
            Log::info('Shiprocket webhook: shipment not found', [
                'awb' => $awb,
                'shipment_id' => $srShipmentId,
            ]);

            return response('OK', 200);
        }

        $status = $statusRaw ?? 'updated';
        $updates = ['status' => $status];

        if ($awb !== null && $awb !== '' && $shipment->awb_code === null) {
            $updates['awb_code'] = $awb;
            $updates['tracking_url'] = 'https://shiprocket.co/tracking/'.$awb;
        }

        $shipment->fill($updates);
        $shipment->save();

        $order = $shipment->order;
        if ($order) {
            $this->syncOrderShipmentMilestones($order, $status);
        }

        return response('OK', 200);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function shiprocketPickString(array $payload, array $keys): ?string
    {
        foreach ($keys as $key) {
            $v = data_get($payload, $key);
            if (is_string($v) && $v !== '') {
                return $v;
            }
            if (is_numeric($v)) {
                return (string) $v;
            }
        }

        return null;
    }

    private function syncOrderShipmentMilestones(Order $order, string $status): void
    {
        $norm = strtolower($status);

        $isShipped = str_contains($norm, 'ship')
            || str_contains($norm, 'dispatch')
            || str_contains($norm, 'transit')
            || str_contains($norm, 'picked')
            || str_contains($norm, 'out for delivery')
            || str_contains($norm, 'ofd')
            || str_contains($norm, 'manifest');

        $isDelivered = str_contains($norm, 'delivered') || str_contains($norm, 'rto delivered');

        if ($isShipped && $order->shipped_at === null) {
            $order->forceFill(['shipped_at' => now()])->save();
            OrderShipped::dispatch($order->fresh());
        }

        if ($isDelivered && $order->delivered_at === null) {
            $order->forceFill(['delivered_at' => now()])->save();
            OrderDelivered::dispatch($order->fresh());
        }
    }
}
