<?php

namespace App\Services;

use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class OrderPaymentFinalizer
{
    /**
     * Mark an order paid exactly once; persist payment row; fire OrderPaid after commit.
     *
     * @param  array<string, mixed>  $rawResponse
     */
    public function markPaid(
        Order $order,
        string $gateway,
        string $transactionId,
        array $rawResponse = [],
        ?string $razorpayPaymentId = null,
        ?string $stripePaymentIntentId = null,
    ): void {
        $dispatchId = null;

        DB::transaction(function () use ($order, $gateway, $transactionId, $rawResponse, $razorpayPaymentId, $stripePaymentIntentId, &$dispatchId): void {
            /** @var Order|null $locked */
            $locked = Order::query()->whereKey($order->id)->lockForUpdate()->first();
            if (! $locked || $locked->status === 'paid') {
                return;
            }

            if ($gateway === 'stripe') {
                $pending = Payment::query()
                    ->where('order_id', $locked->id)
                    ->where('gateway', 'stripe')
                    ->where('status', 'pending')
                    ->lockForUpdate()
                    ->first();
                if ($pending) {
                    $pending->update([
                        'transaction_id' => $transactionId,
                        'status' => 'captured',
                        'raw_response' => $rawResponse,
                    ]);
                } else {
                    Payment::query()->create([
                        'order_id' => $locked->id,
                        'gateway' => 'stripe',
                        'transaction_id' => $transactionId,
                        'amount_inr' => $locked->total_inr,
                        'status' => 'captured',
                        'raw_response' => $rawResponse,
                    ]);
                }
            } else {
                Payment::query()->create([
                    'order_id' => $locked->id,
                    'gateway' => $gateway,
                    'transaction_id' => $transactionId,
                    'amount_inr' => $locked->total_inr,
                    'status' => 'captured',
                    'raw_response' => $rawResponse,
                ]);
            }

            $locked->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_gateway' => $gateway,
                'razorpay_payment_id' => $razorpayPaymentId,
                'stripe_payment_intent_id' => $stripePaymentIntentId,
            ]);

            OrderStatusHistory::query()->create([
                'order_id' => $locked->id,
                'status' => 'paid',
                'note' => 'Payment confirmed via '.$gateway,
                'created_by' => null,
            ]);

            $dispatchId = $locked->id;
        });

        if ($dispatchId !== null) {
            OrderPaid::dispatch(Order::query()->with('items')->findOrFail($dispatchId));
        }
    }
}
