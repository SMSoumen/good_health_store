<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    @php
        use App\Services\SettingService;
        use App\Services\SeoService;
        use App\Services\StoreService;
        $pwa_settings = app(SettingService::class)->getSettings('pwa_settings', true);
        $pwa_settings = $pwa_settings ? json_decode($pwa_settings, true) : null;
        $background_color =
            $pwa_settings && isset($pwa_settings['background_color']) ? $pwa_settings['background_color'] : '#b52046';

        // Store theme colors — emitted early to avoid a flash of the default button colors (FOUC)
        $store_theme = json_decode(app(StoreService::class)->getCurrentStoreData(session('store_id')));
        $store_theme = is_array($store_theme) && isset($store_theme[0]) ? $store_theme[0] : null;
        $theme_primary_color = $store_theme->primary_color ?? '#041632';
        $theme_secondary_color = $store_theme->secondary_color ?? '#f4a51c';
        $theme_active_color = $store_theme->active_color ?? '#041632';
        $theme_hover_color = $store_theme->hover_color ?? '#f4a51c';

        // Get SEO data
        $seoService = app(SeoService::class);
        $seoData = $seoService->getSeoData('global', null);
        if (!$seoData) {
            $seoData = (object) $seoService->getDefaultSeoData();
        }
    @endphp


    <head>
        <meta name="theme-color" content="{{ $background_color }}" />
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $web_settings['logo']) }}">
        <link rel="manifest" href="{{ route('manifest') }}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if (!file_exists($sqlDumpPath) && !file_exists($installViewPath))
            <meta name="keywords" content='{{ $metaKeys ?? $system_settings['app_name'] }}'>
            <meta name="description" content='{{ $metaDescription ?? $system_settings['app_name'] }}'>
            <meta name="product_image" property="og:image"
                content='{{ $metaImage ?? asset('storage/' . $web_settings['logo']) }}'>
            <link rel="shortcut icon" href="{{ asset('storage/' . $web_settings['favicon']) }}" type="image/x-icon">
            <title>
                {{ $title ?? '' }} {{ $system_settings['app_name'] }}
            </title>
            {!! $structuredData ?? '' !!}
        @endif
        @php
            $url = Request::is('cart/checkout');
        @endphp
        <meta property="og:image:type" content="image/jpg,png,jpeg,gif,bmp,eps">
        <meta property="og:image:width" content="1024">
        <meta property="og:image:height" content="1024">

        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/plugins.css') }}?v={{ $version }}">
        <link rel="stylesheet"
            href="{{ asset('frontend/elegant/css/vendor/photoswipe.min.css') }}?v={{ $version }}">
        <link rel="stylesheet"
            href="{{ asset('frontend/elegant/css/bootstrap-table.min.css') }}?v={{ $version }}">
        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/style.css') }}?v={{ $version }}">
        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/theme.min.css') }}?v={{ $version }}">
        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/star-rating.css') }}?v={{ $version }}">
        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/star-rating.min.css') }}?v={{ $version }}">
        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/intlTelInput.css') }}?v={{ $version }}">
        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/select2.min.css') }}?v={{ $version }}">
        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/iziToast.css') }}?v={{ $version }}">
        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/daterangepicker.css') }}?v={{ $version }}">
        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/responsive.css') }}?v={{ $version }}">
        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/shareon.min.css') }}?v={{ $version }}">
        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/app.css') }}?v={{ $version }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/css/dropzone.css') }}?v={{ $version }}">
        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/swiper-bundle.min.css') }}?v={{ $version }}">

        {{-- GHS brand theme layer (loaded after elegant base; restyles only) --}}
        @if (config('constants.theme') === 'ghs')
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link
                href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Outfit:wght@300;400;500;600;700&display=swap"
                rel="stylesheet">
            <link rel="stylesheet" href="{{ asset('frontend/ghs/css/ghs.css') }}?v={{ $version }}">
        @endif

        {{-- Store theme colors applied on first paint to prevent a color flash on the buttons --}}
        <style>
            :root {
                --primary-color: {{ $theme_primary_color }};
                --secondary-color: {{ $theme_secondary_color }};
                --link-active-color: {{ $theme_active_color }};
                --link-hover-color: {{ $theme_hover_color }};
            }
        </style>


        <script src="{{ asset('frontend/elegant/js/plugins.js') }}?v={{ $version }}"></script>
        <script src="{{ asset('frontend/elegant/js/moment.min.js') }}?v={{ $version }}"></script>
        <script src="{{ asset('frontend/elegant/js/sweetalert2.all.min.js') }}?v={{ $version }}"></script>
        <script src="{{ asset('frontend/elegant/js/swiper-bundle.min.js') }}?v={{ $version }}"></script>
        <script src="{{ asset('frontend/elegant/js/shareon.iife.js') }}?v={{ $version }}"></script>

        <script type="module" src="{{ asset('frontend/elegant/js/firebase-app.js') }}?v={{ $version }}"></script>
        <script type="module" src="{{ asset('frontend/elegant/js/firebase-auth.js') }}?v={{ $version }}"></script>
        <script type="module" src="{{ asset('frontend/elegant/js/firebase-firestore.js') }}?v={{ $version }}"></script>
        <script type="module" src="{{ asset('frontend/elegant/js/bootstrap-table.min.js') }}?v={{ $version }}"></script>
        <script type="module" src="{{ asset('frontend/elegant/js/bootstrap-table-export.min.js') }}?v={{ $version }}">
        </script>
        <script type="module" src="{{ asset('frontend/elegant/js/main.js') }}?v={{ $version }}"></script>
        <script type="module" src="{{ asset('frontend/elegant/js/daterangepicker.js') }}?v={{ $version }}"></script>
        <script type="module" src="{{ asset('frontend/elegant/js/ionicons.js') }}?v={{ $version }}"></script>
        <script type="module" src="{{ asset('frontend/elegant/js/star-rating.js') }}?v={{ $version }}"></script>
        <script type="module" src="{{ asset('frontend/elegant/js/intlTelInput.js') }}?v={{ $version }}"></script>
        <script type="module" src="{{ asset('frontend/elegant/js/iziToast.min.js') }}?v={{ $version }}"></script>
        <script type="module" src="{{ asset('frontend/elegant/js/star-rating.min.js') }}?v={{ $version }}"></script>
        <script type="module" src="{{ asset('frontend/elegant/js/select2.min.js') }}?v={{ $version }}"></script>
        <script src="{{ asset('assets/admin/js/dropzone.js') }}"></script>
        <script type="module" src="{{ asset('frontend/elegant/js/checkout.js') }}?v={{ $version }}"
            @if (Request::is('cart/checkout')) data-navigate-track="reload" @endif></script>
        <script type="module" src="{{ asset('frontend/elegant/js/wallet.js') }}?v={{ $version }}"></script>
        {{-- <script src="{{ asset('frontend/elegant/js/plugins.js') }}?v={{ $version }}"></script> --}}
        <script type="module" src="{{ asset('frontend/elegant/js/custom.js') }}?v={{ $version }}"></script>
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    </head>
    @php
        $is_rtl = session('is_rtl') ?? 0;
        use App\Models\Currency;
    @endphp

    <body {{ $is_rtl == 1 ? 'dir=rtl' : '' }}>

        <div class="loading-state screen">
            <div class="loader">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>
        <input type="hidden" id="user_id" name="user_id" value="{{ auth()->id() ?? '' }}">
        <input type="hidden" id="custom_url" name="custom_url" value="{{ url()->full() }}">
        <input type="hidden" id="current_url" name="current_url" value="{{ url()->current() }}">
        <input type="hidden" id="store_slug" name="store_slug" value="{{ session('store_slug') }}">
        <input type="hidden" id="current_store_id" name="current_store_id" value="{{ session('store_id') }}">
        <input type="hidden" id="default_store_slug" name="default_store_slug"
            value="{{ session('default_store_slug') }}">
        @if (!file_exists($sqlDumpPath) && !file_exists($installViewPath))
            @php
                $currency_code = session('currency') ?? $system_settings['currency_setting']['code'];
                $currency_details = fetchDetails(Currency::class, ['code' => $currency_code]);
                $currency_symbol = $currency_details[0]->symbol ?? $system_settings['currency_setting']['symbol'];
            @endphp
            <input type="hidden" id="currency" name="currency" value="{{ $currency_symbol }}">

            <livewire:header.header />
        @endif
        {{ $slot }}
        @if (!file_exists($sqlDumpPath) && !file_exists($installViewPath))
            <livewire:footer.footer />
        @endif

        {{-- GHS newsletter success popup (shown after a footer signup) --}}
        @if (config('constants.theme') === 'ghs')
            <div class="modal fade ghs-news-modal" id="ghs_newsletter_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <button type="button" class="btn-close ghs-news-modal-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                        <div class="modal-body text-center">
                            <img class="ghs-news-modal-img"
                                src="{{ asset('frontend/ghs/images/newsletter-success.png') }}" alt=""
                                onerror="this.style.display='none'">
                            <h3 class="ghs-news-modal-title">
                                {{ labels('front_messages.newsletter_popup_title', "You're Subscribed!") }}
                            </h3>
                            <p class="ghs-news-modal-text" id="ghs_newsletter_modal_text">
                                {{ labels('front_messages.newsletter_popup_text', 'Thank you for joining our mailing list. Keep an eye on your inbox for wellness tips, exclusive offers & updates from the heart of Kashmir.') }}
                            </p>
                            <button type="button" class="ghs-news-modal-btn" data-bs-dismiss="modal">
                                {{ labels('front_messages.continue_shopping', 'Continue Shopping') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <x-include-modal.modals />
        <link rel="stylesheet" href="{{ asset('frontend/elegant/css/lightbox.css') }}">
        <script src="{{ asset('/sw.js') }}"></script>
        <script>
            if ("serviceWorker" in navigator) {
                // Register a service worker hosted at the root of the
                // site using the default scope.
                navigator.serviceWorker.register("/sw.js").then(
                    (registration) => {
                        console.log("Service worker registration succeeded:", registration);
                    },
                    (error) => {
                        console.error(`Service worker registration failed: ${error}`);
                    },
                );
            } else {
                console.error("Service workers are not supported.");
            }
        </script>
    </body>
    {{-- <script src="{{ asset('frontend/elegant/js/checkout.js') }}?v={{ $version }}"></script>
 <script src="{{ asset('frontend/elegant/js/wallet.js') }}?v={{ $version }}"></script>
 <script src="{{ asset('frontend/elegant/js/custom.js') }}?v={{ $version }}"></script> --}}
    <script>
        function home_slider() {
            const isRTL = {{ session('is_rtl', 0) }} === 1;
            console.log("RTL mode:", isRTL);
            console.log(isRTL);
            $(".home-slideshow").slick({
                dots: true,
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                fade: false,
                arrows: false,
                autoplay: true,
                autoplaySpeed: 7000,
                lazyLoad: "ondemand",
                rtl: isRTL // only reverse if RTL
            });
        }

        home_slider();
    </script>
    <script src="{{ asset('frontend/elegant/js/plugins.js') }}?v={{ $version }}" data-navigate-track="reload">
    </script>
    <script src="{{ asset('frontend/elegant/js/vendor/jquery.elevatezoom.js') }}"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('validationSuccessShow', (payload) => {
                if (payload?.success?.length) {
                    iziToast.success({
                        message: payload.success[0],
                        position: 'topRight',
                    });
                }
            });

            // Newsletter footer signup feedback (Footer Livewire `subscribe`)
            Livewire.on('newsletter-result', (payload) => {
                const data = Array.isArray(payload) ? payload[0] : payload;
                const type = data?.type || 'success';
                const message = data?.message || '';
                if (!message) return;
                const fn = type === 'error' ? 'error' : (type === 'info' ? 'info' : 'success');
                iziToast[fn]({
                    message: message,
                    position: 'topRight',
                });
            });

            // Bulk gifting inquiry feedback (BulkGiftingOrders Livewire `submitInquiry`)
            Livewire.on('bulk-inquiry-result', (payload) => {
                const data = Array.isArray(payload) ? payload[0] : payload;
                const type = data?.type || 'success';
                const message = data?.message || '';
                if (!message) return;
                const fn = type === 'error' ? 'error' : (type === 'info' ? 'info' : 'success');
                iziToast[fn]({
                    message: message,
                    position: 'topRight',
                });
            });

            // Successful subscribe -> branded popup
            Livewire.on('newsletter-subscribed', () => {
                const modalEl = document.getElementById('ghs_newsletter_modal');
                if (modalEl && typeof $ !== 'undefined' && $.fn.modal) {
                    $(modalEl).modal('show');
                }
            });
        });
    </script>

</html>
