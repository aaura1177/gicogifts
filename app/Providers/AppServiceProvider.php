<?php

namespace App\Providers;

use App\Events\OrderPaid;
use App\Listeners\CheckStockAlerts;
use App\Listeners\CreateShiprocketOrder;
use App\Listeners\DeductBomInventory;
use App\Listeners\NotifySlackOfOrder;
use App\Listeners\SendOrderConfirmationEmail;
use App\Models\Artisan;
use App\Models\Cart;
use App\Models\Occasion;
use App\Models\Product;
use App\Models\Region;
use App\Models\Story;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $cartComposer = function ($view): void {
            $view->with('storeCart', Cart::current()->load(['items.product.media']));
        };

        View::composer('layouts.app', $cartComposer);
        View::composer('layouts.account', $cartComposer);

        $sitemapCacheKey = 'seo.sitemap_xml_v2';
        foreach ([Product::class, Story::class, Artisan::class, Occasion::class, Region::class] as $modelClass) {
            $modelClass::saved(fn () => Cache::forget($sitemapCacheKey));
            $modelClass::deleted(fn () => Cache::forget($sitemapCacheKey));
        }

        Event::listen(OrderPaid::class, DeductBomInventory::class);
        Event::listen(OrderPaid::class, CheckStockAlerts::class);
        Event::listen(OrderPaid::class, SendOrderConfirmationEmail::class);
        Event::listen(OrderPaid::class, NotifySlackOfOrder::class);
        Event::listen(OrderPaid::class, CreateShiprocketOrder::class);
    }
}
