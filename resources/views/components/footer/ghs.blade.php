@php
    use App\Services\CartService;
    $user_id = auth()->id() ?? 0;
    $store_id = session('store_id') ?? '';
    $favorites = getFavorites(user_id: $user_id, store_id: $store_id);
    $cart_count = app(CartService::class)->getCartCount($user_id, $store_id);
    $ghs_logo = !empty($settings->logo) ? asset('storage/' . $settings->logo) : '';
    // WhatsApp link derived from the support number (digits only).
    $ghs_whatsapp = !empty($settings->support_number)
        ? 'https://wa.me/' . preg_replace('/\D+/', '', $settings->support_number)
        : '';
@endphp
<div class="footer ghs-footer mt-5">

    {{-- ===================== GHS FOOTER MAIN (columns + mailing band merged) ===================== --}}
    <div class="ghs-footer-main">
        {{-- decorative collages on both sides. Drop your custom images into
             public/frontend/ghs/images/ (footer-products-left.png / footer-products.png);
             each hides itself if the file is absent. They span the full merged
             section height, so they can run much taller than before. --}}
        <img class="ghs-foot-products ghs-foot-products-left"
            src="{{ asset('frontend/ghs/images/footer-products-left.png') }}" alt="" onerror="this.remove()">
        <img class="ghs-foot-products ghs-foot-products-right"
            src="{{ asset('frontend/ghs/images/footer-products.png') }}" alt="" onerror="this.remove()">

        <div class="container-fluid">
            <div class="row gy-4">

                {{-- Brand --}}
                <div class="col-12 col-lg-3 ghs-foot-brand text-center">
                    <a wire:navigate href="{{ customUrl('home') }}" class="ghs-foot-logo">
                        @if ($ghs_logo)
                            <img src="{{ $ghs_logo }}" alt="{{ $settings->app_name ?? 'Organic Kashmiri Wellness' }}"
                                onerror="this.style.display='none'">
                        @endif
                    </a>
                    <div class="ghs-foot-brandname">{{ $settings->app_name ?? 'Organic Kashmiri Wellness' }}</div>
                    <span class="ghs-foot-flourish">&#10047;</span>
                </div>

                {{-- Contact Us --}}
                <div class="col-12 col-lg-3 ghs-foot-col ghs-foot-contact">
                    <h4 class="ghs-foot-title">{{ labels('front_messages.contact_us', 'Contact Us') }}</h4>
                    @if (!empty($settings->support_number))
                        <p class="ghs-foot-contact-line">
                            <ion-icon name="call-outline"></ion-icon>
                            <a href="tel:{{ $settings->support_number }}">{{ $settings->support_number }}</a>
                        </p>
                    @endif
                    @if (!empty($settings->support_email))
                        <p class="ghs-foot-contact-line">
                            <ion-icon name="mail-outline"></ion-icon>
                            <a href="mailto:{{ $settings->support_email }}">{{ $settings->support_email }}</a>
                        </p>
                    @endif

                    <ul class="ghs-foot-social list-unstyled">
                        @if (!empty($settings->instagram_link))
                            <li><a href="{{ $settings->instagram_link }}" target="_blank" title="Instagram"><i
                                        class="anm anm-instagram"></i></a></li>
                        @endif
                        @if (!empty($settings->facebook_link))
                            <li><a href="{{ $settings->facebook_link }}" target="_blank" title="Facebook"><i
                                        class="anm anm-facebook"></i></a></li>
                        @endif
                        @if ($ghs_whatsapp)
                            <li><a href="{{ $ghs_whatsapp }}" target="_blank" title="WhatsApp"><i
                                        class="anm anm-whatsapp"></i></a></li>
                        @endif
                        @if (!empty($settings->youtube_link))
                            <li><a href="{{ $settings->youtube_link }}" target="_blank" title="Youtube"><i
                                        class="anm anm-youtube"></i></a></li>
                        @endif
                    </ul>
                </div>

                {{-- Quick Links --}}
                <div class="col-6 col-lg-3 ghs-foot-col ghs-foot-links">
                    <h4 class="ghs-foot-title">{{ labels('front_messages.quick_links', 'Quick Links') }}</h4>
                    <ul class="list-unstyled">
                        <li><a wire:navigate
                                href="{{ customUrl('about_us') }}">{{ labels('front_messages.about_us', 'About Us') }}</a>
                        </li>
                        <li><a wire:navigate
                                href="{{ customUrl('bulk_gifting_orders') }}">{{ labels('front_messages.bulk_gifting_orders', 'Bulk Gifting Orders') }}</a>
                        </li>
                        <li><a wire:navigate
                                href="{{ customUrl('blogs') }}">{{ labels('front_messages.blog', 'Blog') }}</a></li>
                        <li><a wire:navigate
                                href="{{ customUrl('products') }}">{{ labels('front_messages.product_range', 'Product Range') }}</a>
                        </li>
                        <li><a wire:navigate
                                href="{{ customUrl('faqs') }}">{{ labels('front_messages.faqs', 'FAQs') }}</a></li>
                        <li><a wire:navigate
                                href="{{ customUrl('orders') }}">{{ labels('front_messages.track_order', 'Track Order') }}</a>
                        </li>
                        <li><a wire:navigate
                                href="{{ customUrl('shipping_policy') }}">{{ labels('front_messages.shipping_info', 'Shipping Info') }}</a>
                        </li>
                        <li><a wire:navigate
                                href="{{ customUrl('return_policy') }}">{{ labels('front_messages.return_refund_policy', 'Return and Refund Policy') }}</a>
                        </li>
                    </ul>
                </div>

                {{-- Shop by Category --}}
                <div class="col-6 col-lg-3 ghs-foot-col ghs-foot-links">
                    <h4 class="ghs-foot-title">{{ labels('front_messages.shop_by_category', 'Shop by Category') }}</h4>
                    <ul class="list-unstyled">
                        <li><a wire:navigate
                                href="{{ customUrl('home') }}">{{ labels('front_messages.home', 'Home') }}</a></li>
                        <li><a wire:navigate
                                href="{{ customUrl('products') }}">{{ labels('front_messages.tea', 'Tea') }}</a></li>
                        <li><a wire:navigate
                                href="{{ customUrl('products') }}">{{ labels('front_messages.saffron', 'Saffron') }}</a>
                        </li>
                        <li><a wire:navigate
                                href="{{ customUrl('products') }}">{{ labels('front_messages.dry_fruits', 'Dry Fruits') }}</a>
                        </li>
                        <li><a wire:navigate
                                href="{{ customUrl('products') }}">{{ labels('front_messages.other_products', 'Other Products') }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- mailing list + payments — merged into the main section so the side
                 images can span the full (taller) footer height. NOTE: the form is
                 presentational only; wire the submit to a Livewire/store action once
                 a subscriber model exists. --}}
            <div class="ghs-footer-news">
                <div class="ghs-news-row">
                    {{-- FSSAI badge to the left of the mailing card. Drop your image at
                         public/frontend/ghs/images/fssai.png; hides itself if absent. --}}
                    <img class="ghs-news-fssai" src="{{ asset('frontend/ghs/images/fssai.png') }}"
                        alt="FSSAI Registered" onerror="this.remove()">

                    <div class="ghs-news-inner">
                        <div class="ghs-news-copy">
                            <h3 class="ghs-news-title">{{ labels('front_messages.join_our_mailing_list', 'Join Our Mailing List') }}</h3>
                            <p class="ghs-news-sub">{{ labels('front_messages.mailing_list_sub', 'Subscribe for latest updates, offers & wellness tips!') }}</p>
                        </div>
                        <form class="ghs-news-form" wire:submit.prevent="subscribe">
                            <input type="email" name="newsletter_email" class="ghs-news-input"
                                wire:model="newsletter_email"
                                placeholder="{{ labels('front_messages.enter_your_email', 'Enter your email') }}"
                                aria-label="{{ labels('front_messages.enter_your_email', 'Enter your email') }}">
                            <button type="submit" class="ghs-news-btn" wire:loading.attr="disabled"
                                wire:target="subscribe">
                                <span wire:loading.remove wire:target="subscribe">
                                    {{ labels('front_messages.subscribe', 'Subscribe') }}
                                </span>
                                <span wire:loading wire:target="subscribe">
                                    {{ labels('front_messages.subscribing', 'Subscribing...') }}
                                </span>
                                <ion-icon name="chevron-forward-outline"></ion-icon>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== GHS FOOTER BOTTOM ===================== --}}
    <div class="ghs-footer-bottom">
        <div class="container-fluid">
            <div class="ghs-bottom-inner">
                <div class="ghs-bottom-copy">
                    {!! preg_replace('/\b\d{4}\b/', date('Y'), $settings->copyright_details ?? '') !!}
                </div>

                {{-- Custom payment icons (right end). Drop your images into
                     public/frontend/ghs/images/payments/ (visa.png, mastercard.png,
                     rupay.png, upi.png); each removes itself if the file is absent. --}}
                <ul class="ghs-payment-icons list-unstyled">
                    <li><img class="ghs-pay-img" src="{{ asset('frontend/ghs/images/payments/visa.png') }}"
                            alt="Visa" onerror="this.parentElement.remove()"></li>
                    <li><img class="ghs-pay-img" src="{{ asset('frontend/ghs/images/payments/mastercard.png') }}"
                            alt="Mastercard" onerror="this.parentElement.remove()"></li>
                    <li><img class="ghs-pay-img" src="{{ asset('frontend/ghs/images/payments/rupay.png') }}"
                            alt="RuPay" onerror="this.parentElement.remove()"></li>
                    <li><img class="ghs-pay-img" src="{{ asset('frontend/ghs/images/payments/upi.png') }}"
                            alt="UPI" onerror="this.parentElement.remove()"></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ===================== PRESERVED FUNCTIONAL BLOCKS ===================== --}}
    @if (config('constants.ALLOW_MODIFICATION') == 0)
        <a target="blank"
            href="https://codecanyon.net/item/eshop-plus-multi-vendor-ecommerce-multi-module-website-in-laravel/56605998"
            class="buy_now_button position-fixed bottom-0 m-3 z-3 text-decoration-none">
            <div class="btn btn-primary d-flex align-items-center gap-1 mb-4 rounded-pill px-3 py-2">
                <i class="anm anm-cart-plus"></i>
                Buy Now
            </div>
        </a>
    @endif

    @auth
        <iframe src="{{ url('chatify') }}" id="chat-iframe"></iframe>
    @endauth

    <div class="sticky-btn-box">
        @auth
            <a wire:navigate href="{{ customUrl('my-account/live-customer-support') }}"
                class="chat-btn chat-btn-redirect d-flex justify-content-center align-items-center"><i
                    class="anm anm-chat hdr-icon icon"></i></a>
            <div class="chat-btn chat-btn-popup d-flex justify-content-center align-items-center"><i
                    class="anm anm-chat hdr-icon icon"></i></div>
        @endauth
        <div id="site-scroll" class="d-flex justify-content-center align-items-center d-none mt-2"><i
                class="icon anm anm-arw-up"></i></div>
    </div>

    <div class="menubar-mobile d-flex align-items-center justify-content-between d-lg-none">
        <div class="menubar-shop menubar-item">
            <a wire:navigate href="{{ customUrl('products') }}"><i class="menubar-icon anm anm-th-large-l"></i><span
                    class="menubar-label">{{ labels('front_messages.products', 'Products') }}</span></a>
        </div>
        @auth
            <div class="menubar-account menubar-item">
                <a href="{{ customUrl('my-account') }}" wire:navigate><i
                        class="menubar-icon icon anm anm-user-al"></i><span
                        class="menubar-label">{{ labels('front_messages.my_account', 'My Account') }}</span></a>
            </div>
        @else
            <div class="menubar-account menubar-item">
                <a href="{{ customUrl('login') }}" wire:navigate><i class="menubar-icon icon anm anm-user-al"></i><span
                        class="menubar-label">{{ labels('front_messages.sign_in', 'Sign In') }}</span></a>
            </div>
        @endauth
        <div class="menubar-search menubar-item">
            <a wire:navigate href="{{ customUrl('home') }}"><span class="menubar-icon anm anm-home-l"></span><span
                    class="menubar-label">{{ labels('front_messages.home', 'Home') }}</span></a>
        </div>
        <div class="menubar-wish menubar-item">
            <a wire:navigate href="{{ customUrl('my-account.favorites') }}">
                <span class="span-count position-relative text-center"><i
                        class="menubar-icon icon anm anm-heart-l"></i><span
                        class="wishlist-count counter menubar-count">{{ $favorites['favorites_count'] }}</span></span>
                <span class="menubar-label">{{ labels('front_messages.wishlist', 'Wishlist') }}</span>
            </a>
        </div>
        <div class="menubar-cart menubar-item">
            <a href="#;" class="btn-minicart" data-bs-toggle="offcanvas" data-bs-target="#minicart-drawer">
                <span class="span-count position-relative text-center"><i
                        class="menubar-icon icon anm anm-cart-l"></i><span
                        class="cart-count counter menubar-count">{{ $cart_count }}</span></span>
                <span class="menubar-label">{{ labels('front_messages.cart', 'Cart') }}</span>
            </a>
        </div>
    </div>
    <livewire:pages.quickview-model />

</div>
