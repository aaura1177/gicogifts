<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use Illuminate\Support\Facades\Http;

class NotifySlackOfOrder
{
    public function handle(OrderPaid $event): void
    {
        $url = config('services.slack.webhook_url');
        if (! is_string($url) || $url === '') {
            return;
        }

        $order = $event->order;
        Http::asJson()->post($url, [
            'text' => 'Paid order '.$order->order_number.' — ₹'.number_format((float) $order->total_inr, 2).' — '.$order->email,
        ]);
    }
}
