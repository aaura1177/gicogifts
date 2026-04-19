<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Services\Shipping\ShiprocketService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function show(string $awb, ShiprocketService $shiprocket): View
    {
        $shipment = Shipment::query()
            ->where('awb_code', $awb)
            ->with('order')
            ->first();

        $live = null;
        if ($shiprocket->isConfigured() && $awb !== '') {
            $live = Cache::remember(
                'shiprocket_track_'.$awb,
                now()->addMinutes(30),
                fn () => $shiprocket->trackByAwb($awb)
            );
        }

        $timeline = $this->buildTimeline($shipment, $live);
        $trackUrl = route('track', ['awb' => $awb]);
        $whatsappShare = 'https://wa.me/?text='.rawurlencode('Track my GicoGifts order: '.$trackUrl);

        return view('frontend.tracking.show', compact('shipment', 'awb', 'live', 'timeline', 'trackUrl', 'whatsappShare'));
    }

    /**
     * @param  array<string, mixed>|null  $live
     * @return list<array{label: string, done: bool, detail: ?string}>
     */
    private function buildTimeline(?Shipment $shipment, ?array $live): array
    {
        $order = $shipment?->order;
        $status = strtolower((string) ($shipment?->status ?? ''));
        $liveStatus = strtolower((string) ($live['current_status'] ?? $live['shipment_status'] ?? ''));

        $scanHint = null;
        $scan = $live['tracking_data'] ?? $live['shipment_track_activities'] ?? null;
        if (is_array($scan) && $scan !== []) {
            $last = $scan[array_key_last($scan)];
            if (is_array($last)) {
                $scanHint = trim((string) ($last['activity'] ?? $last['status'] ?? '').' '
                    .(string) ($last['location'] ?? ''));
            }
        }

        $hasShipped = $order?->shipped_at !== null
            || str_contains($status, 'ship')
            || str_contains($status, 'dispatch')
            || str_contains($status, 'transit')
            || str_contains($liveStatus, 'ship')
            || str_contains($liveStatus, 'transit');

        $hasOfd = str_contains($status, 'out for delivery')
            || str_contains($status, 'ofd')
            || str_contains($liveStatus, 'out for delivery')
            || str_contains($liveStatus, 'ofd');

        $hasDelivered = $order?->delivered_at !== null
            || str_contains($status, 'delivered')
            || str_contains($liveStatus, 'delivered');

        $packed = (bool) $order?->packed_at || $hasShipped;

        return [
            ['label' => 'Placed', 'done' => $shipment !== null, 'detail' => $order?->paid_at?->format('M j, Y')],
            ['label' => 'Packed', 'done' => $packed, 'detail' => $order?->packed_at?->format('M j, Y')],
            ['label' => 'Shipped', 'done' => $hasShipped, 'detail' => $order?->shipped_at?->format('M j, Y')],
            ['label' => 'Out for delivery', 'done' => $hasOfd, 'detail' => $scanHint],
            ['label' => 'Delivered', 'done' => $hasDelivered, 'detail' => $order?->delivered_at?->format('M j, Y')],
        ];
    }
}
