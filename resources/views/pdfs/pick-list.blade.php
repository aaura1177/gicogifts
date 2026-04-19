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
    <p class="muted">{{ $legalLine ?? '' }}</p>
    <p><strong>Warehouse pick list</strong> — Order {{ $order->order_number }}</p>
    <p>Printed: {{ now()->timezone(config('app.timezone'))->format('d M Y H:i') }}</p>
    @if($order->shipping_snapshot)
        <p>
            Ship to:
            {{ $order->shipping_snapshot['name'] ?? '' }},
            {{ $order->shipping_snapshot['line1'] ?? '' }},
            {{ $order->shipping_snapshot['city'] ?? '' }},
            {{ $order->shipping_snapshot['postal_code'] ?? '' }}
        </p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>SKU</th>
                <th class="right">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $line)
                <tr>
                    <td>{{ $line->product_name }}</td>
                    <td>{{ $line->sku }}</td>
                    <td class="right">{{ $line->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
