# eZeeMart — Developer Manual

A development reference for the eZeeMart multi-vendor e-commerce platform. This document
describes the architecture, project layout, conventions, and the day-to-day workflow for
working on the codebase.

> **Stack at a glance:** Laravel 10 · PHP 8.1+ · MySQL · Livewire 3 (storefront) ·
> Blade (admin / seller / delivery / affiliate panels) · Sanctum (API auth) · Vite +
> Tailwind (+ React libs available) · Spatie Permission · Pusher / Chatify (real-time chat) ·
> Firebase FCM (push) · Multiple payment gateways.

---

## Table of Contents

1. [What this project is](#1-what-this-project-is)
2. [Tech stack & key dependencies](#2-tech-stack--key-dependencies)
3. [Project structure](#3-project-structure)
4. [Application architecture](#4-application-architecture)
5. [The four panels + storefront + APIs](#5-the-four-panels--storefront--apis)
6. [Routing](#6-routing)
7. [Middleware](#7-middleware)
8. [Multi-store architecture](#8-multi-store-architecture)
9. [Authentication, roles & permissions](#9-authentication-roles--permissions)
10. [Services layer](#10-services-layer)
11. [Models & data layer](#11-models--data-layer)
12. [Global helpers](#12-global-helpers)
13. [Payment gateways](#13-payment-gateways)
14. [Media, storage & file handling](#14-media-storage--file-handling)
15. [Notifications, chat & queues](#15-notifications-chat--queues)
16. [Localization / translations](#16-localization--translations)
17. [Frontend & asset pipeline](#17-frontend--asset-pipeline)
18. [Configuration & environment](#18-configuration--environment)
19. [Local setup](#19-local-setup)
20. [Common development tasks](#20-common-development-tasks)
21. [Conventions & gotchas](#21-conventions--gotchas)
22. [Utility & maintenance routes](#22-utility--maintenance-routes)

---

## 1. What this project is

eZeeMart (internal/db name: `eshop_plus` / `eShop Pro`) is a **multi-vendor / multi-store
e-commerce platform**. A single installation serves:

- A **customer-facing storefront** (web, built with Livewire) and a **mobile app** (consumes
  the REST API).
- An **Admin panel** to manage the whole platform.
- A **Seller panel** for vendors to manage their own products, orders, stock, and payouts.
- A **Delivery Boy panel + app** for order delivery and cash collection.
- An **Affiliate panel** for referral-based marketing and commissions.

Core commerce features: products (simple, variant, **combo**, digital), categories/brands,
attributes, offers/sliders, promo codes, taxes, multi-currency, wallet, reviews/ratings,
FAQs, blogs, tickets/support, returns, Shiprocket shipping integration, and SEO/sitemap.

---

## 2. Tech stack & key dependencies

### Backend (`composer.json`)
| Concern | Package |
|---|---|
| Framework | `laravel/framework` 10.* (PHP ^8.1) |
| API tokens | `laravel/sanctum` |
| Social login | `laravel/socialite` (Google, Facebook) |
| Permissions/roles | `spatie/laravel-permission` |
| Media management | `spatie/laravel-medialibrary` |
| Translatable models | `spatie/laravel-translatable` |
| Sitemap | `spatie/laravel-sitemap` |
| Real-time chat | `munafio/chatify` + `pusher/pusher-php-server` |
| Invoices (PDF) | `laraveldaily/laravel-invoices` |
| Images | `intervention/image`, `imagine/imagine` |
| AWS / S3 | `aws/aws-sdk-php`, `league/flysystem-aws-s3-v3` |
| Push (FCM) | `google/apiclient` (FirebaseCloudMessaging service) |
| Payments | `stripe/stripe-php`, `razorpay/razorpay`, `srmklive/paypal` |
| CSV | `league/csv` |
| Reactive UI | `livewire/livewire` 3 |

### Frontend (`package.json`)
- Build: **Vite 4** + `laravel-vite-plugin`, **Tailwind 3**, `@vitejs/plugin-react`.
- A large set of **React / MUI / Inertia** libraries is declared (used by certain
  bundled UI components / the React-based build path); the live web storefront itself is
  **Livewire + Blade**.
- Payment SDKs: Stripe, PayPal, Razorpay, Paystack, Flutterwave (client side).
- Firebase JS SDK (web push / auth helpers).

---

## 3. Project structure

```
app/
├── Console/Commands/        # Artisan commands (GenerateSitemap, SendCartReminders)
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           # ~55 admin controllers
│   │   ├── Seller/          # seller panel controllers (+ v1/ApiController)
│   │   ├── Delivery_boy/    # delivery panel controllers (+ v1/ApiController)
│   │   ├── Affiliate/       # affiliate panel controllers
│   │   ├── App/v1/          # mobile app REST API (ApiController)
│   │   ├── Auth/            # ForgotPasswordController
│   │   ├── vendor/Chatify/  # overridden Chatify messaging controllers
│   │   └── *.php            # shared/storefront controllers (Cart, Product, etc.)
│   └── Middleware/          # ~30 middleware (auth, permissions, store, language, demo…)
├── Jobs/                    # queued jobs (FCM / order / seller notifications)
├── Libraries/               # payment gateway wrappers + Shiprocket
├── Livewire/                # storefront components (the web frontend)
├── Models/                  # ~78 Eloquent models
├── Providers/               # service providers
├── Services/                # business-logic service classes (the "brain")
├── Traits/                  # HandlesValidation
├── function_helper.php      # ~1850 lines of global helper functions
├── sms_helper.php           # SMS helpers
└── ChatifyMessenger.php     # custom Chatify messenger override

routes/        # split by audience (see §6)
config/        # standard Laravel config + app-specific (constants, eshop_pro, chatify, manifest…)
database/      # migrations (20), seeders, factories — note: full schema imported via SQL dump
resources/
├── views/
│   ├── admin/   seller/   delivery_boy/   affiliate/   # Blade panels
│   ├── livewire/                                       # storefront component views
│   ├── components/  partials/  auth/  errors/  vendor/
├── lang/        # ar, en, gu, hi, hn, ja, ... language packs
├── css/  js/    # Vite entry points
developer_notes/ # internal notes (e.g. emailTemplates.txt)
public/          # web root, compiled assets, storage symlink target
storage/         # logs, app storage, dotenv-editor backups
```

---

## 4. Application architecture

The application follows a **fat-service / thin-controller** pattern layered on top of
standard Laravel MVC, with Livewire driving the reactive storefront.

```
                 ┌──────────────────────────────────────────────┐
   Mobile app ──▶│ routes/api.php  → App\Http\Controllers\App\v1 │
   Seller app ──▶│ routes/seller_api.php → Seller\v1\ApiController│──┐
 Delivery app ──▶│ routes/delivery_boy_api.php → ...\v1\ApiCtrl   │  │
                 └──────────────────────────────────────────────┘  │
                                                                    ▼
   Browser  ────▶ routes/web.php ─┬─ admin_routes.php   (Blade admin)   ┌──────────────┐
                                  ├─ seller_routes.php  (Blade seller)──▶│  Services    │
                                  ├─ delivery_boy_routes.php             │  (business   │
                                  ├─ affiliate_routes.php                │   logic)     │
                                  └─ front_end_routes.php (Livewire) ───▶│              │
                                                                         └──────┬───────┘
                                                          function_helper.php   │
                                                          (global helpers) ◀────┤
                                                                                ▼
                                                                          Eloquent Models
                                                                                │
                                                                                ▼
                                                                             MySQL
```

Key idea: **controllers and Livewire components stay thin**; real work (placing orders,
computing prices, sending notifications, syncing shipping, handling media) lives in
`app/Services/*`. Cross-cutting utilities (slugs, price/tax math, fetch helpers, OTP, sliders,
dashboard counts) live in `app/function_helper.php` and are auto-loaded globally.

---

## 5. The four panels + storefront + APIs

| Audience | Entry route file | Controllers | UI |
|---|---|---|---|
| **Admin** | `admin_routes.php` | `App\Http\Controllers\Admin\*` | Blade (`resources/views/admin`) |
| **Seller** | `seller_routes.php` | `App\Http\Controllers\Seller\*` | Blade (`resources/views/seller`) |
| **Delivery boy** | `delivery_boy_routes.php` | `App\Http\Controllers\Delivery_boy\*` | Blade (`resources/views/delivery_boy`) |
| **Affiliate** | `affiliate_routes.php` | `App\Http\Controllers\Affiliate\*` | Blade (`resources/views/affiliate`) |
| **Storefront (web)** | `front_end_routes.php` | `App\Livewire\*` + shared controllers | Livewire (`resources/views/livewire`) |
| **Mobile app API** | `api.php` | `App\Http\Controllers\App\v1\ApiController` | JSON |
| **Seller app API** | `seller_api.php` | `Seller\v1\ApiController` | JSON |
| **Delivery app API** | `delivery_boy_api.php` | `Delivery_boy\v1\ApiController` | JSON |

The storefront is **conditionally enabled**: `web.php` only includes `front_end_routes.php`
when `app/Livewire/Products/Details.php` physically exists. When the storefront component is
absent, the public product/seller/blog URLs instead fall through to
`DeepLinkRedirectController` (which redirects web visitors into the mobile app).

---

## 6. Routing

Routes are **split by audience** instead of living in one file. Registration happens in
`App\Providers\RouteServiceProvider`:

- `routes/api.php` → prefix `api`, `api` middleware group
- `routes/seller_api.php` → prefix `seller_api`
- `routes/delivery_boy_api.php` → prefix `delivery_boy_api`
- `routes/web.php` → `web` middleware group

`web.php` is the orchestrator. Inside an `auth` group it `include_once`s the four panel
route files:

```php
Route::group(['middleware' => ['auth']], function () {
    include_once("admin_routes.php");
    include_once("seller_routes.php");
    include_once("delivery_boy_routes.php");
    include_once("affiliate_routes.php");
});
// front_end_routes.php included separately, only if the storefront component exists
```

**Naming conventions** (used heavily — prefer `route('...')` / route names over hardcoded URLs):
- Admin: `admin.*` (e.g. `admin.home`, `admin.orders.generatInvoicePDF`)
- Seller: `seller.*`
- Delivery: `delivery_boy.*`
- Affiliate: `affiliate.*`
- Storefront: bare names (`home`, `cart`, `products`, `orders`, `my-account.*`)

`admin_routes.php` alone defines **~380 named routes** — it is the largest route surface.

API rate limit: 60 req/min per user (or IP) — `RateLimiter::for('api', …)`.

---

## 7. Middleware

Global stack and groups are in `app/Http/Kernel.php`. The `web` group is extended with
several app-specific middleware:

```
web group: EncryptCookies → AddQueuedCookies → StartSession → ShareErrors →
           VerifyCsrfToken → LanguageManager → SubstituteBindings →
           GetDefaultData → SetDefaultStore → LogoutMiddleware
```

**Named middleware aliases** worth knowing:

| Alias | Class | Purpose |
|---|---|---|
| `auth` | `Authenticate` | Standard auth (custom redirect) |
| `check_token` | `CheckToken` | Validates API token alongside Sanctum |
| `language` | `LanguageMiddleware` | Sets locale for API responses |
| `permissions` | `CheckPermissions` | Spatie permission gate (`permissions:create store`) |
| `role` | `RoleMiddleware` | Role gate |
| `demo_restriction` | `DemoRestriction` | Blocks write ops in DEMO mode |
| `CheckInstallation` | `CheckInstallation` | Redirects to installer if not installed |
| `CheckPurchaseCode` | `CheckPurchaseCode` | License/purchase-code check |
| `CheckDefaultStore` / `SetDefaultStore` / `CheckStoreNotEmpty` | — | Multi-store guards (§8) |
| `guest` | `RedirectIfAuthenticated` | Guest-only routes |

`CheckPermissions` (`permissions:<perm>` on a route) auto-bypasses for the `super_admin`
role, otherwise checks Spatie `hasPermissionTo`. It returns JSON `{error: true, …}` for
`expectsJson()` requests, else throws `AuthorizationException`.

---

## 8. Multi-store architecture

A single installation can host multiple **stores**. One store is flagged `is_default_store`.

- `SetDefaultStore` middleware (web group) resolves the active store per request and pins it
  into the session: `store_id`, `store_name`, `store_image`, `store_slug`,
  `default_store_slug`. It can switch store via the `?store=<slug>` query param and manages
  the "choose store" popup flag (`show_store_popup`).
- The storefront `set_store` route writes the chosen store into the session.
- Most storefront/API queries are **scoped by `store_id`** (e.g.
  `OrderService::getRecentOrdersForNotifications($store_id)`), so when adding new
  store-facing queries, **always filter by the session/store id**.
- `SetDefaultStore` also short-circuits when the app is not yet installed (detects
  `eshop_plus.sql` / `install.blade.php`).

Related models: `Store`, `SellerStore` (pivot linking users↔stores), `StoreService`.

---

## 9. Authentication, roles & permissions

- **Web/panel auth**: Laravel session guard. The `User` model uses Spatie `HasRoles` +
  `HasPermissions`, Sanctum `HasApiTokens`, MediaLibrary `InteractsWithMedia`, and
  `CanResetPassword`.
- **API auth**: `auth:sanctum` + custom `check_token` middleware (most authenticated API
  routes are grouped under both).
- **Roles**: stored on `users.role_id` → `Role` model; `super_admin` bypasses permission
  checks. Roles/permissions managed via `Admin\UserPermissionController` &
  `Admin\UserController`.
- **Social login**: Google & Facebook via Socialite (`auth/google`, `auth/facebook`
  callbacks in `front_end_routes.php`; credentials in `config/services.php`).
- **OTP**: phone/email OTP flows via `Otps` model and helpers `set_user_otp`,
  `validateOtp`, `checkOTPExpiration` (see `function_helper.php` / `sms_helper.php`).

`User` relationships: `role`, `seller_data` (Seller), `products` (as seller), `stores`
(many-to-many via `seller_store`), `sellerStore`, `favorites`, `address`, `affiliateUser`,
`city`.

---

## 10. Services layer

Business logic lives in `app/Services/`. **Put new domain logic here**, not in controllers.

| Service | Responsibility |
|---|---|
| `OrderService` | Order placement, status, invoices, charges, seller payouts (large, central) |
| `CartService` | Cart line-items, totals, sync |
| `ProductService` / `ComboProductService` | Product & combo retrieval, pricing, variants |
| `PromoCodeService` | Promo validation & discount application |
| `CurrencyService` | Multi-currency conversion / formatting |
| `WalletService` | Wallet balance, refill, withdrawals |
| `DeliveryService` / `ParcelService` | Deliverability checks, parcels |
| `ShiprocketService` | Shiprocket shipping integration (serviceability, orders) |
| `SellerService` | Seller commission, store linkage |
| `StoreService` | Store resolution & settings |
| `SettingService` | App/system settings access |
| `MediaService` | Media upload/transform via MediaLibrary |
| `SeoService` | SEO meta + sitemap data |
| `TranslationService` | Runtime label/translation lookups |
| `FirebaseNotificationService` | FCM push composition/sending |
| `MailService` | Transactional email |
| `CustomPathGenerator` / `CustomFileRemover` | MediaLibrary path/cleanup overrides |
| `DeletionService` | Cascade-safe deletes |

`OrderService::placeOrder($data, $for_web, $language_code)` is the canonical example: it
orchestrates many other services (cart, product, delivery, wallet, shiprocket, currency,
notifications, PDF invoice) in one flow.

---

## 11. Models & data layer

- **~78 Eloquent models** in `app/Models/`. Highlights: `User`, `Seller`, `Store`,
  `SellerStore`, `Product`, `Product_variants`, `Product_attributes`, `Attribute`,
  `Attribute_values`, `Category`, `Brand`, `ComboProduct` (+ attributes/values/faq/rating),
  `Order`, `OrderItems`, `OrderCharges`, `OrderTracking`, `OrderBankTransfers`,
  `ReturnRequest`, `Parcel`/`Parcelitem`, `Promocode`, `Tax`, `Currency`, `Transaction`,
  `PaymentRequest`, `FundTransfer`, `Wallet` (via Transaction), `Ticket`/`TicketMessage`/
  `TicketType`, `Blog`/`BlogCategory`, `Slider`/`OfferSliders`/`CategorySliders`/`Offer`,
  `Setting`, `Seo`, `Language`, `Zone`/`Area`/`City`/`Zipcode`/`Country`,
  `Deliveryboy`, `AffiliateUser`/`AffiliateTracking`/`AffiliateTransaction`,
  `UserFcm`/`Notification`, `CustomField` + `*CustomFieldValue`, `Media`/`Image`/`StorageType`.

- **Translatable** models use `spatie/laravel-translatable` (JSON translation columns).
- **Media** is attached via MediaLibrary collections (e.g. `User::registerMediaCollections`
  picks `s3` vs `public` disk based on the default `StorageType`).

### ⚠️ Database schema
Only **20 migrations** exist (users, password resets, jobs, tokens, Chatify, plus recent
incremental ALTERs). The **full schema is imported from an SQL dump** (`eshop_plus.sql`,
referenced by the installer / `SetDefaultStore`). Therefore:

- Do **not** assume `php artisan migrate:fresh` rebuilds the whole schema — it will not.
- New schema changes since the base dump are added as **incremental dated migrations**
  (e.g. `2026_01_22_*_add_shipping_option_to_orders_table.php`). Follow that pattern: add a
  new migration for changes; don't edit the dump unless changing the base install.

---

## 12. Global helpers

`app/function_helper.php` (~1850 lines) and `app/sms_helper.php` are auto-loaded via
composer `files` autoload. They provide **global functions used everywhere** — check here
before writing new utilities. Notable ones:

| Helper | Use |
|---|---|
| `fetchDetails($model, $where, $columns)` | Generic guarded query (returns collection) |
| `updateDetails(...)` / `deleteDetails(...)` | Generic update/delete |
| `generateSlug($name, $table, $field, ...)` | Unique slug generation |
| `getCategoriesOptionHtml(...)` / `subCategories(...)` / `renderCategories(...)` | Category trees |
| `calculatePriceWithTax(...)` / `calculatePrice(...)` / `findDiscountInPercentage(...)` / `formatePriceDecimal(...)` | Pricing/tax math |
| `validateStock(...)` / `countProductsStockLowStatus(...)` / `isProductMisconfigured(...)` | Stock/product validation |
| `getSliders(...)` / `getFavorites(...)` / `getReturnRequest(...)` | Storefront data builders |
| `set_user_otp(...)` / `validateOtp(...)` / `checkOTPExpiration(...)` / `parse_sms(...)` | OTP / SMS |
| `getAuthenticatedUser()` | Resolve API user |
| `labels(...)` | Translation labels |
| `customUrl(...)` / `setUrlParameter(...)` | Store-aware URL building |
| `getProductDisplayComponent()` / `getHomeTheme()` / `getHeaderStyle()` / `getProductDetailsStyle()` | Theme/layout switching |
| `processReferralBonus(...)` | Affiliate referral payouts |
| `get_system_update_info()` / `get_current_version()` | Updater |
| `AdmintotalEarnings()` / `countNewUsers()` / `countDeliveryBoys()` / `getTransactions()` | Admin dashboard metrics |

> **Convention:** every helper is wrapped in `if (!function_exists('…'))`. Keep that guard
> when adding new helpers to avoid redeclare errors.

---

## 13. Payment gateways

Gateway wrappers live in `app/Libraries/`: `Stripe`, `Razorpay`, `Paypal`, `Paystack`,
`Phonepe`, `Midtrans`, plus `Shiprocket` (shipping). Flows:

- **Web checkout**: `PaymentsController` (`payments.stripe`, `payments.razorpay`,
  `payments.phonepe`, `payments.stripe_response`, `verifyRazorpay`) + `Livewire\Payments\Status`
  for the `payments.payment_response` callback.
- **Mobile app**: `App\v1\ApiController` endpoints (`razorpay_create_order`, `phonepe_app`,
  `paystack_webview`, `get_paypal_link`, `paypal_transaction_webview`,
  `handle_paystack_callback`, `app_payment_status`, `ipn`).
- **Webhooks**: `Admin\Webhook` controller —
  `razorpay_webhook`, `paystack_webhook`, `stripe_webhook`, `phonepe_webhook`,
  `spr_webhook` (Shiprocket). Registered in `web.php` under `admin/webhook/*`.

Wallet top-ups/withdrawals route through `WalletController` + `WalletService`; bank-transfer
proofs via `OrderBankTransfers`.

---

## 14. Media, storage & file handling

- **Spatie MediaLibrary** is the primary media system; disk is chosen dynamically from the
  default `StorageType` (`public` vs `s3`). See `config/media-library.php`,
  `Services/MediaService`, `Services/CustomPathGenerator`, `Services/CustomFileRemover`.
- **Storage paths / constants** in `config/constants.php`: `MEDIA_PATH`, `USER_IMG_PATH`,
  `STORE_IMG_PATH`, `SELLER_IMG_PATH`, `DELIVERY_BOY_IMG_PATH`, `REVIEW_IMG_PATH`,
  `TICKET_IMG_PATH`, `CUSTOM_FIELD_FILE_PATH`, plus default image fallbacks (`NO_IMAGE`,
  `DEFAULT_LOGO`, …) and ticket-status codes (`PENDING`/`OPENED`/`RESOLVED`/`CLOSED`/`REOPEN`).
- **Dynamic image endpoint**: `MediaController::dynamic_image`
  (`admin.dynamic_image` / `front_end.dynamic_image`) serves/transforms images on demand.
- **Storage symlink**: there is a `/storage-link` web route that (re)creates
  `public/storage`, falling back to copying files when `symlink()` is unavailable (shared
  hosting). Use it instead of `php artisan storage:link` on hosts without symlink support.
- Image processing via Intervention Image / Imagine.

---

## 15. Notifications, chat & queues

- **Push (FCM)**: `FirebaseNotificationService` + queued jobs
  `SendFcmNotificationJob`, `SendOrderNotificationJob`, `SendSellerNotificationJob`.
  Device tokens stored in `UserFcm`. Web manifest at `/manifest` (`config/manifest.php`),
  PWA support via `ladumor/laravel-pwa`.
- **Real-time chat**: Chatify (`munafio/chatify`) with custom overrides in
  `app/ChatifyMessenger.php` and `app/Http/Controllers/vendor/Chatify/*`. Models `ChMessage`,
  `ChFavorite`. Broadcasting via **Pusher** (`config/broadcasting.php`, `BROADCAST_DRIVER`,
  `PUSHER_*` env). Storefront live support: `Livewire\MyAccount\LiveChat`.
- **Queues**: `QUEUE_CONNECTION` (database/sync per env). Jobs dispatched from services.
  There are helper web routes (`/run-queue`, `/queue-work`, `/test-job`) for hosts without a
  long-running worker — prefer a real `queue:work` worker / supervisor in production.
- **Email**: `MailService`; templates referenced in `developer_notes/emailTemplates.txt`;
  digital-order mail via `DigitalOrdersMail` model/mailable. SMTP via `MAIL_*` env.
- **Cart reminders**: `SendCartReminders` console command (with `CartReminder` model) —
  schedule via cron/scheduler.

---

## 16. Localization / translations

- Language packs under `resources/lang/<locale>/` (ar, en, gu, hi, hn, ja, …).
- Web locale set by `LanguageManager` middleware (web group); API locale by `language`
  middleware (applied per-route on most `get_*` API endpoints).
- Runtime translations via `spatie/laravel-translatable` (model JSON columns) +
  `TranslationService` + `labels()` helper.
- Front-end language management in admin: `Admin\FrontLanguageController`,
  `Admin\LanguageController`; API exposes `get_languages`, `get_language_labels`,
  `get_language_file_info`.

---

## 17. Frontend & asset pipeline

- **Build tool**: Vite. Entry inputs declared in `vite.config.js`
  (`resources/css/app.css`, `resources/js/app.jsx`) with `@vitejs/plugin-react` and
  `laravel-vite-plugin` (`refresh: true`). Tailwind config in `tailwind.config.js`.
- **Storefront UI** = **Livewire 3 components** in `app/Livewire/*` with views in
  `resources/views/livewire/*`. Organized by feature: `Home`, `Products`, `Cart`,
  `Categories`, `Brands`, `Sellers`, `Offers`, `Blogs`, `MyAccount`, `Orders`, `Payments`,
  `Compare`, `Header`, `Footer`, `Pages`, `RegisterAndLogin`.
- **Admin/seller/delivery/affiliate UIs** = classic **Blade** under `resources/views/<panel>`
  with shared `layout.blade.php`, `include_css.blade.php`, `include_script.blade.php`.
- **Theme switching**: admin can change storefront card/category/brand/wishlist/home styles;
  the chosen style is resolved at runtime by the `get*DisplayComponent()` / `getHomeTheme()`
  / `getHeaderStyle()` / `getProductDetailsStyle()` helpers, and there are dedicated style
  preview Blade views (`webProductCardStyle.blade.php`, etc.) served from `web.php`.

Run `npm run dev` during development; `npm run build` for production assets.

---

## 18. Configuration & environment

App-specific config files (beyond Laravel defaults) in `config/`:

| File | Purpose |
|---|---|
| `constants.php` | App constants: paths, ticket statuses, DEMO flags, theme, purchase codes (`APP_CODE`, `WEB_CODE`) |
| `eshop_pro.php` | Product-level settings |
| `chatify.php` | Chat config |
| `manifest.php` | PWA / web manifest payload (served at `/manifest`) |
| `media-library.php` | Media disks/conversions |
| `permission.php` | Spatie permission tables |
| `invoices.php` | PDF invoice settings |
| `services.php` | Google/Facebook/AWS/Mailgun/Postmark credentials |
| `sitemap.php` | Sitemap generation |

**`.env` essentials** (see current `.env`):
- App: `APP_NAME`/`AAP_NAME`, `APP_ENV`, `APP_KEY`, `APP_URL`, `APP_DEBUG`.
- DB: `DB_CONNECTION=mysql`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
- Queue/cache/session: `QUEUE_CONNECTION`, `CACHE_DRIVER`, `SESSION_DRIVER`.
- Mail: `MAIL_*` (note: there is a typo'd `MAMAIL_MAILER` in the current file — the correct
  key is `MAIL_MAILER`; verify when configuring SMTP).
- Broadcast/chat: `BROADCAST_DRIVER`, `PUSHER_*`, and matching `VITE_PUSHER_*`.
- Storage: `FILESYSTEM_DISK`, `AWS_*` (for S3 media).
- Social: `GOOGLE_*`, `FACEBOOK_*` redirect/credentials.

> The committed `.env` contains real-looking Pusher keys and DB creds — **rotate secrets**
> and never reuse these in production. Treat `.env` as sensitive.

---

## 19. Local setup

> Windows / PowerShell environment (this repo is **not** a git repo as cloned here).

```powershell
# 1. PHP deps
composer install

# 2. JS deps
npm install

# 3. Environment
#    .env already exists; if starting clean, copy .env.example and:
php artisan key:generate
#    Set DB_DATABASE / DB_USERNAME / DB_PASSWORD and MAIL_* / PUSHER_* as needed.

# 4. Database
#    The full schema comes from the SQL dump (eshop_plus.sql), not from migrate:fresh.
#    Import the dump into your MySQL database, then run incremental migrations:
php artisan migrate

# 5. Storage symlink (use the web route on hosts without symlink support)
php artisan storage:link        # or visit /storage-link in the browser

# 6. Build assets
npm run dev                      # or: npm run build

# 7. Serve
php artisan serve                # http://127.0.0.1:8000
```

First-run installer (`/install`, `InstallerController`) is available when not yet installed
(`CheckInstallation` middleware). Admin entry: `/admin` → `admin.login` → `admin.home`.

---

## 20. Common development tasks

**Add an admin feature**
1. Add controller in `app/Http/Controllers/Admin/`.
2. Register route in `routes/admin_routes.php` (named `admin.*`, guard with
   `permissions:<perm>` and `demo_restriction` on writes).
3. Put domain logic in a `Service`; keep the controller thin.
4. Add Blade view under `resources/views/admin/pages/…` using the admin `layout`.

**Add a storefront feature**
1. Create a Livewire component in `app/Livewire/<Feature>/`.
2. Add view in `resources/views/livewire/…`.
3. Register route in `routes/front_end_routes.php` (bare name), wrapping in `auth`/`guest`
   group as appropriate.
4. Scope all data by the session `store_id`.

**Add an API endpoint (mobile app)**
1. Add method to `App\Http\Controllers\App\v1\ApiController`.
2. Register in `routes/api.php` — public, or inside the
   `['check_token', 'auth:sanctum']` group for authenticated endpoints.
3. Add `->middleware('language')` for localized responses.
4. Return the standard envelope `{ error: bool, message: string, data: ... }`.

**Add a schema change**
- Create a **new dated migration** (`php artisan make:migration`) — do not edit the base
  SQL dump. Match the existing incremental-ALTER pattern.

**Add a payment gateway**
- Add a wrapper in `app/Libraries/`, wire web/app entry points
  (`PaymentsController` / `ApiController`) and a webhook handler in `Admin\Webhook`.

---

## 21. Conventions & gotchas

- **Thin controllers, fat services.** Reach for `app/Services/*` and `function_helper.php`
  before adding logic to controllers/components.
- **Always wrap new globals** in `if (!function_exists('…'))`.
- **Store scoping is mandatory** for storefront/API data — filter by session `store_id`.
- **DEMO mode**: `config/constants.php` `DEMO`/`ALLOW_MODIFICATION` + `demo_restriction`
  middleware block destructive operations on demo deployments. Apply `demo_restriction` to
  new write routes.
- **Permissions**: gate admin/seller writes with `permissions:<perm>`; `super_admin` bypasses.
- **Schema lives in an SQL dump**, not migrations — `migrate:fresh` won't rebuild it.
- **Storefront is conditional**: only loads when `Livewire/Products/Details.php` exists;
  otherwise public URLs go to the deep-link app redirect.
- **Route names over URLs**: use `route('admin.home')` etc. — there are 380+ named admin
  routes for a reason.
- **Filename caveat**: a couple of files have encoding/typo artifacts in their names
  (e.g. a migration with a stray Unicode quote, `InstallerController-old.php`,
  `*-old.blade.php`). Leave the `-old` files alone; they're legacy backups.
- **Env typo**: current `.env` uses `MAMAIL_MAILER` (should be `MAIL_MAILER`) — fix before
  relying on mail.
- **Secrets in `.env`** are committed — rotate before any deployment.

---

## 22. Utility & maintenance routes

Defined in `routes/web.php` — handy on shared hosting where artisan/CLI access is limited:

| Route | Action |
|---|---|
| `/clear-cache` | `optimize:clear` + cache/config/route/view clear |
| `/clear-logs` | Truncates `storage/logs/laravel.log` |
| `/sitemap` | Runs `sitemap:generate` |
| `/storage-link` | (Re)creates `public/storage` (symlink or copy fallback) |
| `/version` | Returns Laravel version |
| `/publish-livewire-assets` | Publishes Livewire assets |
| `/run-queue`, `/queue-work` | Process queued jobs once / until empty |
| `/test-mail`, `/test-job`, `/test-deep-links`, `/api/test` | Diagnostics |
| `/install`, `/installer/*` | First-run installer |

> ⚠️ Several of these (`/clear-cache`, `/clear-logs`, `/run-queue`, `/test-*`) are
> **unauthenticated**. Consider protecting or removing them in production.

---

### Maintenance of this manual
When you add a panel, service, gateway, or API surface, update the relevant section above so
this stays the single source of truth for onboarding. Generated as a development reference;
verify specifics against the code before relying on edge-case behavior.
