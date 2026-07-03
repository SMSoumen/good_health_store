# Changeset — GHS Storefront + Merchandising & Moderation Features

**Branch:** `main` · **Period:** 2026-06-25 → 2026-07-02 · **Platform:** eShop Pro (Laravel 10 · Livewire 3 · Spatie Permission)

This document summarizes the full working-tree changeset: a new **GHS (Good Health
Store)** brand storefront theme plus five back-office / merchandising features that
extend products, combos, reviews, and lead capture.

> Companion docs: [`DEVELOPMENT_MANUAL.md`](DEVELOPMENT_MANUAL.md) (platform reference)
> and [`GHS_STOREFRONT.md`](GHS_STOREFRONT.md) (the storefront theme deep-dive).

---

## 1. At a glance

| # | Feature | Type | Key tables |
|---|---------|------|-----------|
| 1 | **GHS storefront theme** | Storefront | — (config + views) |
| 2 | **Review moderation** (product + combo) | Admin + storefront + API | `product_ratings.status`, `combo_product_ratings.status` |
| 3 | **Newsletter subscriptions** | Storefront + admin | `newsletter_subscribers` |
| 4 | **Bulk gifting inquiries** | Storefront + admin | `bulk_gifting_inquiries` |
| 5 | **Product / combo key features** | Admin + seller + storefront | `product_key_features`, `combo_product_key_features` |
| 6 | **Product / combo detail sections** | Admin + seller + storefront | `product_sections`, `combo_product_sections` |
| 7 | **Stickers / badges** | Admin + seller + storefront | `stickers`, `product_sticker`, `combo_product_sticker` |

**Totals:** 37 files modified, ~50 new files, 13 new migrations, ~932 insertions.

---

## 2. GHS storefront theme

A dedicated brand theme (`ghs`) built alongside the existing `elegant` theme, which is
left fully intact as a fallback.

- **Theme switch:** `config/constants.php` — `'theme' => 'ghs'` (was `elegant`).
- **View resolution:** `getHomeTheme()` and `getHeaderStyle()` in
  [`app/function_helper.php`](app/function_helper.php) now short-circuit to
  `livewire.ghs.home.home` and `components.header.ghs` whenever the active theme is
  `ghs`, bypassing the per-store `web_home_page_theme` variant.
- **Footer:** [`Footer.php`](app/Livewire/Footer/Footer.php) selects
  `components.footer.ghs` under the GHS theme (mirrors the header logic).
- **Cloned views:** `resources/views/livewire/ghs/**` — full clone of the `elegant`
  storefront so every route resolves after the switch.
- **New brand components:** `components/ghs/` (entry popup, product card),
  `components/header/ghs.blade.php`, `components/footer/ghs.blade.php`, plus a brand CSS
  layer wired into [`layouts/app.blade.php`](resources/views/components/layouts/app.blade.php)
  only when the theme is `ghs`.
- **Home page:** [`Home.php`](app/Livewire/Home.php) adds three curated blocks —
  `bestSellers()` (top-selling products), `wellnessBundles()` (active combos), and
  `ghsTestimonials()` (approved 4–5★ real reviews topped up with seed testimonials, max 8).

See [`GHS_STOREFRONT.md`](GHS_STOREFRONT.md) for the complete theme walk-through.

---

## 3. Review moderation

Product and combo reviews now require **admin approval** before they are published.

**Data model** — `status` column added to `product_ratings` and `combo_product_ratings`:
`0 = pending`, `1 = approved`, `2 = rejected`. Existing reviews are grandfathered to
`approved` on migration so nothing disappears.

**Storefront / API behaviour**
- Public listings only show `status = 1`:
  [`Reviews.php`](app/Livewire/Products/Reviews.php),
  [`CustomerRatings.php`](app/Livewire/Pages/CustomerRatings.php) (which also shows the
  current user their own pending review), and the mobile API
  ([`ApiController.php`](app/Http/Controllers/App/v1/ApiController.php) — `fetch_rating`
  calls now pass a trailing `status = 1`).
- Submitting or editing a review (re)sets it to **pending (0)**.
- Aggregate rating & count are recomputed from **approved reviews only** via a new
  `recalculateAggregate()` helper, so pending reviews never move the public average.

**Admin moderation UI**
- Single type-aware [`ReviewController`](app/Http/Controllers/Admin/ReviewController.php)
  serves both product (`admin/reviews`) and combo (`admin/combo_reviews`) via route
  `defaults('type', …)`.
- Screen: [`review_moderation.blade.php`](resources/views/admin/pages/forms/review_moderation.blade.php).
- New permissions: `view reviews`, `edit reviews`, `delete reviews`.

---

## 4. Newsletter subscriptions

- **Table:** `newsletter_subscribers` (`email` unique, `status`, `store_id`, `ip_address`).
- **Capture:** the GHS footer form posts to `Footer::subscribe()`
  ([`Footer.php`](app/Livewire/Footer/Footer.php)) — validates email, de-dupes, and
  fires `newsletter-subscribed` (branded popup) or `newsletter-result` (toast) events.
- **Admin:** [`NewsletterController`](app/Http/Controllers/Admin/NewsletterController.php)
  + [`newsletter_subscribers.blade.php`](resources/views/admin/pages/forms/newsletter_subscribers.blade.php)
  (list, status toggle, delete, bulk delete).
- **Permissions:** `view / create / edit / delete newsletter`.

---

## 5. Bulk gifting inquiries

A B2B lead-capture flow for corporate / wedding / festive gifting.

- **Table:** `bulk_gifting_inquiries` — contact fields, `requirement_types` (JSON
  multi-select: corporate, wedding, festive, reselling, bulk, other),
  `estimated_quantity`, `message`, `status` (`0 = new`, `1 = contacted/closed`).
- **Storefront:** [`BulkGiftingOrders`](app/Livewire/Pages/BulkGiftingOrders.php) Livewire
  page at `/bulk-gifting-orders` with an on-page success state.
- **Admin:** [`BulkGiftingInquiryController`](app/Http/Controllers/Admin/BulkGiftingInquiryController.php)
  + [`bulk_gifting_inquiries.blade.php`](resources/views/admin/pages/forms/bulk_gifting_inquiries.blade.php)
  with **CSV export**, status toggle, delete, bulk delete.
- **Permissions:** `view / edit / delete bulk_gifting_inquiries`.

---

## 6. Product & combo merchandising

Three repeatable content blocks were added to both regular products and combo products,
backed by shared helpers in [`app/function_helper.php`](app/function_helper.php) and
relations on [`Product`](app/Models/Product.php) / [`ComboProduct`](app/Models/ComboProduct.php).

### 6.1 Key features
- Tables `product_key_features` / `combo_product_key_features` (`feature`, `details`, `row_order`).
- Helper `saveKeyFeatures($modelClass, $productId, $rows)` — delete-then-insert sync,
  skips blank rows, preserves order.
- Models: `ProductKeyFeature`, `ComboProductKeyFeature`; relation `keyFeatures()`.
- Views: `components/key-features-form.blade.php`, `components/key-features-section.blade.php`.

### 6.2 Detail sections (tabs)
- Tables `product_sections` / `combo_product_sections` (`title`, `content` longtext,
  `is_active`, `row_order`).
- Helper `saveProductSections(...)` — same sync strategy; a slot is skipped only when
  both title and content are empty.
- Models: `ProductSection`, `ComboProductSection`; relation `sections()`.
- Views: `product-section-tab-links`, `product-section-tab-content`,
  `product-sections-form`, plus FAQ tab partials and `product-details-styles.blade.php`.

### 6.3 Stickers / badges
- Store-wide master list `stickers` (`text`, `image`, `status`) with many-to-many pivots
  `product_sticker` / `combo_product_sticker`.
- Helpers: `syncStickers()`, `getStickersList()`, `getAttachedStickerIds()`.
- Model `Sticker`; relation `stickers()` (belongsToMany).
- Admin + seller CRUD: [`Admin\StickerController`](app/Http/Controllers/Admin/StickerController.php),
  [`Seller\StickerController`](app/Http/Controllers/Seller/StickerController.php), with
  `stickers` / `update_sticker` blades and `stickers-section` / `stickers-select` partials.

### 6.4 Form & service wiring
- Product/combo create & edit forms (admin + seller) gain the key-features, sections, and
  sticker selectors; `ProductController` / `ComboProductController` (admin + seller) and
  `ProductService` / `ComboProductService` persist them via the shared helpers.
- Storefront detail pages ([`details.blade.php`](resources/views/livewire/elegant/products/details.blade.php),
  [`combo-details.blade.php`](resources/views/livewire/elegant/products/combo-details.blade.php))
  render the new blocks.
- Seller product routes add `change_variant_status` and `delete_variant` endpoints.

---

## 7. Navigation & permissions

- Admin and seller sidebars gain entries for Stickers, Reviews, Newsletter, and Bulk
  Gifting ([`admin/side-bar.blade.php`](resources/views/components/admin/side-bar.blade.php),
  [`seller/side-bar.blade.php`](resources/views/components/seller/side-bar.blade.php)).
- New routes registered in `routes/admin_routes.php`, `routes/seller_routes.php`,
  `routes/front_end_routes.php`.
- New Spatie permissions (run migrations to register): newsletter (×4), reviews (×3),
  bulk gifting (×3). Assign them to the relevant roles after deploy.

---

## 8. Migration order

Run `php artisan migrate`. New migrations (13):

```
2026_06_25_000001_create_newsletter_subscribers_table
2026_06_25_000002_add_newsletter_permissions
2026_06_25_000003_add_status_to_product_ratings_table          # grandfathers to approved
2026_06_25_000004_add_review_permissions
2026_06_25_000005_add_status_to_combo_product_ratings_table
2026_06_29_000001_create_bulk_gifting_inquiries_table
2026_06_29_000002_add_bulk_gifting_permissions
2026_06_30_000001_create_product_key_features_table
2026_06_30_000002_create_combo_product_key_features_table
2026_07_01_000001_create_product_sections_table
2026_07_01_000002_create_combo_product_sections_table
2026_07_02_000001_create_stickers_table
2026_07_02_000002_create_product_sticker_table
2026_07_02_000003_create_combo_product_sticker_table
```

## 9. Deploy checklist

1. `php artisan migrate`
2. Assign the new permissions to admin/seller roles.
3. Confirm `config('constants.theme')` is `ghs` (clear config cache if cached).
4. Ensure GHS brand assets exist under `public/frontend/ghs/`.
5. Smoke-test: storefront home, a product detail page, review submit → admin approve,
   newsletter signup, and the bulk-gifting form.
