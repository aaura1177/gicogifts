# GicoGifts — Implementation vs `GICOGIFTS_CURSOR_BUILD_PLAN.md`

This document maps **what the codebase implements today** against the **original build plan** (`GICOGIFTS_CURSOR_BUILD_PLAN.md`, doc v1.0 — Apr 2026). Use it for onboarding, handoffs, and launch checklists.

**Plan file:** `GICOGIFTS_CURSOR_BUILD_PLAN.md` (repository root)

---

## 1. Stack & doc drift (plan vs repo)

| Topic | Plan says | Repo reality |
|--------|-----------|--------------|
| Admin UI | Filament v3 | **Filament v5** (Laravel 12–aligned packages) |
| Gigi rulebook timing | Plan §10.2 once said “Phase 8” for `docs/GIGI_AI_RULEBOOK.md` | Rulebook shipped with **Phase 7** (`docs/GIGI_AI_RULEBOOK.md`) |
| “LowStockAlert” widget name | Plan §9.2 | Implemented as **`LowStockComponentsWidget`** (same intent) |

Everything else aligns with the plan’s Laravel 12 + Blade + Tailwind + Alpine + Vite stack.

---

## 2. Phase-by-phase status

Legend: **Done** = matches plan intent | **Partial** = implemented with gaps | **N/A** = mostly business/process, not code

### Phase 0 — Repository setup (Plan §3)

| Deliverable | Status |
|-------------|--------|
| Laravel app, Breeze, Vite, core packages | **Done** |
| Filament admin panel | **Done** |
| Git / remote | **Done** (remote may differ from plan’s `aurateria/gicogifts` naming) |

### Phase 1 — Database schema (Plan §4)

| Deliverable | Status |
|-------------|--------|
| Migrations for catalog, orders, carts, BOM, etc. | **Done** |
| Seeders (regions, occasions, products, components, FAQs, stories, admin) | **Done** |
| `migrate:fresh --seed` | **Done** (verify in target environment) |

### Phase 2 — Public routes & pages (Plan §5)

| Deliverable | Status |
|-------------|--------|
| Route map (shop, product, stories, artisans, cart, checkout, account, track, legal, SEO, webhooks) | **Done** |
| Home, shop variants, product detail, cart drawer, checkout flow | **Done** |
| `/shop/region/{slug}` | **Done** (extends plan table) |
| Gigi route | **Done** |

**Partial / verify manually:** COD hidden by default, India Post pincode autofill, Lighthouse ≥ 90 — **confirm on production build**.

### Phase 3 — Frontend components & styling (Plan §6)

| Deliverable | Status |
|-------------|--------|
| Layouts (`app`, `account`, checkout pattern) | **Done** |
| Tailwind brand colors, Fraunces + Inter, Alpine patterns | **Done** |
| `cartDrawer`, `productGallery`, checkout Alpine, `gigiWidget`, announcement bar | **Done** |
| Blade components / partials per plan | **Done** (paths may differ slightly; functionality present) |

### Phase 4 — Payments: Razorpay + Stripe (Plan §7)

| Deliverable | Status |
|-------------|--------|
| `RazorpayService` (order, signature, webhook verify, **refund**) | **Done** |
| Stripe **Checkout Session** for international path + webhook | **Done** (plan text mentions Payment Intents; implementation uses **Checkout** + `stripe_payment_intent_id` on order) |
| `OrderPaid` + listeners: BOM deduct, stock alerts, email, Slack, Shiprocket create | **Done** |
| Invoice PDF (DomPDF) + order confirmation mail | **Done** |
| Webhook-first paid status (no trust browser alone) | **Done** |

**Tests:** Feature tests cover webhooks / flows where implemented.

### Phase 5 — Shiprocket (Plan §8)

| Deliverable | Status |
|-------------|--------|
| `ShiprocketService` (auth cache, create order, serviceability, label URL, etc.) | **Done** |
| `CreateShiprocketOrder` on `OrderPaid` | **Done** |
| Checkout serviceability endpoint | **Done** |
| `/track/{awb}` | **Done** |
| Webhook updates `shipments` | **Done** |
| Filament: shipping label action on order | **Done** |

### Phase 6 — Filament admin (Plan §9)

| Deliverable | Status |
|-------------|--------|
| Resources: Product, Component, Order, Shipment, Category, Occasion, Region, Artisan, Coupon, Review, Faq, **Post**, Story, ContactSubmission, NewsletterSubscriber, User, Setting | **Done** |
| Dashboard widgets: Today stats, revenue chart, recent orders, pending shipments, low stock | **Done** |
| Order actions: Mark packed, resend invoice, pick list PDF, Shiprocket label, **Cancel & refund (Razorpay + Stripe)** | **Done** |
| Components: bulk restock + `inventory_movements` | **Done** |
| Product form: tabs (General, Story, Images, BOM, Shipping, SEO), **Related** (artisans & occasions), **Spatie media**, BOM margin/suggested price, **MarkdownEditor** for `story_md` | **Done** |
| Order **View** page + **read-only relation managers** (items, payments, shipments, status history) | **Done** |
| `OrderResource::canCreate(false)` | **Done** |

### Phase 7 — Gigi AI (Plan §10)

| Deliverable | Status |
|-------------|--------|
| `GigiChatController` + `GigiChatService` + `GigiGeminiClient` | **Done** |
| Level 1 keyword replies (hi, refund, shipping, track, corporate, …) | **Done** |
| Level 2 Gemini + `docs/GIGI_AI_RULEBOOK.md` + live product JSON in prompt | **Done** |
| Level 3 Slack ping on price/discount sensitivity | **Done** |
| Optional `gigi_chat_logs` + `config/gigi.php` | **Done** |
| FAB on main + **account** layout | **Done** |
| Missing `GEMINI_API_KEY` → graceful fallback | **Done** |

### Phase 8 — SEO, content, legal (Plan §11)

| Deliverable | Status |
|-------------|--------|
| `SeoController@robots` + sitemap URL + **AI user-agents** | **Done** |
| `SeoController@sitemap` — dynamic, **cached 1h**, products/stories/artisans/occasions/regions/static, **lastmod** | **Done** |
| Cache invalidation on catalog-related model save/delete | **Done** |
| `partials/seo-head`: Organization JSON-LD, canonical, meta description, OG + Twitter | **Done** |
| Product: Product + Offer + optional AggregateRating + BreadcrumbList | **Done** |
| Story: Article + BreadcrumbList; cover + markdown body | **Done** |
| Occasion/region/artisan: BreadcrumbList JSON-LD where added | **Done** |
| Legal: privacy, terms, shipping, refund — **full copy** | **Done** |
| §11.3 seed content: 3 stories, 5 FAQs, 6 occasions, 5 artisans (incl. Govindji-style names) | **Done** (seeders); occasion **hero images** via `hero_image` + seeder |

**Manual:** Google Rich Results Test on a live product URL before launch.

### Phase 9 — Launch prep (Plan §12)

| Area | Status |
|------|--------|
| Compliance, GST, trademarks, live payment gateways, real photos, Lighthouse, analytics, Sentry | **N/A / manual** — **not** a single code phase; use §12 checklists in the plan |

Optional **future code** (not required by plan MVP): Plausible/GA snippet, Sentry SDK, Telescope on staging only.

---

## 3. Enhancements not spelled out in the original plan

- **Stripe refunds** in Filament (plan §9.3 listed Razorpay-only; Stripe path added).
- **Order `ViewOrder` + infolist + read-only relation managers** for audit-friendly order detail.
- **Sitemap** includes **regions** and **`/cart`**, with **`lastmod`** and **cache busting** on model changes.
- **Gigi** tests + removal of “testing skip” in Gemini client so `Http::fake()` works in PHPUnit.

---

## 4. Quick “what’s left” before public launch

1. **Production `.env`**: `APP_URL`, mail, **live** Razorpay / Stripe / Shiprocket, `BRAND_*`, optional `GEMINI_API_KEY`, `SLACK_WEBHOOK_URL`.
2. **`php artisan migrate`** on server after deploy.
3. **Phase §12.2**: Replace placeholder/picsum imagery with real assets in Filament; real weights/BOM where needed.
4. **Phase §12.3**: One full **live** ₹1-style order end-to-end; mobile pass; Lighthouse.

---

## 5. Related files (high signal)

| Area | Paths |
|------|--------|
| Plan | `GICOGIFTS_CURSOR_BUILD_PLAN.md` |
| Payments | `app/Services/Payments/`, `app/Http/Controllers/WebhookController.php`, `app/Http/Controllers/CheckoutController.php` |
| Shiprocket | `app/Services/Shipping/ShiprocketService.php`, `app/Listeners/CreateShiprocketOrder.php` |
| Filament | `app/Filament/`, `app/Providers/Filament/AdminPanelProvider.php` |
| Gigi | `app/Services/Gigi/`, `app/Http/Controllers/GigiChatController.php`, `docs/GIGI_AI_RULEBOOK.md`, `config/gigi.php` |
| SEO | `app/Http/Controllers/SeoController.php`, `resources/views/partials/seo-head.blade.php` |
| Legal views | `resources/views/frontend/static/*.blade.php` |

---

*Last updated to match repo state at the time of adding this document (Phase 0–8 implementation complete in code; Phase 9 = launch checklist).*
