# GHS Storefront — New Livewire Theme

A development log and reference for the **Good Health Store (GHS)** storefront — a new
Livewire theme (`ghs`) built on top of the existing eShop Pro platform.

> **Brand:** Good Health Store — *"Organic Kashmiri Wellness"*
> **Approach:** New theme folder `ghs` (the existing `elegant` theme is left fully intact
> as an on-disk fallback), reusing the existing Bootstrap/jQuery asset stack and restyling
> it with a brand CSS layer.

---

## 1. Design decisions

Two decisions shaped the whole effort:

| Decision | Choice | Why |
|---|---|---|
| **Theme strategy** | New theme folder `ghs` | Cleanest separation. `elegant` stays untouched as a working fallback; switching themes is a one-line config change. |
| **Styling approach** | Reuse Bootstrap stack, restyle | Keeps cart, checkout, wallet, sliders and all wired JS working. We add a GHS brand CSS layer on top instead of rebuilding components. |

### How theming works in this app
- View paths are resolved at runtime from `config('constants.theme')`
  (e.g. `livewire.{theme}.home.home`). Every storefront Livewire component uses this.
- Home page has 6 layout variants (`home`, `homeThemeTwo`…`Six`) selected by the store
  setting `web_home_page_theme` via the `getHomeTheme()` / `getHeaderStyle()` helpers.
- The **header** view is **not** theme-path-prefixed — it lives under
  `resources/views/components/header/` and is chosen by `getHeaderStyle()`.

---

## 2. Summary of changes

| # | Change | File(s) |
|---|---|---|
| 1 | Cloned all 45 storefront views into a new `ghs` theme | `resources/views/livewire/ghs/**` |
| 2 | Switched the active theme to `ghs` | `config/constants.php` |
| 3 | Added the GHS brand CSS layer | `public/frontend/ghs/css/ghs.css` |
| 4 | Linked the brand CSS (only when theme = `ghs`) | `resources/views/components/layouts/app.blade.php` |
| 5 | Built the GHS header & hooked it into `getHeaderStyle()` | `resources/views/components/header/ghs.blade.php`, `app/function_helper.php` |
| 6 | Built the GHS homepage hero, trust badges & membership banner | `resources/views/livewire/ghs/home/home.blade.php` |

---

## 3. Detailed changes

### 3.1 Cloned theme views
Copied `resources/views/livewire/elegant/` → `resources/views/livewire/ghs/` (45 files).
This ensures **every** storefront route resolves after the theme switch — not just the
home page. The cloned views contain no hardcoded `livewire.elegant` paths, so the theme is
self-contained.

### 3.2 Theme switch
`config/constants.php`
```php
// before
'theme' => 'elegant',
// after
'theme' => 'ghs',
```
> To revert to the old storefront, change this back to `'elegant'` and run
> `php artisan config:clear`.

### 3.3 Brand CSS layer
`public/frontend/ghs/css/ghs.css` — new file. Loaded **after** the elegant base CSS, so it
only restyles; it does not replace the framework. Brand tokens:

```
--ghs-green       #21512b   header / brand
--ghs-green-mid   #2e7d32   Shop Now / accents
--ghs-gold        #d99a25   Buy Now pill
--ghs-maroon      #7a1f3d   membership CTA
--ghs-cream       #f7f1e3   soft sections
--ghs-display     Fraunces  (Google Font) — hero headlines, product titles, callouts
--ghs-sans        Outfit    (Google Font) — body, editorial, subheadings (base body font)
```
> Fonts are loaded in `app.blade.php` (guarded for the GHS theme). `Outfit` is set as the
> base `body` font; `Fraunces` is applied to `.ghs-title`, `.ghs-member-title`,
> `.section-header h2`, and `.product-title`.
Styles cover: header (green bar, white nav, gold cart/wishlist counts), the `Buy Now` pill,
header social icons, the hero (background, title, subtitle, Shop Now, category quick-links,
product showcase), trust badges, and the membership card. Responsive rules collapse the
membership card and tighten hero padding on mobile.

### 3.4 Layout link
`resources/views/components/layouts/app.blade.php` — after the elegant `swiper-bundle.min.css`
link, added (guarded so it loads only for the GHS theme):
```blade
@if (config('constants.theme') === 'ghs')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;...&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/ghs/css/ghs.css') }}?v={{ $version }}">
@endif
```

### 3.5 GHS header
`resources/views/components/header/ghs.blade.php` — copied from the elegant header so **all
Livewire wiring is preserved** (search drawer, minicart offcanvas, account dropdown,
wishlist, currency/language pickers, store popup, mobile nav, `<livewire:header.SearchProduct />`,
`<livewire:pages.model-cart />`). Changes applied:

- Root wrapper given the `ghs-header` class (CSS scope).
- Desktop nav replaced with the GHS menu:
  **Home · Our Story · Food · Oils · Women · Men · Combo Deals · Bulk/Gifting Orders · Contact Us**
- Added header **social icons** + gold **Buy Now** pill.
- Grid widths re-balanced: logo `col-lg-2`, menu `col-lg-7`, icons `col-lg-3`.

Hooked into the header resolver in `app/function_helper.php`. Because `ghs` is a dedicated
brand theme, **both** `getHeaderStyle()` and `getHomeTheme()` short-circuit to the GHS views
when the active theme is `ghs` — regardless of the store's `web_home_page_theme` variant
setting (1–6). `elegant` behaviour is unchanged.
```php
// getHeaderStyle()
if (config('constants.theme') === 'ghs') {
    return 'components.header.ghs';
}
// getHomeTheme()
if (config('constants.theme') === 'ghs') {
    return 'livewire.ghs.home.home';
}
```
> **Why:** the home/header variant is driven by the store setting `web_home_page_theme`.
> If that setting is 2–6, the resolver would otherwise return `headerThemeTwo…Six` (elegant
> style) and an unstyled cloned home variant — which looks like "the old theme".

### 3.6 GHS homepage top
`resources/views/livewire/ghs/home/home.blade.php` — inserted three sections **above** the
existing slider/category/product sections (which still render with inherited markup):

1. **Hero** — logo, "Organic Kashmiri Wellness" eyebrow, serif title (`app_name`), subtitle,
   **Shop Now** button, category quick-links (Saffron · Honey · Dry Fruits · Kehwa Tea ·
   Herbs & Spices · Shawls & Handicrafts), and a product showcase image.
2. **Trust badges** — GST No. `07ABFPY0574P1ZW`, MSME Registered `Regd. No. 4480751`,
   Trademark Certified `Regd. No. 223002000934`.
3. **Membership banner** — shown to guests only (`@guest`): "Become a Member" → register,
   "Sign In" → login.

---

## 4. Routing / link map

`customUrl($name)` resolves a **route name** to a store-aware URL. Nav/CTA targets:

| Label | Route name | Status |
|---|---|---|
| Home | `home` | final |
| Our Story | `about_us` | final |
| Food / Oils / Women / Men | `products` | **placeholder** — point to real category slugs later |
| Combo Deals | `combo-products` | final |
| Bulk/Gifting Orders | `contact_us` | **placeholder** — needs a dedicated page |
| Contact Us | `contact_us` | final |
| Shop Now / category quick-links / Buy Now | `products` | placeholder targets |
| Become a Member | `register` | final |
| Sign In | `login` | final |

---

## 5. Pending / TODO

- [ ] **Brand images** — add to `public/frontend/ghs/images/`:
      `hero-bg.jpg` (mountains), `hero-products.png` (jars), `membership.png` (gift box).
      Missing images hide gracefully via `onerror` until supplied.
- [ ] **Real nav targets** — wire Food/Oils/Women/Men to actual category slugs; add a
      Bulk/Gifting Orders page.
- [ ] **Trust badge values** — currently hardcoded from the design; consider moving to
      admin settings.
- [ ] **Remaining sections** — restyle the rest of the home page, product cards,
      category/product pages, and footer (still using inherited elegant markup under the
      green GHS header).

---

## 6. Verification done

- `php -l` clean on `app/function_helper.php` and `config/constants.php`.
- `php artisan view:cache` compiled **all** Blade templates with no errors (validates the
  new header, hero and membership views).
- `php artisan config:clear` + `php artisan view:clear` run.

### Run / preview
```bash
php artisan serve        # then open the homepage
# if anything looks stale:
php artisan config:clear
php artisan view:clear
```

### Revert to the old storefront
Set `'theme' => 'elegant'` in `config/constants.php` and run `php artisan config:clear`.
The `getHeaderStyle()` default automatically falls back to the elegant header.
