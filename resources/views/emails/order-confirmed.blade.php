<x-mail::message>
# Thank you for your order

**{{ $order->order_number }}** is paid. We will pack and ship from Udaipur as soon as possible.

**Total:** ₹{{ number_format((float) $order->total_inr, 2) }}

Your invoice is attached as a PDF.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
