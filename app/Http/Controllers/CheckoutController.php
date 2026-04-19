<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\RazorpayService;
use App\Services\Payments\StripePaymentService;
use App\Services\Shipping\ShiprocketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function serviceability(Request $request, ShiprocketService $shiprocket): JsonResponse
    {
        $validated = $request->validate([
            'postal_code' => ['required', 'string', 'max:16'],
            'country' => ['nullable', 'string', 'max:2'],
        ]);

        $country = strtoupper((string) ($validated['country'] ?? 'IN'));
        if ($country !== 'IN') {
            return response()->json([
                'ok' => true,
                'serviceable' => true,
                'message' => 'International delivery via Stripe checkout.',
            ]);
        }

        if (! $shiprocket->isConfigured()) {
            return response()->json([
                'ok' => true,
                'serviceable' => true,
                'message' => 'Delivery options load at dispatch. Flat ₹99 (free above ₹2,000).',
            ]);
        }

        $cart = Cart::current()->load(['items.product']);
        $weightKg = $this->cartWeightKg($cart);

        $result = $shiprocket->checkServiceability((string) $validated['postal_code'], $weightKg);
        if ($result === null) {
            return response()->json([
                'ok' => false,
                'serviceable' => null,
                'message' => 'Could not verify this PIN code right now. You can still try to place your order.',
            ]);
        }

        if (! $result['ok']) {
            return response()->json([
                'ok' => true,
                'serviceable' => false,
                'message' => 'Sorry, we cannot ship to this PIN code yet. Try another address or contact us for corporate orders.',
            ]);
        }

        return response()->json([
            'ok' => true,
            'serviceable' => true,
            'message' => 'Delivery expected in about 3–6 business days. Flat ₹99 (free above ₹2,000).',
        ]);
    }

    public function show(): View|RedirectResponse
    {
        $cart = Cart::current()->load(['items.product']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('shop.index')->with('status', 'Your cart is empty.');
        }

        return view('frontend.checkout.show', compact('cart'));
    }

    public function place(Request $request): JsonResponse|RedirectResponse
    {
        $cart = Cart::current()->load(['items.product']);

        if ($cart->items->isEmpty()) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'Your cart is empty.'], 422);
            }

            return redirect()->route('shop.index')->with('status', 'Your cart is empty.');
        }

        if (! $request->expectsJson()) {
            return redirect()
                ->route('checkout.show')
                ->withErrors(['checkout' => 'Please complete checkout using the Pay button on this page.']);
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:32'],
            'name' => ['required', 'string', 'max:255'],
            'line1' => ['required', 'string', 'max:255'],
            'line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'max:120'],
            'postal_code' => ['required', 'string', 'max:16'],
            'country' => ['nullable', 'string', 'max:2'],
            'payment_gateway' => ['required', 'string', 'in:razorpay,stripe'],
        ]);

        $country = strtoupper((string) ($validated['country'] ?? 'IN'));
        $gateway = $validated['payment_gateway'];

        if ($country === 'IN' && $gateway === 'stripe') {
            throw ValidationException::withMessages([
                'payment_gateway' => 'For India, pay with Razorpay (cards, UPI, netbanking).',
            ]);
        }

        if ($country !== 'IN' && $gateway === 'razorpay') {
            throw ValidationException::withMessages([
                'payment_gateway' => 'For addresses outside India, pay with Stripe.',
            ]);
        }

        $shiprocket = app(ShiprocketService::class);
        if ($country === 'IN' && $shiprocket->isConfigured()) {
            $svc = $shiprocket->checkServiceability((string) $validated['postal_code'], $this->cartWeightKg($cart));
            if (is_array($svc) && $svc['ok'] === false) {
                throw ValidationException::withMessages([
                    'postal_code' => 'We cannot ship to this PIN code yet. Try another address or contact us for help.',
                ]);
            }
        }

        $subtotal = $cart->subtotalInr();
        $shipping = $subtotal >= 2000 ? 0.0 : 99.0;
        $total = $subtotal + $shipping;

        $razorpay = app(RazorpayService::class);
        $stripe = app(StripePaymentService::class);

        if ($gateway === 'razorpay' && ! $razorpay->isConfigured()) {
            throw ValidationException::withMessages([
                'payment_gateway' => 'Razorpay is not configured. Add RAZORPAY_KEY and RAZORPAY_SECRET to .env.',
            ]);
        }

        if ($gateway === 'stripe' && ! $stripe->isConfigured()) {
            throw ValidationException::withMessages([
                'payment_gateway' => 'Stripe is not configured. Add STRIPE_SECRET to .env.',
            ]);
        }

        try {
            $order = DB::transaction(function () use ($request, $cart, $subtotal, $shipping, $total, $validated, $gateway, $country, $razorpay, $stripe) {
                $order = Order::query()->create([
                    'order_number' => 'GG-'.now()->format('ym').'-'.strtoupper(substr(uniqid(), -8)),
                    'user_id' => $request->user()?->id,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'status' => 'pending',
                    'subtotal_inr' => $subtotal,
                    'shipping_inr' => $shipping,
                    'discount_inr' => 0,
                    'gst_inr' => 0,
                    'total_inr' => $total,
                    'payment_gateway' => $gateway,
                    'notes' => 'Checkout address: '.$validated['name'].', '.$validated['line1'].', '
                        .$validated['city'].' '.$validated['postal_code'].', '.$country,
                    'shipping_snapshot' => [
                        'name' => $validated['name'],
                        'line1' => $validated['line1'],
                        'line2' => $validated['line2'] ?? null,
                        'city' => $validated['city'],
                        'state' => $validated['state'],
                        'postal_code' => $validated['postal_code'],
                        'country' => $country,
                    ],
                ]);

                foreach ($cart->items as $line) {
                    $p = $line->product;
                    $lineTotal = (float) $line->unit_price_inr * (int) $line->quantity;
                    $order->items()->create([
                        'product_id' => $p->id,
                        'product_name' => $p->name,
                        'sku' => $p->sku,
                        'quantity' => $line->quantity,
                        'unit_price_inr' => $line->unit_price_inr,
                        'line_total_inr' => $lineTotal,
                        'hsn_code' => $p->hsn_code,
                        'gst_rate' => $p->gst_rate,
                    ]);
                }

                if ($gateway === 'razorpay') {
                    $rpOrder = $razorpay->createOrder($order);
                    $order->update([
                        'razorpay_order_id' => $rpOrder['id'],
                    ]);
                } else {
                    $session = $stripe->createCheckoutSession($order);
                    Payment::query()->create([
                        'order_id' => $order->id,
                        'gateway' => 'stripe',
                        'transaction_id' => $session['session_id'],
                        'amount_inr' => $total,
                        'status' => 'pending',
                        'raw_response' => ['checkout_url' => $session['url']],
                    ]);
                }

                CartItem::query()->where('cart_id', $cart->id)->delete();

                return $order->fresh();
            });
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
            }

            throw $e;
        }

        $request->session()->push('recent_checkout_orders', $order->id);

        if (! $request->expectsJson()) {
            return redirect()
                ->route('checkout.success', $order)
                ->with('status', 'Order created. Complete payment using the instructions on the next page.');
        }

        if ($gateway === 'razorpay') {
            $order->refresh();

            return response()->json([
                'ok' => true,
                'gateway' => 'razorpay',
                'order_id' => $order->id,
                'razorpay_key' => config('services.razorpay.key_id'),
                'razorpay_order_id' => $order->razorpay_order_id,
                'amount' => (int) round((float) $order->total_inr * 100),
                'currency' => 'INR',
                'prefill' => [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'contact' => $validated['phone'] ?? '',
                ],
                'success_url' => route('checkout.success', $order),
            ]);
        }

        $order->refresh();
        $pending = Payment::query()
            ->where('order_id', $order->id)
            ->where('gateway', 'stripe')
            ->where('status', 'pending')
            ->latest()
            ->first();

        return response()->json([
            'ok' => true,
            'gateway' => 'stripe',
            'order_id' => $order->id,
            'stripe_checkout_url' => $pending?->raw_response['checkout_url'] ?? route('checkout.success', $order),
            'success_url' => route('checkout.success', $order),
        ]);
    }

    public function success(Request $request, Order $order): View
    {
        $allowed = collect($request->session()->get('recent_checkout_orders', []))->unique()->all();
        abort_unless(in_array($order->id, $allowed, true), 403);

        return view('frontend.checkout.success', [
            'order' => $order->fresh(['items']),
        ]);
    }

    private function cartWeightKg(Cart $cart): float
    {
        $grams = 0.0;
        foreach ($cart->items as $line) {
            $p = $line->product;
            $w = (int) ($p?->weight_grams ?? 200);
            $grams += max(1, $w) * (int) $line->quantity;
        }

        return max(0.05, round($grams / 1000, 3));
    }
}
