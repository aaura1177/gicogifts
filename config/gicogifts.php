<?php

return [
    'legal_line' => env('BRAND_LEGAL_LINE', ''),
    'gstin' => env('BRAND_GSTIN', ''),

    'default_meta_description' => env(
        'BRAND_DEFAULT_META_DESCRIPTION',
        'Curated artisan gift boxes from Rajasthan — story-first gifting, handmade by vetted makers, packed in Udaipur.'
    ),

    /** Absolute URL; leave empty to use the built-in /images/og-default.svg. */
    'default_og_image' => env('BRAND_DEFAULT_OG_IMAGE', ''),

    'organization' => [
        'name' => env('BRAND_ORG_NAME', 'GicoGifts'),
        'contact_email' => env('BRAND_CONTACT_EMAIL', ''),
    ],

    'social' => [
        'instagram' => env('BRAND_INSTAGRAM_URL', ''),
        'facebook' => env('BRAND_FACEBOOK_URL', ''),
        'linkedin' => env('BRAND_LINKEDIN_URL', ''),
        'youtube' => env('BRAND_YOUTUBE_URL', ''),
    ],
];
