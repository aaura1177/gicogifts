<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Gigi (storefront assistant)
    |--------------------------------------------------------------------------
    */

    'log_chats' => (bool) env('GIGI_LOG_CHATS', false),

    /**
     * When false, the model must never suggest coupons, percent-off deals, or haggling.
     */
    'allow_discount_mentions' => (bool) env('GIGI_ALLOW_DISCOUNT_MENTIONS', false),

];
