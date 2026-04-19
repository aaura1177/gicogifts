<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(): View
    {
        $cart = Cart::current()->load(['items.product.media']);

        return view('frontend.cart.show', compact('cart'));
    }

    public function add(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::query()->where('id', $data['product_id'])->where('is_active', true)->firstOrFail();
        $qty = $data['quantity'] ?? 1;

        $cart = Cart::current();

        $item = CartItem::query()->firstOrNew([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $item->quantity = (int) ($item->exists ? $item->quantity + $qty : $qty);
        $item->unit_price_inr = $product->price_inr;
        $item->save();

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'item_count' => $cart->fresh()->itemCount(),
                'message' => 'Added to cart.',
            ]);
        }

        return redirect()
            ->route('cart.show')
            ->with('status', 'Added to cart.');
    }

    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'cart_item_id' => ['required', 'integer', 'exists:cart_items,id'],
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $cart = Cart::current();

        $item = CartItem::query()
            ->where('id', $data['cart_item_id'])
            ->where('cart_id', $cart->id)
            ->firstOrFail();

        if ($data['quantity'] === 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $data['quantity']]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'item_count' => $cart->fresh()->itemCount(),
            ]);
        }

        return redirect()->route('cart.show')->with('status', 'Cart updated.');
    }

    public function remove(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'cart_item_id' => ['required', 'integer', 'exists:cart_items,id'],
        ]);

        $cart = Cart::current();

        CartItem::query()
            ->where('id', $data['cart_item_id'])
            ->where('cart_id', $cart->id)
            ->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'item_count' => $cart->fresh()->itemCount(),
            ]);
        }

        return redirect()->route('cart.show')->with('status', 'Removed from cart.');
    }
}
