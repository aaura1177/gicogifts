<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('frontend.static.about');
    }

    public function corporate(): View
    {
        return view('frontend.static.corporate');
    }

    public function corporateSubmit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:32'],
            'company' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactSubmission::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'metadata' => [
                'type' => 'corporate_gifting',
                'company' => $data['company'] ?? null,
            ],
        ]);

        return redirect()->route('corporate')->with('status', 'Thank you — our team will reply shortly.');
    }

    public function faq(): View
    {
        $faqs = Faq::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('frontend.static.faq', compact('faqs'));
    }

    public function privacy(): View
    {
        return view('frontend.static.privacy');
    }

    public function terms(): View
    {
        return view('frontend.static.terms');
    }

    public function shippingPolicy(): View
    {
        return view('frontend.static.shipping-policy');
    }

    public function refundPolicy(): View
    {
        return view('frontend.static.refund-policy');
    }
}
