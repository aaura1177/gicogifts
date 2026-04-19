<?php

namespace App\Services\Shipping;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShiprocketService
{
    private const BASE = 'https://apiv2.shiprocket.in/v1/external';

    public function isConfigured(): bool
    {
        $email = config('services.shiprocket.email');
        $password = config('services.shiprocket.password');

        return is_string($email) && $email !== '' && is_string($password) && $password !== '';
    }

    /**
     * @return array<string, mixed>|null  Normalized courier list + raw, or null on failure
     */
    public function checkServiceability(string $deliveryPostcode, float $weightKg, int $cod = 0): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $pickup = (string) config('services.shiprocket.pickup_postcode', '');
        $deliveryPostcode = preg_replace('/\D/', '', $deliveryPostcode) ?? '';

        if (strlen($pickup) < 4 || strlen($deliveryPostcode) < 4) {
            return null;
        }

        try {
            $token = $this->authToken();
            $res = Http::timeout(20)
                ->withToken($token)
                ->acceptJson()
                ->asJson()
                ->post(self::BASE.'/courier/serviceability/', [
                    'pickup_postcode' => $pickup,
                    'delivery_postcode' => $deliveryPostcode,
                    'weight' => max(0.05, round($weightKg, 3)),
                    'cod' => $cod,
                ]);

            if (! $res->successful()) {
                Log::warning('Shiprocket serviceability HTTP error', [
                    'status' => $res->status(),
                    'body' => $res->body(),
                ]);

                return null;
            }

            $json = $res->json();
            $data = is_array($json['data'] ?? null) ? $json['data'] : [];
            $companies = $data['available_courier_companies'] ?? $json['available_courier_companies'] ?? [];

            if (! is_array($companies)) {
                $companies = [];
            }

            $normalized = [];
            foreach ($companies as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $cid = $row['courier_company_id'] ?? $row['courier_id'] ?? null;
                $normalized[] = [
                    'courier_company_id' => is_numeric($cid) ? (int) $cid : null,
                    'courier_name' => (string) ($row['courier_name'] ?? $row['courier_company_name'] ?? ''),
                    'freight_charge' => isset($row['freight_charge']) ? (float) $row['freight_charge'] : null,
                    'estimated_delivery_days' => $row['estimated_delivery_days'] ?? $row['etd'] ?? null,
                    'raw' => $row,
                ];
            }

            return [
                'ok' => $normalized !== [],
                'couriers' => $normalized,
                'raw' => $json,
            ];
        } catch (\Throwable $e) {
            Log::warning('Shiprocket serviceability exception', ['message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Create Shiprocket order + shipment, assign AWB when possible.
     *
     * @return array{
     *   shiprocket_order_id: ?string,
     *   shiprocket_shipment_id: ?string,
     *   awb_code: ?string,
     *   courier_name: ?string,
     *   tracking_url: ?string,
     *   status: ?string,
     * }|null
     */
    public function createShipmentForOrder(Order $order): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $snapshot = is_array($order->shipping_snapshot) ? $order->shipping_snapshot : [];
        $country = strtoupper((string) ($snapshot['country'] ?? 'IN'));
        if ($country !== 'IN') {
            return null;
        }

        $token = $this->authToken();
        $payload = $this->buildAdhocPayload($order, $snapshot);

        try {
            $res = Http::timeout(45)
                ->withToken($token)
                ->acceptJson()
                ->asJson()
                ->post(self::BASE.'/orders/create/adhoc', $payload);

            if (! $res->successful()) {
                Log::error('Shiprocket create adhoc failed', [
                    'order_id' => $order->id,
                    'status' => $res->status(),
                    'body' => $res->body(),
                ]);

                return null;
            }

            $json = $res->json() ?? [];
            $ids = $this->extractShipmentIds($json);

            $awb = $ids['awb'];
            $courierName = $ids['courier_name'];
            $trackingUrl = $ids['tracking_url'];
            $status = $ids['status'] ?? 'new';

            $shipmentId = $ids['shipment_id'];
            $srOrderId = $ids['order_id'];

            if ($awb === null && $shipmentId !== null) {
                $assigned = $this->assignCheapestCourier($token, $shipmentId, (string) ($snapshot['postal_code'] ?? ''), $this->orderWeightKg($order));
                if (is_array($assigned)) {
                    $awb = $assigned['awb_code'] ?? $awb;
                    $courierName = $assigned['courier_name'] ?? $courierName;
                    $trackingUrl = $assigned['tracking_url'] ?? $trackingUrl;
                    $status = $assigned['status'] ?? $status;
                }
            }

            return [
                'shiprocket_order_id' => $srOrderId !== null ? (string) $srOrderId : null,
                'shiprocket_shipment_id' => $shipmentId !== null ? (string) $shipmentId : null,
                'awb_code' => $awb,
                'courier_name' => $courierName,
                'tracking_url' => $trackingUrl ?? ($awb ? 'https://shiprocket.co/tracking/'.$awb : null),
                'status' => $status,
            ];
        } catch (\Throwable $e) {
            Log::error('Shiprocket createShipmentForOrder exception', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    /**
     * Ask Shiprocket for a shipping label URL for the given Shiprocket shipment id.
     */
    public function fetchShippingLabelUrl(string $shiprocketShipmentId): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $id = (int) $shiprocketShipmentId;
        if ($id <= 0) {
            return null;
        }

        try {
            $token = $this->authToken();
            $res = Http::timeout(60)
                ->withToken($token)
                ->acceptJson()
                ->asJson()
                ->post(self::BASE.'/courier/generate/label', [
                    'shipment_id' => [$id],
                ]);

            if (! $res->successful()) {
                Log::warning('Shiprocket generate label failed', [
                    'shipment_id' => $id,
                    'status' => $res->status(),
                    'body' => $res->body(),
                ]);

                return null;
            }

            $json = $res->json();
            if (! is_array($json)) {
                return null;
            }

            $url = data_get($json, 'label_url')
                ?? data_get($json, 'response.label_url')
                ?? data_get($json, 'payload.label_url');

            return is_string($url) && str_starts_with($url, 'http') ? $url : null;
        } catch (\Throwable $e) {
            Log::warning('Shiprocket generate label exception', ['message' => $e->getMessage()]);

            return null;
        }
    }

    public function trackByAwb(string $awb): ?array
    {
        if ($awb === '') {
            return null;
        }

        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $token = $this->authToken();
            $res = Http::timeout(25)
                ->withToken($token)
                ->acceptJson()
                ->get(self::BASE.'/courier/track/awb/'.$awb);

            if (! $res->successful()) {
                Log::warning('Shiprocket track AWB failed', ['awb' => $awb, 'status' => $res->status()]);

                return null;
            }

            $json = $res->json();

            return is_array($json) ? $json : null;
        } catch (\Throwable $e) {
            Log::warning('Shiprocket track exception', ['awb' => $awb, 'message' => $e->getMessage()]);

            return null;
        }
    }

    private function authToken(): string
    {
        return Cache::remember('shiprocket_api_token', now()->addDays(9)->addHours(12), function (): string {
            $res = Http::timeout(20)->acceptJson()->asJson()->post(self::BASE.'/auth/login', [
                'email' => config('services.shiprocket.email'),
                'password' => config('services.shiprocket.password'),
            ]);

            if (! $res->successful()) {
                throw new \RuntimeException('Shiprocket auth failed: HTTP '.$res->status());
            }

            $token = $res->json('token');
            if (! is_string($token) || $token === '') {
                throw new \RuntimeException('Shiprocket auth: missing token');
            }

            return $token;
        });
    }

    /**
     * @param  array<string, mixed>  $snapshot
     * @return array<string, mixed>
     */
    private function buildAdhocPayload(Order $order, array $snapshot): array
    {
        $name = trim((string) ($snapshot['name'] ?? 'Customer'));
        $parts = preg_split('/\s+/', $name, 2, PREG_SPLIT_NO_EMPTY);
        $first = $parts[0] ?? $name;
        $last = $parts[1] ?? '.';

        $line1 = (string) ($snapshot['line1'] ?? '');
        if ($line1 === '' && is_string($order->notes) && $order->notes !== '') {
            $line1 = substr(preg_replace('/\s+/', ' ', $order->notes) ?? '', 0, 200);
        }
        $line2 = (string) ($snapshot['line2'] ?? '');
        $city = (string) ($snapshot['city'] ?? '');
        $state = (string) ($snapshot['state'] ?? '');
        $pin = preg_replace('/\D/', '', (string) ($snapshot['postal_code'] ?? '')) ?? '';
        if (strlen($pin) > 6) {
            $pin = substr($pin, -6);
        }
        if (strlen($pin) < 6) {
            $pin = str_pad($pin, 6, '0', STR_PAD_RIGHT);
        }

        $phone = $this->normalizeIndiaPhone((string) ($order->phone ?? ''));
        $email = (string) ($order->email ?? 'noreply@gicogifts.com');

        $weight = $this->orderWeightKg($order);
        [$len, $br, $ht] = $this->orderDimensionsCm($order);

        $items = [];
        foreach ($order->items as $line) {
            $unit = round((float) $line->unit_price_inr, 2);
            $items[] = [
                'name' => (string) $line->product_name,
                'sku' => (string) ($line->sku ?? 'SKU-'.$line->product_id),
                'units' => (int) $line->quantity,
                'selling_price' => $unit,
                'discount' => 0,
                'tax' => 0,
            ];
        }

        if ($items === []) {
            $items[] = [
                'name' => 'Order '.$order->order_number,
                'sku' => 'GG-ITEM',
                'units' => 1,
                'selling_price' => round((float) $order->total_inr, 2),
                'discount' => 0,
                'tax' => 0,
            ];
        }

        return [
            'order_id' => (string) $order->order_number,
            'order_date' => now()->format('d-m-Y'),
            'pickup_location' => (string) config('services.shiprocket.pickup_location', 'Primary'),
            'billing_customer_name' => $first,
            'billing_last_name' => $last,
            'billing_address' => $line1 !== '' ? $line1 : 'Address',
            'billing_address_2' => $line2,
            'billing_city' => $city !== '' ? $city : 'City',
            'billing_pincode' => $pin,
            'billing_state' => $state !== '' ? $state : 'State',
            'billing_country' => 'India',
            'billing_email' => $email,
            'billing_phone' => $phone,
            'shipping_is_billing' => true,
            'order_items' => $items,
            'payment_method' => 'Prepaid',
            'sub_total' => round((float) $order->total_inr, 2),
            'length' => $len,
            'breadth' => $br,
            'height' => $ht,
            'weight' => $weight,
        ];
    }

    private function normalizeIndiaPhone(string $raw): string
    {
        $digits = preg_replace('/\D/', '', $raw) ?? '';
        if (strlen($digits) >= 10) {
            $digits = substr($digits, -10);
        }
        if (strlen($digits) < 10) {
            $digits = str_pad($digits, 10, '9', STR_PAD_RIGHT);
        }

        return $digits;
    }

    private function orderWeightKg(Order $order): float
    {
        $grams = 0.0;
        foreach ($order->items as $line) {
            $w = (int) ($line->product?->weight_grams ?? 200);
            $grams += max(1, $w) * (int) $line->quantity;
        }
        $kg = $grams / 1000;

        return max(0.05, round($kg, 3));
    }

    /**
     * @return array{0: float, 1: float, 2: float}
     */
    private function orderDimensionsCm(Order $order): array
    {
        $l = 20.0;
        $b = 15.0;
        $h = 10.0;
        foreach ($order->items as $line) {
            $p = $line->product;
            if (! $p) {
                continue;
            }
            $l = max($l, (float) ($p->length_cm ?? 0));
            $b = max($b, (float) ($p->width_cm ?? 0));
            $h = max($h, (float) ($p->height_cm ?? 0));
        }

        return [round($l, 2), round($b, 2), round($h, 2)];
    }

    /**
     * @param  array<string, mixed>  $json
     * @return array{
     *   order_id: int|string|null,
     *   shipment_id: int|string|null,
     *   awb: ?string,
     *   courier_name: ?string,
     *   tracking_url: ?string,
     *   status: ?string,
     * }
     */
    private function extractShipmentIds(array $json): array
    {
        $payload = is_array($json['payload'] ?? null) ? $json['payload'] : $json;

        $awb = $payload['awb'] ?? $payload['awb_code'] ?? $json['awb'] ?? $json['awb_code'] ?? null;
        $awb = is_string($awb) && $awb !== '' ? $awb : (is_numeric($awb) ? (string) $awb : null);

        return [
            'order_id' => $payload['order_id'] ?? $json['order_id'] ?? null,
            'shipment_id' => $payload['shipment_id'] ?? $json['shipment_id'] ?? null,
            'awb' => $awb,
            'courier_name' => isset($payload['courier']) && is_string($payload['courier']) ? $payload['courier'] : (isset($json['courier_name']) ? (string) $json['courier_name'] : null),
            'tracking_url' => isset($payload['tracking_url']) && is_string($payload['tracking_url']) ? $payload['tracking_url'] : (isset($json['tracking_url']) ? (string) $json['tracking_url'] : null),
            'status' => isset($payload['status']) && is_string($payload['status']) ? $payload['status'] : (isset($json['status']) ? (string) $json['status'] : null),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function assignCheapestCourier(string $token, int|string $shipmentId, string $deliveryPin, float $weightKg): ?array
    {
        $svc = $this->checkServiceability($deliveryPin, $weightKg, 0);
        if ($svc === null || $svc['couriers'] === []) {
            return null;
        }

        $best = null;
        $bestRate = PHP_FLOAT_MAX;
        foreach ($svc['couriers'] as $c) {
            $id = $c['courier_company_id'] ?? null;
            if ($id === null) {
                continue;
            }
            $rate = $c['freight_charge'] ?? PHP_FLOAT_MAX;
            if ($rate < $bestRate) {
                $bestRate = $rate;
                $best = $c;
            }
        }

        if ($best === null && $svc['couriers'] !== []) {
            $best = $svc['couriers'][0];
        }

        if ($best === null || ! isset($best['courier_company_id'])) {
            return null;
        }

        $res = Http::timeout(30)
            ->withToken($token)
            ->acceptJson()
            ->asJson()
            ->post(self::BASE.'/courier/assign/awb', [
                'shipment_id' => (int) $shipmentId,
                'courier_id' => (int) $best['courier_company_id'],
            ]);

        if (! $res->successful()) {
            Log::warning('Shiprocket assign AWB failed', [
                'shipment_id' => $shipmentId,
                'status' => $res->status(),
                'body' => $res->body(),
            ]);

            return null;
        }

        $json = $res->json() ?? [];
        if (! is_array($json)) {
            return null;
        }

        $ids = $this->extractShipmentIds($json);

        return [
            'awb_code' => $ids['awb'],
            'courier_name' => $ids['courier_name'] ?? (string) ($best['courier_name'] ?? ''),
            'tracking_url' => $ids['tracking_url'] ?? ($ids['awb'] ? 'https://shiprocket.co/tracking/'.$ids['awb'] : null),
            'status' => $ids['status'] ?? 'booked',
        ];
    }
}
