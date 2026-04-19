<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        return view('frontend.account.dashboard');
    }

    public function orders(): View
    {
        $orders = Order::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('frontend.account.orders', compact('orders'));
    }

    public function order(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load('items');

        return view('frontend.account.order-detail', compact('order'));
    }

    public function addresses(): View
    {
        return view('frontend.account.addresses');
    }

    public function wishlist(): View
    {
        return view('frontend.account.wishlist');
    }
}
