@php
    use App\Services\StoreService;
    use App\Services\TranslationService;

    $store_settings = app(StoreService::class)->getStoreSettings();
    $language_code = app(TranslationService::class)->getLanguageCode();
    $category_section_title = $store_settings['category_section_title'] ?? 'Categories';
@endphp

<style>
    #page-content h2,
    #page-content h4,
    #page-content p,
    #page-content .ctg-title,
    #page-content .ctg-des,
    #page-content .category-title,
    #page-content .btn,
    #page-content .button-text,
    #page-content a {
        line-height: 1;
    }
</style>

<div id="page-content" class="index-demo1" wire:ignore>

    {{-- ============================= GHS HERO ============================= --}}
    <section class="ghs-hero"
        style="background-image:url('{{ asset('frontend/ghs/images/hero-bg.jpg') }}')">
        {{-- Hero logo hidden per request
        @if (!empty($web_settings['logo']))
            <img class="ghs-hero-logo" src="{{ asset('storage/' . $web_settings['logo']) }}"
                alt="{{ $web_settings['app_name'] ?? 'Good Health Store' }}"
                onerror="this.style.display='none'">
        @endif
        --}}
        {{-- Hero eyebrow text hidden per request
        <p class="ghs-eyebrow">{{ labels('front_messages.organic_kashmiri_wellness', 'Organic Kashmiri Wellness') }}</p>
        --}}
        <h1 class="ghs-title">{{ $web_settings['app_name'] ?? 'GOOD HEALTH STORE' }}</h1>
        <p class="ghs-subtitle">{{ labels('front_messages.organic_kashmiri_wellness', 'Organic Kashmiri Wellness') }}</p>

        <a wire:navigate href="{{ customUrl('products') }}"
            class="ghs-shop-now">{{ labels('front_messages.shop_now', 'Shop Now') }}</a>

        {{-- category quick-links --}}
        <ul class="ghs-hero-cats">
            <li><a wire:navigate href="{{ customUrl('products') }}">{{ labels('front_messages.saffron', 'Saffron') }}</a></li>
            <li><a wire:navigate href="{{ customUrl('products') }}">{{ labels('front_messages.honey', 'Honey') }}</a></li>
            <li><a wire:navigate href="{{ customUrl('products') }}">{{ labels('front_messages.dry_fruits', 'Dry Fruits') }}</a></li>
            <li><a wire:navigate href="{{ customUrl('products') }}">{{ labels('front_messages.kehwa_tea', 'Kehwa Tea') }}</a></li>
            <li><a wire:navigate href="{{ customUrl('products') }}">{{ labels('front_messages.herbs_spices', 'Herbs & Spices') }}</a></li>
            <li><a wire:navigate href="{{ customUrl('products') }}">{{ labels('front_messages.shawls_handicrafts', 'Shawls & Handicrafts') }}</a></li>
        </ul>

        {{-- product showcase image (brand asset) --}}
        <div class="ghs-hero-showcase">
            <img src="{{ asset('frontend/ghs/images/hero-products.png') }}" alt="Good Health Store products"
                onerror="this.style.display='none'">
        </div>
    </section>

    {{-- ========================= GHS TRUST BADGES ======================== --}}
    <section class="ghs-trust">
        <div class="container">
            <div class="row">
                <div class="col-6 col-md-4 mb-2 mb-md-0">
                    <div class="ghs-trust-item">
                        <ion-icon class="ghs-trust-ico" name="ribbon-outline"></ion-icon>
                        <div>
                            <div class="ghs-trust-label">{{ labels('front_messages.gst_no', 'GST No.') }}</div>
                            <div class="ghs-trust-sub">07ABFPY0574P1ZW</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 mb-2 mb-md-0">
                    <div class="ghs-trust-item">
                        <ion-icon class="ghs-trust-ico" name="shield-checkmark-outline"></ion-icon>
                        <div>
                            <div class="ghs-trust-label">{{ labels('front_messages.msme_registered', 'MSME Registered') }}</div>
                            <div class="ghs-trust-sub">Regd. No. 4480751</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="ghs-trust-item">
                        <ion-icon class="ghs-trust-ico" name="checkmark-circle-outline"></ion-icon>
                        <div>
                            <div class="ghs-trust-label">{{ labels('front_messages.trademark_certified', 'Trademark Certified') }}</div>
                            <div class="ghs-trust-sub">Regd. No. 223002000934</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= GHS MEMBERSHIP BANNER ===================== --}}
    @guest
        <section class="ghs-membership">
            <div class="ghs-member-card">
                <div class="container ghs-member-inner">
                    <div class="ghs-member-body">
                        <h3 class="ghs-member-title">
                            {{ labels('front_messages.membership_title', 'Enjoy benefits & rewards with') }}
                            {{ $web_settings['app_name'] ?? 'Good Health Store' }}
                        </h3>
                        <p class="ghs-member-text">
                            {{ labels('front_messages.membership_text', 'Join the family and reap the rewards!') }}
                        </p>
                        <div>
                            <a wire:navigate href="{{ customUrl('register') }}"
                                class="ghs-btn-member">{{ labels('front_messages.become_a_member', 'Become a Member') }}</a>
                            <a wire:navigate href="{{ customUrl('login') }}"
                                class="ghs-btn-signin">{{ labels('front_messages.sign_in', 'Sign In') }}</a>
                        </div>
                    </div>
                    <div class="ghs-member-img">
                        <img src="{{ asset('frontend/ghs/images/membership.png') }}" alt="Membership rewards"
                            onerror="this.style.display='none'">
                    </div>
                </div>
            </div>
        </section>
    @endguest

    {{-- ====================== GHS NATURAL QUALITIES ===================== --}}
    <section class="ghs-qualities"
        style="background-image:url('{{ asset('frontend/ghs/images/qualities-bg.jpg') }}')">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6 col-md-3 mb-4 mb-md-0">
                    <div class="ghs-quality-item">
                        <img class="ghs-quality-ico" src="{{ asset('frontend/ghs/images/quality-natural.png') }}"
                            alt="{{ labels('front_messages.hundred_natural', '100% Natural') }}"
                            onerror="this.style.display='none'">
                        <h4 class="ghs-quality-title">{{ labels('front_messages.hundred_natural', '100% Natural') }}</h4>
                        <span class="ghs-quality-check">
                            <ion-icon name="checkmark-circle"></ion-icon>
                            {{ labels('front_messages.hundred_natural', '100% Natural') }}
                        </span>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-4 mb-md-0">
                    <div class="ghs-quality-item">
                        <img class="ghs-quality-ico" src="{{ asset('frontend/ghs/images/quality-no-flavours.png') }}"
                            alt="{{ labels('front_messages.no_added_flavours', 'No Added Flavours') }}"
                            onerror="this.style.display='none'">
                        <h4 class="ghs-quality-title">{{ labels('front_messages.no_added_flavours', 'No Added Flavours') }}</h4>
                        <span class="ghs-quality-check">
                            <ion-icon name="checkmark-circle"></ion-icon>
                            {{ labels('front_messages.no_added_flavours', 'No Added Flavours') }}
                        </span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="ghs-quality-item">
                        <img class="ghs-quality-ico" src="{{ asset('frontend/ghs/images/quality-no-processing.png') }}"
                            alt="{{ labels('front_messages.no_unnecessary_processing', 'No Unnecessary Processing') }}"
                            onerror="this.style.display='none'">
                        <h4 class="ghs-quality-title">{{ labels('front_messages.no_unnecessary_processing', 'No Unnecessary Processing') }}</h4>
                        <span class="ghs-quality-check">
                            <ion-icon name="checkmark-circle"></ion-icon>
                            {{ labels('front_messages.no_unnecessary_processing', 'No Unnecessary Processing') }}
                        </span>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="ghs-quality-item">
                        <img class="ghs-quality-ico" src="{{ asset('frontend/ghs/images/quality-energizing.png') }}"
                            alt="{{ labels('front_messages.naturally_energizing', 'Naturally Energizing') }}"
                            onerror="this.style.display='none'">
                        <h4 class="ghs-quality-title">{{ labels('front_messages.naturally_energizing', 'Naturally Energizing') }}</h4>
                        <span class="ghs-quality-check">
                            <ion-icon name="checkmark-circle"></ion-icon>
                            {{ labels('front_messages.naturally_energizing', 'Naturally Energizing') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ====================== MISSION / VISION / PROGRESS ===================== --}}
    <section class="ghs-mvp">
        <div class="container">
            <div class="row g-4">
                {{-- Mission --}}
                <div class="col-12 col-md-4">
                    <div class="ghs-mvp-card">
                        <div class="ghs-mvp-head">
                            <span class="ghs-mvp-ico ghs-mvp-ico--mission">
                                <ion-icon name="leaf"></ion-icon>
                            </span>
                            <h3 class="ghs-mvp-title">{{ labels('front_messages.mission_title', 'Mission') }}</h3>
                        </div>
                        <ul class="ghs-mvp-list">
                            <li>
                                <ion-icon name="checkmark-circle"></ion-icon>
                                <span>{{ labels('front_messages.mission_point_1', 'Deliver 100% authentic Kashmiri products without alteration') }}</span>
                            </li>
                            <li>
                                <ion-icon name="checkmark-circle"></ion-icon>
                                <span>{{ labels('front_messages.mission_point_2', 'Ensure fair pricing & support for farmers and savers') }}</span>
                            </li>
                            <li>
                                <ion-icon name="checkmark-circle"></ion-icon>
                                <span>{{ labels('front_messages.mission_point_3', 'Reduce middlemen to maintain purity, trust & transparency') }}</span>
                            </li>
                            <li>
                                <ion-icon name="checkmark-circle"></ion-icon>
                                <span>{{ labels('front_messages.mission_point_4', 'Make natural wellness accessible to every household') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Vision --}}
                <div class="col-12 col-md-4">
                    <div class="ghs-mvp-card">
                        <div class="ghs-mvp-head">
                            <span class="ghs-mvp-ico ghs-mvp-ico--vision">
                                <ion-icon name="telescope"></ion-icon>
                            </span>
                            <h3 class="ghs-mvp-title">{{ labels('front_messages.vision_title', 'Vision') }}</h3>
                        </div>
                        <ul class="ghs-mvp-list">
                            <li>
                                <ion-icon name="checkmark-circle"></ion-icon>
                                <span>{{ labels('front_messages.vision_point_1', 'Become a trusted household name across India') }}</span>
                            </li>
                            <li>
                                <ion-icon name="checkmark-circle"></ion-icon>
                                <span>{{ labels('front_messages.vision_point_2', "Be part of every family's lifestyle, kitchen & daily routine") }}</span>
                            </li>
                            <li>
                                <ion-icon name="checkmark-circle"></ion-icon>
                                <span>{{ labels('front_messages.vision_point_3', "Promote Kashmir's heritage and traditional products globally") }}</span>
                            </li>
                            <li>
                                <ion-icon name="checkmark-circle"></ion-icon>
                                <span>{{ labels('front_messages.vision_point_4', 'Empower farmers by ensuring purity, quality & authenticity') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Progress --}}
                <div class="col-12 col-md-4">
                    <div class="ghs-mvp-card">
                        <div class="ghs-mvp-head">
                            <span class="ghs-mvp-ico ghs-mvp-ico--progress">
                                <ion-icon name="bar-chart"></ion-icon>
                            </span>
                            <h3 class="ghs-mvp-title">{{ labels('front_messages.progress_title', 'Progress') }}</h3>
                        </div>
                        <ul class="ghs-mvp-list">
                            <li>
                                <ion-icon name="checkmark-circle"></ion-icon>
                                <span>{{ labels('front_messages.progress_point_1', '1500+ Happy Customers') }}</span>
                            </li>
                            <li>
                                <ion-icon name="checkmark-circle"></ion-icon>
                                <span>{{ labels('front_messages.progress_point_2', "10% of business profits go towards supporting an NGO that works in children's education") }}</span>
                            </li>
                            <li>
                                <ion-icon name="checkmark-circle"></ion-icon>
                                <span>{{ labels('front_messages.progress_point_3', 'Growing Product Range & Community') }}</span>
                            </li>
                            <li>
                                <ion-icon name="checkmark-circle"></ion-icon>
                                <span>{{ labels('front_messages.progress_point_4', 'We aim to expand our reach & customer satisfaction') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ====================== FROM THE HEART OF KASHMIR (TESTIMONIALS) ===================== --}}
    {{-- $ghs_testimonials is built in App\Livewire\Home::ghsTestimonials():
         highest-rated approved real reviews first, seed testimonials filling up to 8. --}}
    @if (!empty($ghs_testimonials))
    <section class="ghs-heart"
        style="background-image:url('{{ asset('frontend/ghs/images/kashmir-bg.jpg') }}')">
        <div class="ghs-heart-overlay"></div>
        <div class="container">
            <div class="ghs-heart-head text-center">
                <h2 class="ghs-heart-title">{{ labels('front_messages.heart_of_kashmir_title', 'From the Heart of Kashmir') }}</h2>
                <p class="ghs-heart-sub">{{ labels('front_messages.heart_of_kashmir_text', 'Bringing you the finest, authentic products from the valleys of Kashmir') }}</p>
            </div>
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-4">
                    <div class="ghs-heart-figure">
                        <img src="{{ asset('frontend/ghs/images/kashmir-farmer.jpg') }}"
                            alt="{{ labels('front_messages.heart_of_kashmir_title', 'From the Heart of Kashmir') }}"
                            onerror="this.style.display='none'">
                    </div>
                </div>
                <div class="col-12 col-lg-8">
                    <div class="swiper ghs-testimonials-swiper">
                        <div class="swiper-wrapper">
                            @foreach ($ghs_testimonials as $tm)
                                <div class="swiper-slide">
                                    <div class="ghs-tm-card">
                                        <div class="ghs-tm-top">
                                            <span class="ghs-tm-avatar">
                                                <img src="{{ $tm['avatar'] }}" alt="{{ $tm['name'] }}"
                                                    onerror="this.style.display='none'">
                                            </span>
                                            <div class="ghs-tm-meta">
                                                <h5 class="ghs-tm-name">{{ $tm['name'] }}</h5>
                                                @php $tm_rating = (int) ($tm['rating'] ?? 5); @endphp
                                                <span class="ghs-tm-stars">
                                                    @for ($s = 1; $s <= 5; $s++)
                                                        <ion-icon name="{{ $s <= $tm_rating ? 'star' : 'star-outline' }}"></ion-icon>
                                                    @endfor
                                                </span>
                                            </div>
                                        </div>
                                        <p class="ghs-tm-text">{{ $tm['text'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination ghs-tm-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ====================== BEST SELLERS & WELLNESS BUNDLES ===================== --}}
    @if ((!empty($best_sellers) && count($best_sellers)) || (!empty($wellness_bundles) && count($wellness_bundles)))
        <section class="ghs-bestsellers">
            <div class="container">
                {{-- Best Sellers (real products) --}}
                @if (!empty($best_sellers) && count($best_sellers))
                    <div class="ghs-bs-head text-center">
                        <h2 class="ghs-bs-title">{{ labels('front_messages.best_sellers', 'Best Sellers') }}</h2>
                    </div>
                    <div class="row g-3 g-md-4">
                        @foreach ($best_sellers as $product)
                            <div class="col-6 col-lg-3">
                                <x-ghs.product-card :product="$product" />
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Wellness Bundles (real combo products) --}}
                @if (!empty($wellness_bundles) && count($wellness_bundles))
                    <div class="ghs-bs-head text-center {{ !empty($best_sellers) && count($best_sellers) ? 'mt-5' : '' }}">
                        <h2 class="ghs-bs-title">{{ labels('front_messages.wellness_bundles', 'Wellness Bundles') }}</h2>
                        <p class="ghs-bs-sub">{{ labels('front_messages.wellness_bundles_sub', 'Curated combinations for your complete well-being') }}</p>
                    </div>
                    <div class="row g-3 g-md-4">
                        @foreach ($wellness_bundles as $bundle)
                            @php
                                // Top-right badge = the combo's first tag (if any).
                                // `tags` may be a comma-separated string or an array (model cast).
                                $bundle_badge = null;
                                $bundle_tags = $bundle->tags ?? null;
                                if (is_string($bundle_tags)) {
                                    $bundle_tags = explode(',', $bundle_tags);
                                }
                                if (!empty($bundle_tags) && is_array($bundle_tags)) {
                                    $bundle_tags = array_values(array_filter(array_map('trim', $bundle_tags)));
                                    $bundle_badge = $bundle_tags[0] ?? null;
                                }
                            @endphp
                            <div class="col-12 col-md-4">
                                <x-ghs.product-card :product="$bundle" :linkOnly="true" :badge="$bundle_badge"
                                    :desc="\Illuminate\Support\Str::limit(strip_tags($bundle->short_description ?? ''), 110)"
                                    :cta="labels('front_messages.shop_now', 'Shop Now')" ctaIcon="arrow-forward-outline" />
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- SLIDER (hidden — kept in code but not rendered/loaded; set to true to re-enable) --}}
    @if (false)
    <section class="slideshow slideshow-wrapper slideshow-medium">
        <div class="swiper mySwiper home-mySwiper">
            <div class="swiper-wrapper">
                @foreach ($sliders as $slider)
                    <div class="swiper-slide slideshow-wrap">
                        @if ($slider['type'] !== 'default')
                            <a href="{{ $slider['link'] }}" @if ($slider['type'] !== 'slider_url') wire:navigate @endif
                                target="{{ $slider['type'] == 'slider_url' ? '_blank' : '_self' }}" class="slider-link">
                                <img class="rounded-4 blur-up lazyload" src="{{ $slider['image'] }}"
                                    data-src="{{ $slider['image'] }}" alt="slider">
                            </a>
                        @else
                            <img class="rounded-4 blur-up lazyload" src="{{ $slider['image'] }}"
                                data-src="{{ $slider['image'] }}" alt="slider">
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section>
    @endif

    {{-- POPULAR CATEGORIES (hidden — kept in code but not rendered; set to true to re-enable) --}}
    @php $categories = $categories['categories'] ?? []; @endphp
    @if (false && count($categories))
        <section class="section collection-slider">
            <div class="container-fluid skinned">
                <div class="section-header style2 d-flex justify-content-between">
                    <div>
                        <h2>
                            {{ is_array($category_section_title)
                                ? $category_section_title[$language_code] ?? ($category_section_title['en'] ?? reset($category_section_title))
                                : $category_section_title }}
                        </h2>
                        <p>{{ labels('front_messages.explore_categories', 'Explore top picks in our Categories!') }}</p>
                    </div>
                    <a wire:navigate href="{{ customUrl('categories') }}" class="view_more_icon">
                        <i class="anm anm-arrow-alt-right"></i>
                    </a>
                </div>

                <x-utility.categories.sliders.sliderThree :categories="$categories" />
            </div>
        </section>
    @endif

    {{-- CATEGORY SECTIONS --}}
    @if (!empty($categories_section))
        @foreach ($categories_section as $category_section)
            <section class="section collection-slider">
                <div class="container-fluid">
                    <div class="section-header style2 d-flex justify-content-between">
                        <div>
                            <h2>{{ $category_section->title }}</h2>
                            <p>{{ labels('front_messages.explore_categories', 'Explore top picks in our Categories!') }}
                            </p>
                        </div>
                        <a wire:navigate href="{{ customUrl('categories') }}" class="view_more_icon">
                            <i class="anm anm-arrow-alt-right"></i>
                        </a>
                    </div>

                    <x-utility.categories.sliders.sliderThree :categories="$category_section->categories_detail" />
                </div>
            </section>
        @endforeach
    @endif

    {{-- BRANDS --}}
    @if(2<1)
        @if (!empty($brands['brands']))
            <section class="section collection-slider">
                <div class="container-fluid">
                    <div class="section-header style2 d-flex justify-content-between">
                        <div>
                            <h2>{{ labels('front_messages.popular_brands', 'Popular Brands') }}</h2>
                            <p>{{ labels('front_messages.explore_brands', 'Explore top picks in our Brands!') }}</p>
                        </div>
                        <a wire:navigate href="{{ customUrl('brands') }}" class="view_more_icon">
                            <i class="anm anm-arrow-alt-right"></i>
                        </a>
                    </div>

                    <div class="swiper category-mySwiper">
                        <div class="swiper-wrapper">
                            @foreach ($brands['brands'] as $brand)
                                <div class="swiper-slide slider-brand rounded-4">
                                    <a wire:navigate href="{{ customUrl('products/?brand=' . $brand['brand_slug']) }}"
                                        class="brand-box">
                                        <img class="blur-up lazyload" src="{{ $brand['brand_img'] }}"
                                            data-src="{{ $brand['brand_img'] }}" alt="{{ $brand['brand_name'] }}">
                                        @if (($store_settings['brand_style'] ?? '') === 'brands_style_1')
                                            <h4 class="text-center">{{ $brand['brand_name'] }}</h4>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endif


    {{-- PRODUCT SECTIONS --}}
    @foreach ($sections as $row)
        @if (!empty($row->product_details))
            {{-- STYLE 1 --}}
            @if ($row->style === 'style_1')
                <section class="section product-slider">
                    <div class="container-fluid">
                        <x-utility.section_header.sectionHeaderOne :title="$row" />
                        <div class="swiper style1-mySwiper">
                            <div class="swiper-wrapper">
                                @foreach ($row->product_details as $details)
                                    <div class="swiper-slide">
                                        <x-dynamic-component :component="getProductDisplayComponent($store_settings)" :details="(object) $details" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            {{-- STYLE 3 --}}
            @if ($row->style === 'style_3')
                <section class="section product-banner-slider">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-3">
                                <img class="w-100 rounded" src="{{ $row->banner_image }}" alt="{{ $row->title }}">
                            </div>
                            <div class="col-lg-9">
                                <div class="swiper style2-mySwiper">
                                    <div class="swiper-wrapper">
                                        @foreach ($row->product_details as $details)
                                            <div class="swiper-slide">
                                                <x-dynamic-component :component="getProductDisplayComponent($store_settings)" :details="(object) $details" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        @endif
    @endforeach

    {{-- SERVICES --}}
    @if (
        $web_settings['support_mode'] ||
            $web_settings['shipping_mode'] ||
            $web_settings['safety_security_mode'] ||
            $web_settings['return_mode']
    )
        <section class="section service-section">
            <x-utility.others.serviceSection />
        </section>
    @endif

    {{-- ============================= GHS ENTRY POPUP ============================= --}}
    {{-- First-order discount welcome modal. Reusable on other pages later. --}}
    <x-ghs.entry-popup offer="10% OFF" :appName="$web_settings['app_name'] ?? 'Good Health Store'"
        :logo="$web_settings['logo'] ?? null" />

</div>
