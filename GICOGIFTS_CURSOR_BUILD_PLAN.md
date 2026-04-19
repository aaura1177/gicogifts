# GicoGifts — Cursor Build Plan

**Owner:** Ethan Stark (Aurateria)
**Brand:** GicoGifts — Premium Hyper-Local Artisan Gift Boxes from Rajasthan
**Tech Stack:** Laravel 12 monolith (same as aurateria.com)
**Target first sale:** Day 30
**Target profit:** ₹2L/month by 8 Oct 2026, ₹3L/month by year end
**Doc version:** 1.0 — Apr 2026

---

## 0. How to Use This Document with Cursor

Read this doc top-to-bottom once. Then work phase by phase. Every phase has:
1. Goal
2. Deliverables (exact files)
3. Commands to run
4. Acceptance test

Do NOT skip phases. Do NOT start Phase 3 before Phase 2 passes its acceptance test. Cursor: when you start a phase, re-read that phase's full section before touching any file.

**Golden rule:** Mirror aurateria.com's code patterns wherever possible. Same folder layout, same middleware pattern, same Blade structure, same Vite config. That way Ethan can mentally map `aurateria` ↔ `gicogifts` one-to-one.

---

## 1. Product & Brand Context (Read First)

### 1.1 What GicoGifts sells
Curated artisan gift boxes from Rajasthan. Launch catalog (5 boxes):

| # | Product | Retail ₹ | COGS target | Theme |
|---|---------|----------|-------------|-------|
| 1 | Mewar Heritage Box | 2,200 | 750–850 | Pichwai art, brass diya, masala chai, block-print napkins |
| 2 | Jaipur Colour Box | 1,500 | 500–600 | Block-printed scarf, blue pottery coasters, dry fruits, saffron |
| 3 | Tribal Discovery Box | 1,800 | 550–700 | Tribal jewelry, handwoven textile, herbal tea, terracotta |
| 4 | Royal Udaipur Experience | 3,500 | 1,100–1,300 | Miniature painting, marble inlay, premium tea tin, notebooks, brass bookmark |
| 5 | Mini Rajasthan Sampler | 799 | 250–300 | Entry funnel — cotton pouch IS the packaging |

Plus **8 individual test-phase SKUs** already bought (marble inlay boxes, coasters, elephants, soapstone pieces — ₹350–1,800 retail).

### 1.2 Target customer
- Primary: Urban Indian, household income ₹15L+, 28–55 years, gifts for weddings/Diwali/corporate/personal
- Secondary: NRIs (US/UK/UAE) sending gifts to India, or importing themselves

### 1.3 Voice & feel
"Refined Luxury meets Warm Artisan." Editorial, not busy. Story-first, price-second. Every box has a named artisan/region story. **No hustle-culture cringe, no deep-discount banners, no pop-ups.**

---

## 2. Tech Stack (Final, Locked)

| Layer | Choice | Reason |
|-------|--------|--------|
| PHP | 8.2+ | Laravel 12 requirement |
| Framework | **Laravel 12** | Same as aurateria.com |
| Frontend | Blade + Tailwind v4 + Alpine.js + Vite | Same as aurateria.com |
| Admin | **Filament v3** | Free admin UI; saves 2 weeks of CRUD work |
| Auth (customers) | Laravel Breeze (Blade) | Standard |
| Auth (admin) | Filament's built-in + `is_admin` flag | Same pattern as aurateria |
| DB | MySQL 8 (prod), SQLite (local) | Standard |
| Payments | **Razorpay** (India, primary) + **Stripe** (NRI/international fallback) | Domestic + international covered |
| Shipping | **Shiprocket REST API** | Per business plan |
| Media | spatie/laravel-medialibrary | Product images, variants, thumbs |
| Email | Resend or Brevo (pick whichever has free tier available at signup) | Transactional emails |
| Queue | Database driver initially (Redis later) | Simple |
| Search | Laravel Scout + `database` driver | Free start; swap to Meilisearch at scale |
| AI chat | Gemini 2.5 Flash | Same as Jules on aurateria.com |
| Fonts | **Fraunces** (display) + **Inter** (body) | Google Fonts |
| Hosting | Hostinger VPS or DigitalOcean $6 droplet | Cheap |
| Domain | gicogifts.com (or .in) | Register if not done |
| Repo | Private on GitHub under `aurateria` org | `aurateria/gicogifts` |

### 2.1 Colors (Tailwind config)
```js
colors: {
  ivory:    { 50: '#FDF8F3', 100: '#F7EEE3', 200: '#ECDBC4' },
  sienna:   { 400: '#C27454', 500: '#A0522D', 600: '#8A4526', 700: '#6D3620' },
  gold:     { 400: '#D9BC82', 500: '#C9A96E', 600: '#A88A52' },
  chocolate:{ 700: '#4A2A1C', 800: '#3A1F14', 900: '#2C1810' },
}
```

### 2.2 What we deliberately DO NOT use
- ❌ Next.js — monolith is faster to ship
- ❌ Livewire — Blade + Alpine is enough for a catalog site
- ❌ Redis on day one — adds hosting complexity
- ❌ Separate API subdomain — single domain, same app
- ❌ Countdown timers, exit-intent popups, wheel-of-fortune widgets — cheapens the brand

---

## 3. Repository Setup (Phase 0)

### 3.1 Goal
Working Laravel 12 skeleton on Ethan's laptop, pushed to private GitHub repo.

### 3.2 Commands
```bash
# 1. Create project
composer create-project laravel/laravel gicogifts
cd gicogifts

# 2. Install Breeze (Blade stack)
composer require laravel/breeze --dev
php artisan breeze:install blade

# 3. Install Filament v3
composer require filament/filament:"^3.2" -W
php artisan filament:install --panels

# 4. Install core packages
composer require spatie/laravel-medialibrary
composer require razorpay/razorpay
composer require stripe/stripe-php
composer require laravel/scout
composer require spatie/laravel-sluggable
composer require barryvdh/laravel-dompdf   # invoices
composer require guzzlehttp/guzzle          # Shiprocket calls

# 5. Frontend
npm install
npm install -D @tailwindcss/vite tailwindcss
npm install alpinejs

# 6. Init git
git init
git add .
git commit -m "chore: initial laravel 12 skeleton"
```

### 3.3 `.env` additions (prepare now, fill later)
```
APP_NAME=GicoGifts
APP_URL=http://localhost:8000

# Razorpay
RAZORPAY_KEY=
RAZORPAY_SECRET=
RAZORPAY_WEBHOOK_SECRET=

# Stripe (NRI)
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

# Shiprocket
SHIPROCKET_EMAIL=
SHIPROCKET_PASSWORD=
SHIPROCKET_PICKUP_LOCATION="Udaipur Home"

# Gemini (product-page AI assistant "Gigi")
GEMINI_API_KEY=
GEMINI_MODEL=gemini-2.5-flash

# Slack (order alerts, replaces Telegram bot in plan)
SLACK_WEBHOOK_URL=

# Brand
BRAND_LEGAL_LINE="A brand of Aurateria | GSTIN: <fill>"
BRAND_GSTIN=
```

### 3.4 Acceptance
- `php artisan serve` renders default Laravel welcome page
- `/admin/login` loads Filament login
- `npm run dev` builds without error
- Repo pushed to `github.com/aurateria/gicogifts` (private)

---

## 4. Database Schema (Phase 1)

Everything hinges on this. Get it right before building anything else.

### 4.1 Tables overview
```
users                  — customers + admin (is_admin flag)
settings               — key/value site config (same pattern as aurateria)

# Catalog
categories             — Gift Boxes, Individual Items, Occasions
products               — both boxes and individuals; is_box flag
product_variants       — optional (e.g. small vs large marble box)
product_media          — handled by spatie/media-library
components             — raw items used to assemble boxes (BOM ingredients)
product_components     — BOM pivot: product_id, component_id, qty
occasions              — Diwali, Wedding, Birthday, etc.
occasion_product       — pivot
regions                — Mewar, Jaipur, Tribal Belt, etc.
artisans               — name, region_id, bio, photo
product_artisan        — pivot (credit artisans per product)

# Orders
carts                  — guest (session) + user carts
cart_items
addresses              — reusable; belongs_to user
orders                 — totals, status, razorpay_order_id, shipment_id
order_items
order_status_history   — audit trail
payments               — razorpay/stripe rows
coupons                — optional; code, type, amount, expires_at

# Shipping
shipments              — shiprocket awb, courier, tracking_url, status

# Content
posts                  — blog (same as aurateria)
reviews                — product reviews (moderated)
faqs                   — accordion items, category-scoped
newsletter_subscribers — email + source
contact_submissions    — same as aurateria
stories                — artisan/region long-form pieces (drives SEO)

# Ops
inventory_movements    — audit: +purchase, -order, -breakage
stock_alerts_log       — when a component hit low threshold
jules_chat_logs        — optional; log AI chats for tuning
```

### 4.2 Critical field definitions

**`products`**
```
id, slug (unique), sku (unique), name, subtitle, story_md (long markdown),
short_description, price_inr, compare_at_price_inr (for strike-through),
is_box (bool), is_active (bool), is_featured (bool),
region_id (nullable FK), hsn_code, gst_rate (decimal, e.g. 5.00),
weight_grams, length_cm, width_cm, height_cm,   // for Shiprocket
meta_title, meta_description,
sort_order, published_at, timestamps
```

**`components`** (for BOM)
```
id, name, sku, unit_cost_inr, stock_on_hand, reorder_threshold,
supplier_name, supplier_contact, hsn_code, timestamps
```

**`product_components`** (BOM pivot)
```
product_id, component_id, quantity, notes
```

**`orders`**
```
id, order_number (GG-2526-00001 format),
user_id (nullable — guest checkout),
email, phone, status (pending|paid|packed|shipped|delivered|cancelled|refunded),
subtotal_inr, shipping_inr, discount_inr, gst_inr, total_inr,
shipping_address_id, billing_address_id (nullable — same as shipping default),
razorpay_order_id, razorpay_payment_id,
stripe_payment_intent_id,
payment_gateway (razorpay|stripe|cod),
coupon_code,
notes (gift message, internal notes),
is_gift (bool), gift_message (text),
created_at, paid_at, packed_at, shipped_at, delivered_at
```

**`shipments`**
```
id, order_id, shiprocket_order_id, shiprocket_shipment_id, awb_code,
courier_name, status, tracking_url, expected_delivery, actual_delivery,
label_pdf_url, manifest_pdf_url, timestamps
```

### 4.3 Migrations to generate (in order)
```bash
php artisan make:migration create_settings_table
php artisan make:migration create_categories_table
php artisan make:migration create_regions_table
php artisan make:migration create_artisans_table
php artisan make:migration create_products_table
php artisan make:migration create_product_artisan_table
php artisan make:migration create_occasions_table
php artisan make:migration create_occasion_product_table
php artisan make:migration create_components_table
php artisan make:migration create_product_components_table
php artisan make:migration create_addresses_table
php artisan make:migration create_carts_table
php artisan make:migration create_cart_items_table
php artisan make:migration create_coupons_table
php artisan make:migration create_orders_table
php artisan make:migration create_order_items_table
php artisan make:migration create_order_status_histories_table
php artisan make:migration create_payments_table
php artisan make:migration create_shipments_table
php artisan make:migration create_inventory_movements_table
php artisan make:migration create_stock_alerts_logs_table
php artisan make:migration create_reviews_table
php artisan make:migration create_faqs_table
php artisan make:migration create_newsletter_subscribers_table
php artisan make:migration create_contact_submissions_table
php artisan make:migration create_posts_table
php artisan make:migration create_stories_table
php artisan make:migration add_is_admin_to_users_table
php artisan make:migration create_media_table   # from spatie
```

### 4.4 Models to generate
```bash
php artisan make:model Setting
php artisan make:model Category
php artisan make:model Region
php artisan make:model Artisan
php artisan make:model Product
php artisan make:model Occasion
php artisan make:model Component
php artisan make:model ProductComponent -p
php artisan make:model Address
php artisan make:model Cart
php artisan make:model CartItem
php artisan make:model Coupon
php artisan make:model Order
php artisan make:model OrderItem
php artisan make:model OrderStatusHistory
php artisan make:model Payment
php artisan make:model Shipment
php artisan make:model InventoryMovement
php artisan make:model Review
php artisan make:model Faq
php artisan make:model NewsletterSubscriber
php artisan make:model ContactSubmission
php artisan make:model Post
php artisan make:model Story
```

### 4.5 Seeders
```bash
php artisan make:seeder AdminUserSeeder       # Ethan's admin login
php artisan make:seeder SettingSeeder
php artisan make:seeder RegionSeeder          # Mewar, Jaipur, Tribal Belt
php artisan make:seeder OccasionSeeder        # Diwali, Wedding, Birthday, Housewarming, Thank You, Corporate
php artisan make:seeder ArtisanSeeder
php artisan make:seeder ProductSeeder         # 5 boxes + 8 test-phase items
php artisan make:seeder ComponentSeeder       # all BOM components
php artisan make:seeder FaqSeeder
php artisan make:seeder StorySeeder           # 3 starter stories
```

### 4.6 Acceptance
- `php artisan migrate:fresh --seed` runs cleanly
- DB has 5 boxes + 8 individual products seeded with images (use placeholder URLs; swap real photos in Phase 7)
- Each of the 5 boxes has a working BOM (components + quantities)
- Admin user can log in at `/admin`

---

## 5. Public Routes & Pages (Phase 2)

### 5.1 Route map

| Path | Name | Controller | Purpose |
|------|------|-----------|---------|
| `/` | `home` | `HomeController@index` | Hero, featured boxes, occasions, stories teaser, testimonial, newsletter |
| `/shop` | `shop.index` | `ShopController@index` | All products grid with filters |
| `/shop/boxes` | `shop.boxes` | `ShopController@boxes` | Only the 5 gift boxes |
| `/shop/individual` | `shop.individual` | `ShopController@individual` | Individual artisan items |
| `/shop/occasion/{slug}` | `shop.occasion` | `ShopController@occasion` | Filtered by occasion |
| `/product/{slug}` | `product.show` | `ProductController@show` | Product detail page |
| `/stories` | `stories.index` | `StoryController@index` | Blog-like region/craft stories |
| `/stories/{slug}` | `stories.show` | `StoryController@show` | Story detail |
| `/artisans` | `artisans.index` | `ArtisanController@index` | Our artisans grid |
| `/artisans/{slug}` | `artisans.show` | `ArtisanController@show` | Artisan profile + their products |
| `/about` | `about` | static | Brand story, founder note, sustainability promise |
| `/corporate-gifting` | `corporate` | static + form | Bulk enquiry form |
| `/cart` | `cart.show` | `CartController@show` | Cart page |
| `/cart/add` | `cart.add` (POST) | `CartController@add` | |
| `/cart/update` | `cart.update` (PATCH) | `CartController@update` | |
| `/cart/remove` | `cart.remove` (DELETE) | `CartController@remove` | |
| `/checkout` | `checkout.show` | `CheckoutController@show` | One-page checkout |
| `/checkout/place-order` | `checkout.place` (POST) | `CheckoutController@place` | Creates order, returns Razorpay order id |
| `/checkout/success/{order}` | `checkout.success` | `CheckoutController@success` | Thank-you + invoice |
| `/webhooks/razorpay` | `webhooks.razorpay` | `WebhookController@razorpay` | Payment confirmation |
| `/webhooks/stripe` | `webhooks.stripe` | `WebhookController@stripe` | |
| `/webhooks/shiprocket` | `webhooks.shiprocket` | `WebhookController@shiprocket` | Tracking updates |
| `/account` | `account.dashboard` | `AccountController@index` | Login required |
| `/account/orders` | `account.orders` | `AccountController@orders` | |
| `/account/orders/{order}` | `account.order.show` | `AccountController@order` | |
| `/account/addresses` | `account.addresses` | `AccountController@addresses` | |
| `/account/wishlist` | `account.wishlist` | `AccountController@wishlist` | |
| `/track/{awb}` | `track` | `TrackingController@show` | Public order tracking by AWB |
| `/faq` | `faq` | static | |
| `/contact` | `contact` | static + form | Same pattern as aurateria |
| `/gigi/chat` | `gigi.chat` (POST) | `GigiChatController@chat` | AI assistant, same pattern as Jules |
| `/newsletter/subscribe` | `newsletter.subscribe` (POST) | `NewsletterController@store` | |
| `/privacy-policy`, `/terms`, `/shipping-policy`, `/refund-policy` | static | Legal pages |
| `/sitemap.xml`, `/robots.txt` | SEO | Same pattern as aurateria |

### 5.2 Homepage sections (in render order)

1. **Announcement bar** (dismissible Alpine) — "Free shipping above ₹2,000 · Handcrafted in Udaipur"
2. **Sticky nav** — Logo / Shop / Stories / Artisans / About / Cart icon / Account icon
3. **Hero** — Large lifestyle photo of a box, serif headline ("Rajasthan, unboxed."), subhead, two CTAs: `Shop Gift Boxes` (primary, sienna) + `Read Our Story` (ghost)
4. **Featured boxes** — 3+2 grid, hover elevates card + shows "Add to Cart" overlay
5. **Shop by occasion** — 6 pill cards: Diwali, Wedding, Birthday, Housewarming, Thank You, Corporate
6. **Our Craft Regions** — 3 cards: Mewar, Jaipur, Tribal Belt — each links to `/shop/region/{slug}` or story
7. **How It Works** — 4 simple steps: Choose → We assemble → Hand-packed in Udaipur → Delivered in 3–7 days
8. **Meet our artisans** — Horizontal scroll of artisan portraits
9. **Social proof** — Stats strip ("150+ artisans", "12 regions", "4.9★ rating") + 3 testimonial cards
10. **Stories teaser** — 3 recent stories with cover images
11. **Newsletter** — Single email field, one sentence of value
12. **Footer** — 4-column: brand blurb + social / Shop / Help / Legal + payment icons + trust badges

### 5.3 Product detail page structure
- Image gallery left (thumbnails below, zoom on click), info right
- Info panel: breadcrumb → name → short_description → price → qty + **Add to Cart** button → **Buy Now** (skips cart) → trust strip (Free ship > ₹2K · Handmade · 7-day return on breakage)
- Tabs below fold: **The Story** (markdown story_md) · **What's Inside** (component list) · **The Artisan** (artisan card) · **Shipping & Returns** · **Reviews**
- Related products carousel at bottom (same occasion/region)

### 5.4 Cart & Checkout rules (keep it SIMPLE)
- Cart drawer slides in from right on "Add to Cart" — never full page redirect unless user clicks cart icon
- Checkout is ONE page with 3 collapsible sections: Contact → Shipping Address → Payment
- Guest checkout is default. "Create account?" is a single optional checkbox at the end.
- Auto-fill pincode → city/state via India Post API (or Shiprocket serviceability call)
- Live shipping cost shown after pincode entered (flat ₹99, free above ₹2,000)
- Gift toggle: "This is a gift" → shows message textarea + "hide prices on invoice" checkbox
- Payment methods shown as large cards: Razorpay (all UPI/cards/wallets/netbanking) + Stripe (international card) + COD (hidden by default; enable later)
- After placing order → Razorpay modal opens → on success, redirect to `/checkout/success/{order}`

### 5.5 Controllers to create
```bash
php artisan make:controller HomeController
php artisan make:controller ShopController
php artisan make:controller ProductController
php artisan make:controller StoryController
php artisan make:controller ArtisanController
php artisan make:controller CartController
php artisan make:controller CheckoutController
php artisan make:controller AccountController
php artisan make:controller TrackingController
php artisan make:controller ContactController
php artisan make:controller NewsletterController
php artisan make:controller GigiChatController
php artisan make:controller WebhookController
php artisan make:controller SeoController        # robots + sitemap (copy from aurateria)
```

### 5.6 Acceptance
- All routes load without error (even if content is placeholder)
- Homepage renders with seeded products showing real images
- Product detail page works for all 13 seeded products
- Cart add/update/remove works via Alpine + POST
- Checkout form loads (payment integration comes in Phase 4)

---

## 6. Frontend Components & Styling (Phase 3)

### 6.1 Blade layouts (mirror aurateria structure)
```
resources/views/
├── layouts/
│   ├── app.blade.php              # main site layout (nav + footer + Gigi partial)
│   ├── checkout.blade.php         # minimal layout: logo only, no nav, no footer links
│   └── account.blade.php          # sidebar + content
├── frontend/
│   ├── home.blade.php
│   ├── shop/
│   │   ├── index.blade.php
│   │   ├── boxes.blade.php
│   │   └── occasion.blade.php
│   ├── product/show.blade.php
│   ├── stories/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── artisans/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── cart/show.blade.php
│   ├── checkout/
│   │   ├── show.blade.php
│   │   └── success.blade.php
│   ├── account/
│   │   ├── dashboard.blade.php
│   │   ├── orders.blade.php
│   │   ├── order-detail.blade.php
│   │   ├── addresses.blade.php
│   │   └── wishlist.blade.php
│   ├── static/
│   │   ├── about.blade.php
│   │   ├── corporate.blade.php
│   │   ├── faq.blade.php
│   │   ├── contact.blade.php
│   │   ├── privacy.blade.php
│   │   ├── terms.blade.php
│   │   ├── shipping-policy.blade.php
│   │   └── refund-policy.blade.php
│   └── partials/
│       ├── nav.blade.php
│       ├── footer.blade.php
│       ├── gigi.blade.php          # AI chat FAB, copy from Jules
│       ├── announcement-bar.blade.php
│       ├── cart-drawer.blade.php
│       ├── product-card.blade.php
│       ├── occasion-card.blade.php
│       ├── artisan-card.blade.php
│       ├── story-card.blade.php
│       └── newsletter-cta.blade.php
└── components/                   # Blade components for reuse
    ├── button.blade.php
    ├── input.blade.php
    ├── price.blade.php           # formats ₹2,200 with strike-through support
    └── rating-stars.blade.php
```

### 6.2 Tailwind config (`tailwind.config.js`)
```js
/** @type {import('tailwindcss').Config} */
export default {
  content: ['./resources/**/*.blade.php', './resources/**/*.js'],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        display: ['Fraunces', 'Georgia', 'serif'],
      },
      colors: {
        ivory:     { 50: '#FDF8F3', 100: '#F7EEE3', 200: '#ECDBC4' },
        sienna:    { 400: '#C27454', 500: '#A0522D', 600: '#8A4526', 700: '#6D3620' },
        gold:      { 400: '#D9BC82', 500: '#C9A96E', 600: '#A88A52' },
        chocolate: { 700: '#4A2A1C', 800: '#3A1F14', 900: '#2C1810' },
      },
      boxShadow: {
        warm: '0 10px 30px -10px rgba(160, 82, 45, 0.15)',
      },
    },
  },
};
```

### 6.3 Design rules Cursor must follow
1. Default body: `bg-ivory-50 text-chocolate-900 font-sans antialiased`
2. Headlines: `font-display` (Fraunces), weight 400–500 only, never 700+
3. Buttons: sienna fill primary, chocolate outline secondary, min height 44px (thumb-friendly)
4. Cards: `bg-white rounded-2xl shadow-warm` — never hard black shadows
5. Spacing: use Tailwind's 4/6/8/12/16/24 scale. Sections are `py-16 md:py-24`.
6. Max content width: `max-w-6xl` for text-heavy, `max-w-7xl` for grids
7. Images: always lazy-loaded, always have explicit width/height to avoid CLS
8. Nav height: 72px desktop, 60px mobile, with top announcement bar of 36px
9. **No carousel on mobile hero** — one static image, one headline, two buttons
10. **No modals on first load** — newsletter prompt appears inline or footer only

### 6.4 Alpine components to write
- `cartDrawer()` — handles add-to-cart animation, qty updates, remove
- `productGallery()` — image zoom, thumbnail switch
- `checkoutForm()` — pincode lookup, shipping calc, gift toggle
- `gigiWidget()` — AI chat (copy from `aurateria/resources/views/frontend/partials/jules.blade.php`)
- `accordion()` — FAQ items
- `announcementBar()` — dismiss + localStorage remember

### 6.5 Acceptance
- Homepage matches the 12-section structure above
- Mobile (375px) tested — no horizontal scroll, tap targets ≥ 44px
- Lighthouse performance ≥ 90 on homepage
- Product detail page has working gallery, tabs, related products
- Cart drawer opens/closes correctly on add-to-cart

---

## 7. Payments: Razorpay + Stripe (Phase 4)

### 7.1 Razorpay (primary — Indian customers)

**Flow:**
1. User clicks "Place Order" on checkout
2. `CheckoutController@place` validates, creates `Order` with status=`pending`, returns `razorpay_order_id`
3. Frontend opens Razorpay Checkout modal with that order id
4. On success → Razorpay hits `/webhooks/razorpay` with signed payload
5. `WebhookController@razorpay` verifies signature, marks order=`paid`, triggers `OrderPaid` event

**Service class:** `app/Services/Payments/RazorpayService.php`
```php
class RazorpayService {
    public function createOrder(Order $order): array  // returns ['id' => 'order_xxx', 'amount' => ...]
    public function verifySignature(string $orderId, string $paymentId, string $signature): bool
    public function verifyWebhook(string $body, string $signature): bool
}
```

**Critical:** NEVER mark an order paid from the browser response alone. ONLY from the webhook with verified signature. Browser success is a UX hint only.

### 7.2 Stripe (for NRI / international)

Show Stripe option only if shipping address country ≠ India. Use Payment Intents API. Same webhook-first rule.

### 7.3 Events
```php
// app/Events/OrderPaid.php
// app/Events/OrderShipped.php
// app/Events/OrderDelivered.php
// app/Events/LowStockAlert.php
```

### 7.4 Listeners (on `OrderPaid`)
1. `DeductBomInventory` — iterate order items, for each box decrement each component by (BOM qty × order qty); write `inventory_movements` row
2. `CheckStockAlerts` — if any component now ≤ reorder_threshold, write `stock_alerts_logs` and fire Slack
3. `SendOrderConfirmationEmail` — customer email with invoice PDF
4. `NotifySlackOfOrder` — Slack webhook ping for Ethan
5. `CreateShiprocketOrder` — push to Shiprocket (Phase 5)

### 7.5 Invoice PDF
- Use `barryvdh/laravel-dompdf`
- Template: `resources/views/pdfs/invoice.blade.php`
- Header: "GicoGifts" logo + tagline
- Legal line: `config('brand.legal_line')` from `.env` (BRAND_LEGAL_LINE)
- Invoice series: `GG/2526/00001`, auto-increment via `orders.order_number`
- Show HSN code and GST rate per line item
- Attach to order confirmation email

### 7.6 Acceptance
- Test order placed on Razorpay test mode successfully flips order to `paid` via webhook
- Signature verification fails gracefully (returns 400) if tampered
- Invoice PDF downloads correctly and includes HSN codes
- BOM deduction verified: seed a box with 3 components @ qty 1 each, place order for 2 boxes → each component stock drops by 2

---

## 8. Shipping: Shiprocket (Phase 5)

### 8.1 Service class: `app/Services/Shipping/ShiprocketService.php`

Endpoints used:
- `POST /auth/login` — get token, cache 10 days
- `POST /orders/create/adhoc` — create shipment after order paid
- `POST /courier/serviceability` — check which courier delivers to pincode + rate
- `POST /courier/assign/awb` — assign AWB
- `POST /courier/generate/pickup` — schedule pickup
- `GET /orders/show/{id}` — tracking status
- `POST /orders/cancel` — cancel shipment
- `GET /manifests/generate` / `GET /courier/generate/label` — PDFs

### 8.2 Flow

```
Order paid
   └─> CreateShiprocketOrder listener
        ├─> POST /orders/create/adhoc (pass Order + items + pickup location)
        ├─> Save shiprocket_order_id, shiprocket_shipment_id on shipments row
        ├─> POST /courier/serviceability → pick cheapest reliable courier
        └─> POST /courier/assign/awb → save awb_code

Next: Admin prints label + manifest from Filament, packs the box, confirms pickup
   └─> POST /courier/generate/pickup → pickup scheduled

Ongoing:
   └─> Shiprocket webhook → /webhooks/shiprocket → update Shipment status
        └─> fires OrderShipped / OrderDelivered events as appropriate
```

### 8.3 Pincode serviceability on checkout
Before placing an order, call `/courier/serviceability` with pickup pincode (Udaipur) + delivery pincode + weight. Show:
- ✅ "Delivery expected in 3–5 days" if serviceable
- ❌ "Sorry, we can't ship to this pincode yet" if not
- Flat ₹99 displayed always (free above ₹2,000) — we absorb any extra

### 8.4 Public tracking page `/track/{awb}`
- Fetch latest status from cached `shipments` row (update via webhook, fallback to API hit cached 30min)
- Show timeline: Placed → Packed → Shipped → Out for Delivery → Delivered
- Share button (WhatsApp) pre-fills "Track my GicoGifts order: [url]"

### 8.5 Acceptance
- Test mode Shiprocket order created on webhook `OrderPaid`
- AWB assigned and stored on `shipments` table
- Label PDF downloadable from Filament admin
- Webhook updates shipment status
- Customer can view tracking on `/track/{awb}` without login

---

## 9. Admin Panel via Filament (Phase 6)

### 9.1 Resources to generate
```bash
php artisan make:filament-resource Product --generate
php artisan make:filament-resource Component --generate
php artisan make:filament-resource Order --generate
php artisan make:filament-resource Shipment --generate
php artisan make:filament-resource Category --generate
php artisan make:filament-resource Occasion --generate
php artisan make:filament-resource Region --generate
php artisan make:filament-resource Artisan --generate
php artisan make:filament-resource Coupon --generate
php artisan make:filament-resource Review --generate
php artisan make:filament-resource Faq --generate
php artisan make:filament-resource Post --generate
php artisan make:filament-resource Story --generate
php artisan make:filament-resource ContactSubmission --generate
php artisan make:filament-resource NewsletterSubscriber --generate
php artisan make:filament-resource User --generate
php artisan make:filament-resource Setting --generate
```

### 9.2 Dashboard widgets (Filament)
1. **TodayStats** — Orders today, revenue today, pending-to-pack count
2. **LowStockAlert** — Components at/below reorder_threshold (clickable → component page)
3. **RevenueChart** — Last 30 days line chart
4. **PendingShipments** — Table of paid orders with no AWB yet
5. **RecentOrders** — Last 10 orders

### 9.3 Custom actions on Order resource
- "Mark as Packed" → sets `packed_at`, writes `order_status_history`
- "Generate Shipping Label" → calls Shiprocket, downloads PDF
- "Resend Invoice" → fires email
- "Cancel & Refund" → Razorpay refund API + updates order
- "Print Pick List" → PDF of items + quantities (for home-warehouse assembly)

### 9.4 Bulk actions on Component resource
- "Bulk restock" → opens modal, enter qty, writes `inventory_movements` rows

### 9.5 Product form special fields
- Tabs: General / Story / BOM (Box only) / Media / SEO / Related
- BOM tab only visible if `is_box = true`
- Repeater for components + qty
- Markdown editor for `story_md` (use `filament-tiptap-editor` or similar)
- Auto-calc "Suggested price" showing margin % based on BOM cost vs `price_inr`

### 9.6 Acceptance
- Ethan can add a new product end-to-end with photos in < 3 minutes
- Order detail page shows customer info, items, payment, shipment, history
- Clicking "Generate Label" downloads a valid Shiprocket PDF
- Low-stock widget correctly flags seeded low components

---

## 10. AI Assistant "Gigi" (Phase 7)

### 10.1 What
Same pattern as Jules on aurateria.com. Copy `JulesChatController` → `GigiChatController`. Replace rulebook. Keep 3-level logic:

**Level 1 (keyword quick-replies):**
- "hi", "hello" → friendly greeting + suggest shopping gift boxes
- "refund", "return" → policy summary + link to `/refund-policy`
- "shipping", "delivery time" → "We ship in 3–7 days across India, 7–14 international. Flat ₹99 or free above ₹2,000."
- "track" → "Share your AWB or order number and I'll pull it up. Or visit yourdomain.com/track/{awb}"
- "corporate", "bulk", "wedding favors" → link to `/corporate-gifting`
- "help me choose", "gift for mom/dad/wife" → trigger recommendation flow

**Level 2 (Gemini fallback with rulebook):**
Rulebook = `docs/GIGI_AI_RULEBOOK.md` (we'll write this as a separate deliverable in Phase 8).

**Level 3 (lead / cart abandonment signal):**
If user says "expensive", "price too high", "discount" → Slack ping to Ethan with conversation snippet.

### 10.2 Rulebook content (to be written as `docs/GIGI_AI_RULEBOOK.md`)
- Brand voice: warm, knowledgeable, mentions regions & artisans by name
- Never offers discounts or coupon codes unless config says so
- Never invents product details — sticks to what's in DB
- Always suggests 1–2 specific products with links when relevant
- For "gift recommendation" — ask 2 short questions: occasion + price range → pull top 2 matching products
- Escalates to human via contact form if user seems frustrated

### 10.3 Privacy & logging
- Log chats to `jules_chat_logs` (or `gigi_chat_logs`) only if `config('gigi.log_chats') = true`
- Never store payment details or addresses from chat text
- Strip emails/phones before storing for analytics

### 10.4 Acceptance
- FAB button visible bottom-right on all public pages
- "hi" → instant L1 reply (no Gemini call)
- "what box should I gift my sister for her wedding?" → Gemini reply suggests Royal Udaipur or Jaipur Colour with links
- Missing GEMINI_API_KEY → graceful fallback, no 500

---

## 11. SEO, Content, Legal (Phase 8)

### 11.1 SEO plumbing (copy from aurateria)
- `SeoController@robots` — robots.txt with sitemap URL + AI crawler allow
- `SeoController@sitemap` — dynamic, includes products, stories, static pages, cached 1h
- JSON-LD in `layouts/app.blade.php`:
  - `Organization` on all pages
  - `Product` + `AggregateRating` + `Offer` on product pages
  - `Article` on story pages
  - `BreadcrumbList` on category/product/story pages
- Canonical URLs
- Open Graph + Twitter Card on every page (auto-fill from `meta_*` fields, fallback to defaults)

### 11.2 Legal pages (required for trust + marketplace parity)
- `/privacy-policy` — PII handling, cookies, Razorpay/Stripe data sharing
- `/terms` — order acceptance, cancellation windows, pricing changes, jurisdiction (Udaipur courts)
- `/shipping-policy` — timelines, coverage map, ₹99 flat / free above ₹2K, international rules, who pays customs (customer)
- `/refund-policy` — 7-day breakage-only returns, food items non-returnable, refund timelines (5–7 business days), how to initiate

### 11.3 Content to seed at launch
- 3 stories (each 800–1200 words):
  1. "The Last Pichwai Painters of Nathdwara"
  2. "Why Sanganer's Water Makes Block Prints Sing"
  3. "Inside a Banswara Tribal Weaver's Home"
- 5 FAQs minimum: shipping time, what if I don't like it, gift message, corporate orders, is it handmade
- 6 occasion pages (Diwali, Wedding, Birthday, Housewarming, Thank You, Corporate) — each with hero image + filtered product grid
- Artisan page with 4–5 profiles (names can be placeholder "Govindji — Pichwai Artist, Nathdwara")

### 11.4 Acceptance
- `/sitemap.xml` returns valid XML with all live URLs
- Product page passes Google's Rich Results Test for Product schema
- All 4 legal pages published
- 3 stories live at `/stories/{slug}` with cover images

---

## 12. Launch Prep: Compliance + Real Data (Phase 9)

This phase mirrors the 90-Day Launch Playbook from the business context doc.

### 12.1 Compliance checklist (Ethan does these outside the code)
- [ ] Trademark application filed for "GicoGifts" (Class 35 + Class 20)
- [ ] Aurateria GST amended to add HSN goods codes (6802, 4802, 5208, 8306, 9701, 0902, 3401 etc.)
- [ ] FSSAI Basic Registration (if selling tea/spices)
- [ ] Udyam/MSME registration
- [ ] LUT filed (for future Etsy/international sales — Form GST RFD-11)
- [ ] Razorpay merchant account approved
- [ ] Shiprocket account verified + pickup address added
- [ ] Domain `gicogifts.com` pointed to VPS
- [ ] SSL certificate (Let's Encrypt)

### 12.2 Product data checklist (in Filament)
- [ ] Real photos for all 5 boxes + 8 individual items (3–6 per product, shot on neutral warm background)
- [ ] Real weights + dimensions for each product (for Shiprocket)
- [ ] Real BOM for each box with actual component costs
- [ ] Real `story_md` for each box
- [ ] 4–5 artisan profiles with photos
- [ ] 3 stories published
- [ ] All legal pages proofread
- [ ] Razorpay in LIVE mode (not test)
- [ ] Shiprocket in LIVE mode

### 12.3 Pre-launch tests
- [ ] End-to-end: browse → add to cart → checkout → Razorpay live payment of ₹1 → webhook received → order shows paid → Shiprocket order created → invoice emailed → tracking page shows status
- [ ] Place 3 orders to different pincodes (Udaipur local, Delhi metro, small town like Bhiwadi) to test Shiprocket serviceability logic
- [ ] Test COD flow (if enabling)
- [ ] Test refund from Filament admin
- [ ] Test low-stock alert by manually dropping a component to 1 unit
- [ ] Mobile pass: iPhone SE 375px + Android Chrome, full checkout
- [ ] Run `php artisan test` — all feature tests pass
- [ ] Lighthouse: 90+ on Perf/Accessibility/Best Practices/SEO on homepage + product page
- [ ] Broken link check (use Screaming Frog or `wget --spider -r`)

### 12.4 Analytics / monitoring
- Plausible Analytics (privacy-friendly, ₹9/mo) or Google Analytics 4
- Sentry free tier for error tracking
- UptimeRobot free tier (5-min checks on `/`, `/cart`, `/checkout/success/1`)
- Server-side: Laravel Telescope in staging only

### 12.5 Acceptance
All 12.1, 12.2, and 12.3 checkboxes ticked. Ethan places a real ₹1 test order himself and it goes through end-to-end without manual intervention.

---

## 13. Phase Timeline (4-Week Build Sprint)

Assumes ~4–6 focused hours/day. Adjust for Ethan's other commitments.

| Week | Days | Phases | Deliverable |
|------|------|--------|-------------|
| 1 | 1–2 | 0, 1 | Repo + DB schema + seeded dummy data |
| 1 | 3–5 | 2 | All public routes + placeholder pages rendering |
| 1 | 6–7 | 3 | Homepage + product detail pixel-perfect |
| 2 | 8–10 | 3 cont. | Shop, stories, artisans, about, cart drawer |
| 2 | 11–14 | 4 | Razorpay integration + test orders working |
| 3 | 15–17 | 5 | Shiprocket integration + tracking |
| 3 | 18–20 | 6 | Filament admin fully usable |
| 3 | 21 | 7 | Gigi AI chat live |
| 4 | 22–24 | 8 | SEO + legal + 3 stories + FAQ |
| 4 | 25–27 | 9 | Real photos, real BOMs, real story content |
| 4 | 28 | Launch | Soft launch to friends & family (10 orders) |

After soft launch: 3–5 days of fixes → public launch → Amazon/Flipkart listings begin.

---

## 14. Post-Launch (Not in MVP but Plan for It)

### 14.1 Month 2
- Amazon Karigar listing (Ethan, outside this codebase)
- Instagram shop tagging
- First 3 reviews imported from friends-and-family phase
- Cart abandonment email (24h after, Laravel scheduled job)
- Referral program (share URL → friend gets ₹100 off, you get ₹100 credit)

### 14.2 Month 3
- Wishlist email nudges
- "Build Your Own Box" feature (select 3 components under a price cap)
- Corporate bulk-order portal with CSV upload
- Etsy listing (NRI push, file LUT first)

### 14.3 Scale triggers
- 150 orders/month → move to rented workspace (₹8–15k/mo)
- 400 orders/month → Shiprocket Fulfillment 3PL
- International > 20/mo → separate fulfillment workflow
- ₹25–50L annual → incorporate GicoGifts Pvt Ltd

### 14.4 Tech upgrades when justified
- Meilisearch when catalog > 100 products
- Redis when queue jobs > 1000/day
- S3 when media > 10GB
- Separate Next.js frontend ONLY if shipping a native app too

---

## 15. Cursor Prompting Tips (for Ethan)

When asking Cursor to implement a phase, prompt like this:

> "Read `GICOGIFTS_CURSOR_BUILD_PLAN.md` section 5 (Public Routes & Pages). Implement phase 2 end-to-end. Follow the exact route map in 5.1, the controller list in 5.5, and the Blade structure in 6.1. When done, run the acceptance checks in 5.6 and report results. Do not proceed to phase 3."

Other good prompts:
- "Re-read section 4.2 and verify the `products` migration matches exactly. Fix any drift."
- "Open aurateria's `app/Http/Controllers/JulesChatController.php` as a reference, then build `GigiChatController.php` following the 3-level pattern from section 10."
- "Run `php artisan test` and show me failing tests. Fix them one by one."

### Rules for Cursor (paste into `.cursorrules` at repo root)
```
You are building GicoGifts, a Laravel 12 e-commerce monolith.

Rules:
1. Follow GICOGIFTS_CURSOR_BUILD_PLAN.md section-by-section. Do not skip ahead.
2. Mirror the aurateria.com codebase patterns (same folder names, same middleware pattern, same Blade structure). Ethan ships both; keep mental model aligned.
3. Use Blade + Tailwind v4 + Alpine.js. Do NOT introduce Livewire, Inertia, Vue, or React unless the user explicitly asks.
4. Customer UX priority: SIMPLE over clever. Fewer clicks. Big tap targets. No modals on first load.
5. Money: NEVER trust browser-side payment success. Always verify via server webhook with signature check.
6. BOM logic: every paid Box order must atomically decrement component stock AND write inventory_movements rows.
7. Invoice numbering: GG/2526/00001 format, auto-increment, no gaps.
8. Run `php artisan test` after every phase.
9. Commit at each phase boundary with message `feat(phase-N): <summary>`.
10. If you are unsure about a business decision, STOP and ask. Do not invent policy (prices, refund windows, shipping rates).
```

---

## 16. File Deliverables Checklist (What Success Looks Like)

A working GicoGifts app has these files implemented and tested:

**Backend (Laravel):**
- [ ] 26 migrations under `database/migrations/`
- [ ] 24 models under `app/Models/`
- [ ] 8 seeders under `database/seeders/`
- [ ] 14 public controllers under `app/Http/Controllers/`
- [ ] 17 Filament resources under `app/Filament/Resources/`
- [ ] 4 service classes: `RazorpayService`, `StripeService`, `ShiprocketService`, `InvoiceService`
- [ ] 4 events + 5 listeners for order lifecycle
- [ ] `.env.example` documented with every config key
- [ ] `config/gicogifts.php` for brand-specific config (legal_line, gstin, pickup_address, etc.)
- [ ] `tests/Feature/` covering: checkout flow, webhook signature verification, BOM deduction, invoice generation

**Frontend (Blade + Tailwind + Alpine):**
- [ ] 3 layouts + ~35 Blade views
- [ ] 10+ partials
- [ ] 5 Blade components
- [ ] 6 Alpine components
- [ ] Tailwind config with brand tokens
- [ ] `resources/css/app.css` imports Fraunces + Inter

**Content:**
- [ ] All 13 seed products with real photos, weights, BOMs, stories
- [ ] 3 published stories
- [ ] 5 FAQs minimum
- [ ] 4 legal pages
- [ ] 6 occasion pages
- [ ] Artisans page with 4+ profiles

**Infra:**
- [ ] Production deploy on Hostinger VPS or DO droplet
- [ ] SSL via Let's Encrypt
- [ ] Cron for `php artisan schedule:run`
- [ ] Queue worker via Supervisor (database queue)
- [ ] Nightly DB backup to local disk + weekly offsite sync (rsync to second VPS or S3)

**Integrations (live mode):**
- [ ] Razorpay verified + webhook registered
- [ ] Stripe verified + webhook registered
- [ ] Shiprocket verified + pickup location confirmed + webhook registered
- [ ] Gemini API key active
- [ ] Slack webhook posting order pings
- [ ] Email sender domain verified (SPF/DKIM/DMARC)

---

## 17. Budget Reality Check (Ties Back to Ethan's Targets)

From `my personal info.docx`: profit target **₹2L/month by 8 Oct 2026**.

### 17.1 Fixed monthly burn (approximate)
- VPS: ₹500
- Domain: ₹80 (annualized)
- Email service: ₹0–400 (Brevo free up to 300/day)
- Gemini API: ₹300–800 (depends on chat volume)
- Plausible: ₹750
- **Ops total: ~₹2,000–3,000/mo**

### 17.2 Variable cost ratios (per ₹1 of revenue, from business doc Pillar 5)
- COGS: ₹0.28
- Packaging: ₹0.06
- Shipping: ₹0.04 (net after ₹99 fee)
- Razorpay: ₹0.024
- Returns/breakage: ₹0.03
- GST (net of input credit): ₹0.04
- **Gross margin: ~₹0.53**

### 17.3 To hit ₹2L net profit/month (own website only, no ads)
- Required revenue: ~₹4L/mo → **~180 orders/mo at ₹2,200 AOV**
- Matches the "Moderate" scenario from the business doc
- Marketing budget: keep under ₹50K/mo (≤12.5% of rev)

### 17.4 Path to that number
- Month 1: 10–30 orders (friends, family, first Instagram push)
- Month 2: 50–80 orders (Amazon live, organic IG growth, 1–2 paid reels)
- Month 3: 100–150 orders (repeat customers + Diwali season if Oct target)
- Month 4+: 180+ orders

**The website must not be the bottleneck.** It needs to convert at ≥2% on direct traffic, handle pincode serviceability without friction, and ship invoices + labels automatically so Ethan's time goes into marketing and artisan relationships, not order processing.

---

## 18. Open Questions for Ethan (Decide Before Phase 9)

1. **Domain:** gicogifts.com or gicogifts.in? (Both available? Check.)
2. **COD at launch:** yes or no? Business doc recommends no (RTO risk). Suggest: no for first 60 days.
3. **International shipping at launch:** yes, or India-only for first 30 days? Suggest: India-only, then add NRI after first 50 orders settle the fulfillment rhythm.
4. **Loyalty/referral:** launch with or phase 2? Suggest: phase 2 (Month 2).
5. **Corporate enquiry:** form only, or live price-on-request page? Suggest: form only for now.
6. **Wishlist:** login required or localStorage? Suggest: localStorage for guests, migrate on login.
7. **Reviews:** auto-publish or moderate? Suggest: moderate for first 90 days.
8. **Email provider final pick:** Resend vs Brevo vs Zoho ZeptoMail? Suggest: Brevo (free tier, Indian-friendly, good deliverability).

---

## Appendix A — Aurateria Pattern Map (What to Copy)

When Cursor needs a reference for "how we do X here," point it at these Aurateria files:

| What Cursor needs | Copy pattern from |
|---|---|
| Admin middleware + `is_admin` flag | `app/Http/Middleware/AdminMiddleware.php` |
| Settings key-value model | `app/Models/Setting.php` + `SettingController` |
| AI chat 3-level logic | `app/Http/Controllers/JulesChatController.php` |
| SEO robots + sitemap | `app/Http/Controllers/SeoController.php` + `SeoPingService` |
| JSON-LD in layout | `resources/views/layouts/frontend.blade.php` |
| Contact form → DB | `app/Http/Controllers/ContactController.php` |
| Blade partial injection pattern | `resources/views/frontend/partials/jules.blade.php` |
| Vite + Tailwind v4 config | `vite.config.js` + `resources/css/app.css` |
| Post/Portfolio CRUD in admin | `app/Http/Controllers/Admin/PostController.php` |

Cursor should treat these as load-bearing conventions, not suggestions.

---

## Appendix B — Product Seeder Data (Exact)

Use these exact values when seeding `products` table:

| slug | sku | name | price_inr | hsn | gst | is_box | is_featured |
|------|-----|------|-----------|-----|-----|--------|-------------|
| mewar-heritage-box | GG-BOX-MWR-01 | The Mewar Heritage Box | 2200 | 9701 | 5 | 1 | 1 |
| jaipur-colour-box | GG-BOX-JPR-01 | The Jaipur Colour Box | 1500 | 5208 | 5 | 1 | 1 |
| tribal-discovery-box | GG-BOX-TRB-01 | The Tribal Discovery Box | 1800 | 6802 | 5 | 1 | 0 |
| royal-udaipur-experience | GG-BOX-UDP-01 | The Royal Udaipur Experience | 3500 | 9701 | 5 | 1 | 1 |
| mini-rajasthan-sampler | GG-BOX-MIN-01 | Mini Rajasthan Sampler | 799 | 0902 | 5 | 1 | 0 |
| small-marble-inlay-box | GG-ITM-MRB-01 | Small Marble Inlay Box | 399 | 6802 | 5 | 0 | 0 |
| large-marble-inlay-box | GG-ITM-MRB-02 | Large Marble Inlay Box | 599 | 6802 | 5 | 0 | 0 |
| marble-coaster-set-6 | GG-ITM-MRB-03 | Marble Inlay Coaster Set (6) | 1599 | 6802 | 5 | 0 | 1 |
| printed-marble-elephant | GG-ITM-MRB-04 | Hand-Painted Marble Elephant | 799 | 6802 | 5 | 0 | 0 |
| white-marble-elephant | GG-ITM-MRB-05 | White Marble Elephant | 749 | 6802 | 5 | 0 | 0 |
| small-lady-elephant | GG-ITM-MRB-06 | Petite Lady Elephant | 549 | 6802 | 5 | 0 | 0 |
| soapstone-candle-holder | GG-ITM-SST-01 | Soapstone Jaali Candle Holder | 649 | 6802 | 5 | 0 | 1 |
| soapstone-face-sculpture | GG-ITM-SST-02 | Soapstone Face Sculpture | 499 | 6802 | 5 | 0 | 0 |

---

*End of plan. Update this document as decisions evolve. Re-version at each major phase completion.*
