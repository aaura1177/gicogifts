<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GigiChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/boxes', [ShopController::class, 'boxes'])->name('shop.boxes');
Route::get('/shop/individual', [ShopController::class, 'individual'])->name('shop.individual');
Route::get('/shop/occasion/{slug}', [ShopController::class, 'occasion'])->name('shop.occasion');
Route::get('/shop/region/{slug}', [ShopController::class, 'region'])->name('shop.region');

Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('/stories', [StoryController::class, 'index'])->name('stories.index');
Route::get('/stories/{slug}', [StoryController::class, 'show'])->name('stories.show');

Route::get('/artisans', [ArtisanController::class, 'index'])->name('artisans.index');
Route::get('/artisans/{slug}', [ArtisanController::class, 'show'])->name('artisans.show');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/corporate-gifting', [PageController::class, 'corporate'])->name('corporate');
Route::post('/corporate-gifting', [PageController::class, 'corporateSubmit'])->name('corporate.submit');

Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout/serviceability', [CheckoutController::class, 'serviceability'])->name('checkout.serviceability');
Route::post('/checkout/place-order', [CheckoutController::class, 'place'])->name('checkout.place');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::post('/webhooks/razorpay', [WebhookController::class, 'razorpay'])->name('webhooks.razorpay');
Route::post('/webhooks/stripe', [WebhookController::class, 'stripe'])->name('webhooks.stripe');
Route::post('/webhooks/shiprocket', [WebhookController::class, 'shiprocket'])->name('webhooks.shiprocket');

Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account.dashboard');
    Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/orders/{order}', [AccountController::class, 'order'])->name('account.order.show');
    Route::get('/account/addresses', [AccountController::class, 'addresses'])->name('account.addresses');
    Route::get('/account/wishlist', [AccountController::class, 'wishlist'])->name('account.wishlist');
});

Route::get('/track/{awb}', [TrackingController::class, 'show'])->name('track')->where('awb', '[A-Za-z0-9\-]+');

Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::post('/gigi/chat', [GigiChatController::class, 'chat'])->name('gigi.chat');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'store'])->name('newsletter.subscribe');

Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy-policy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/shipping-policy', [PageController::class, 'shippingPolicy'])->name('shipping-policy');
Route::get('/refund-policy', [PageController::class, 'refundPolicy'])->name('refund-policy');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
