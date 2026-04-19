<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #2C1810; }
        h1 { font-size: 18px; margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ECDBC4; padding: 6px 8px; text-align: left; }
        th { background: #F7EEE3; }
        .muted { color: #6D3620; font-size: 10px; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>{{ config('app.name') }}</h1>
    <p class="muted">Rajasthan, unboxed.</p>
    <p><strong>Invoice</strong> — Order {{ $order->order_number }}</p>
    <p>Date: {{ $order->paid_at?->timezone(config('app.timezone'))->format('d M Y H:i') ?? $order->updated_at->timezone(config('app.timezone'))->format('d M Y H:i') }}</p>
    <p>Bill to: {{ $order->email }}@if($order->phone) · {{ $order->phone }}@endif</p>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>SKU</th>
                <th class="right">Qty</th>
                <th class="right">Unit ₹</th>
                <th class="right">Line ₹</th>
                <th>HSN</th>
                <th class="right">GST %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $line)
                <tr>
                    <td>{{ $line->product_name }}</td>
                    <td>{{ $line->sku }}</td>
                    <td class="right">{{ $line->quantity }}</td>
                    <td class="right">{{ number_format((float) $line->unit_price_inr, 2) }}</td>
                    <td class="right">{{ number_format((float) $line->line_total_inr, 2) }}</td>
                    <td>{{ $line->hsn_code ?? '—' }}</td>
                    <td class="right">{{ $line->gst_rate !== null ? number_format((float) $line->gst_rate, 2) : '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 50%; margin-left: auto; margin-top: 12px; border: none;">
        <tr><td>Subtotal</td><td class="right">₹{{ number_format((float) $order->subtotal_inr, 2) }}</td></tr>
        <tr><td>Shipping</td><td class="right">₹{{ number_format((float) $order->shipping_inr, 2) }}</td></tr>
        <tr><td>Discount</td><td class="right">₹{{ number_format((float) $order->discount_inr, 2) }}</td></tr>
        <tr><td>GST</td><td class="right">₹{{ number_format((float) $order->gst_inr, 2) }}</td></tr>
        <tr><th>Total</th><th class="right">₹{{ number_format((float) $order->total_inr, 2) }}</th></tr>
    </table>

    @if($legalLine)
        <p class="muted" style="margin-top: 24px;">{{ $legalLine }}</p>
    @endif
</body>
</html>
