<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Mail\OrderConfirmed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail
{
    public function handle(OrderPaid $event): void
    {
        try {
            Mail::to($event->order->email)->send(new OrderConfirmed($event->order));
        } catch (\Throwable $e) {
            Log::warning('Order confirmation email failed: '.$e->getMessage());
        }
    }
}
