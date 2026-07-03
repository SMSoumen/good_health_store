{{--
    GHS reusable product card
    -------------------------
    A brand-styled card used across the GHS storefront (Best Sellers,
    Wellness Bundles, and future product grids).

    Two ways to use it:

    1) Real product (wires a working Add to Cart / Quick View):
         <x-ghs.product-card :product="$product" />
       `$product` is a product row from ProductService::fetchProduct()
       (regular, variable_product or combo-product). Image, title, link,
       price and the cart wiring are all derived from it.

    2) Presentational (static cards, e.g. bundles/mockups):
         <x-ghs.product-card image="..." title="..." price="₹999"
                             href="..." cta="Shop Now" />

    Props:
      product  : real product object/array (mode 1). When set, the props
                 below are derived automatically unless explicitly overridden.
      image    : image URL
      title    : product / bundle name
      href     : link target (defaults to '#')
      price    : current price, pre-formatted (e.g. "₹499")
      oldPrice : optional strikethrough price
      desc     : optional short description (used by bundles)
      badge    : optional ribbon label (e.g. "Hot Deal")
      cta      : button label (e.g. "Add to Cart" / "Shop Now")
      ctaIcon  : ion-icon name for the button
      linkOnly : with a real product, render the CTA as a plain link to the
                 product/combo page instead of an inline add-to-cart (used by
                 bundles whose CTA is "Shop Now").
--}}
@props([
    'product' => null,
    'image' => null,
    'title' => '',
    'href' => '#',
    'price' => null,
    'oldPrice' => null,
    'desc' => null,
    'badge' => null,
    'cta' => null,
    'ctaIcon' => 'bag-handle-outline',
    'linkOnly' => false,
])
@php
    use App\Services\MediaService;
    use App\Services\CurrencyService;

    $p = $product ? (object) $product : null;
    $isCombo = false;
    $isVariable = false;

    if ($p) {
        $isCombo = ($p->type ?? '') === 'combo-product';
        $isVariable = ($p->type ?? '') === 'variable_product';

        $image = $image ?? app(MediaService::class)->dynamic_image($p->image, 400);
        $title = $title !== '' ? $title : $p->name;
        $href = $href !== '#'
            ? $href
            : ($isCombo ? customUrl('combo-products/' . $p->slug) : customUrl('products/' . $p->slug));

        // pricing — special price wins when present
        if ($isVariable) {
            $base = app(CurrencyService::class)->currentCurrencyPrice($p->min_max_price['max_price'], true);
            $sp = $p->min_max_price['special_min_price'] ?? 0;
            $special = $sp > 0 ? app(CurrencyService::class)->currentCurrencyPrice($sp, true) : $base;
        } elseif ($isCombo) {
            $base = app(CurrencyService::class)->currentCurrencyPrice($p->price, true);
            $special = (!empty($p->special_price) && $p->special_price > 0)
                ? app(CurrencyService::class)->currentCurrencyPrice($p->special_price, true)
                : $base;
        } else {
            $base = app(CurrencyService::class)->currentCurrencyPrice($p->variants[0]['price'], true);
            $sp = $p->variants[0]['special_price'] ?? 0;
            $special = $sp > 0 ? app(CurrencyService::class)->currentCurrencyPrice($sp, true) : $base;
        }
        $price = $special;
        $oldPrice = $special !== $base ? $base : null;

        $cta = $cta ?? labels('front_messages.add_to_cart', 'Add to Cart');
    }
@endphp

<div {{ $attributes->merge(['class' => 'ghs-product-card']) }}>
    @if (!empty($badge))
        <span class="ghs-pc-badge">{{ $badge }}</span>
    @endif

    <a wire:navigate href="{{ $href }}" class="ghs-pc-media">
        <img src="{{ $image }}" alt="{{ $title }}" loading="lazy"
            onerror="this.style.display='none'">
    </a>

    <div class="ghs-pc-body">
        <h3 class="ghs-pc-title">
            <a wire:navigate href="{{ $href }}">{{ $title }}</a>
        </h3>

        @if (!empty($desc))
            <p class="ghs-pc-desc">{{ $desc }}</p>
        @endif

        <div class="ghs-pc-foot">
            @if (!empty($price))
                <div class="ghs-pc-price">
                    @if (!empty($oldPrice))
                        <span class="ghs-pc-old">{{ $oldPrice }}</span>
                    @endif
                    <span class="ghs-pc-new">{{ $price }}</span>
                </div>
            @endif

            @if ($p && !$linkOnly && $isVariable)
                {{-- variable product: choose a variant via Quick View --}}
                <a href="#quickview-modal" class="ghs-pc-cta quickview quick-view-modal"
                    data-bs-toggle="modal" data-bs-target="#quickview_modal"
                    data-product-id="{{ $p->id }}" data-product-variant-id=''>
                    <ion-icon name="{{ $ctaIcon }}"></ion-icon>
                    <span>{{ $cta }}</span>
                </a>
            @elseif ($p && !$linkOnly)
                {{-- regular / combo product: direct add to cart (wired by custom.js .add_cart) --}}
                <div class="ghs-pc-cart add_cart"
                    data-product-variant-id="{{ $isCombo ? $p->id : $p->variants[0]['id'] }}"
                    data-name='{{ $p->name }}' data-slug='{{ $p->slug }}'
                    data-image='{{ app(MediaService::class)->dynamic_image($p->image, 220) }}'
                    data-product-type='{{ $isCombo ? 'combo' : 'regular' }}'
                    data-max='{{ $p->total_allowed_quantity }}'
                    data-step='{{ $p->quantity_step_size }}'
                    data-min='{{ $p->minimum_order_quantity }}'
                    data-stock-type='{{ $p->stock_type }}' data-store-id='{{ $p->store_id }}'
                    data-variant-price="{{ app(CurrencyService::class)->currentCurrencyPrice($isCombo ? $p->special_price : $p->variants[0]['special_price']) }}">
                    <a class="ghs-pc-cta" data-product-id="{{ $p->id }}">
                        <ion-icon name="{{ $ctaIcon }}"></ion-icon>
                        <span>{{ $cta }}</span>
                    </a>
                </div>
            @elseif (!empty($cta))
                {{-- presentational card (bundles / mockups) --}}
                <a wire:navigate href="{{ $href }}" class="ghs-pc-cta">
                    <ion-icon name="{{ $ctaIcon }}"></ion-icon>
                    <span>{{ $cta }}</span>
                </a>
            @endif
        </div>
    </div>
</div>
