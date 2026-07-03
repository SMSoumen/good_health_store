@php
    $title = labels('front_messages.about_us', 'About Us');
    $app_name = $settings->app_name ?? 'Good Health Store';

    // "Our Approach" checklist — translatable, falls back to the brand copy.
    $approach_points = [
        labels('front_messages.about_approach_1', '100% Natural Ingredients, thoughtfully sourced with care'),
        labels('front_messages.about_approach_2', 'No Added Flavours or Artificial Enhancers'),
        labels('front_messages.about_approach_3', 'Made for Everyday Use, trusted first by our own family'),
        labels('front_messages.about_approach_4', 'Rooted in Simplicity, Transparency, and Honest Practices'),
        labels('front_messages.about_approach_5', 'Supporting Local Sourcing and Purpose-Driven Impact'),
        labels('front_messages.about_approach_6', "In collaboration with an NGO supporting children's education"),
    ];
@endphp
<div>
    {{-- ===================== GHS ABOUT — WHAT IS GOOD HEALTH STORE ===================== --}}
    <section class="ghs-about">
        <span class="ghs-about-leaf ghs-about-leaf--tl" aria-hidden="true"></span>

        <div class="container">
            <div class="ghs-about-head text-center">
                <h2 class="ghs-about-title">
                    {{ labels('front_messages.about_what_is', 'What is') }} {{ $app_name }}?
                </h2>
                <span class="ghs-about-divider" aria-hidden="true">
                    <ion-icon name="leaf"></ion-icon>
                </span>
            </div>

            <div class="row align-items-center g-4 g-lg-5">
                {{-- Image --}}
                <div class="col-12 col-lg-5">
                    <div class="ghs-about-figure">
                        <img src="{{ asset('frontend/ghs/images/about-beekeeper.jpg') }}"
                            alt="{{ $app_name }}" loading="lazy"
                            onerror="this.closest('.ghs-about-figure').classList.add('is-empty');this.style.display='none'">
                    </div>
                </div>

                {{-- Editorial copy --}}
                <div class="col-12 col-lg-7">
                    <div class="ghs-about-body">
                        @if (!empty($about_us))
                            <div class="ghs-about-text">{!! nl2br(e($about_us)) !!}</div>
                        @else
                            <p>{{ labels('front_messages.about_para_1', 'The Good Health Store is a family-run wellness brand rooted in Kashmiri traditional wellness, organic sourcing, and a commitment to healthy living. What began as a simple intention to choose better for ourselves has grown into a dedication to offer clean, natural, and thoughtfully sourced products.') }}</p>
                            <p>{{ labels('front_messages.about_para_2', 'Our focus goes beyond just providing products. We create what we trust for our own family first, ensuring every product reflects simplicity, purity, and everyday wellness.') }}</p>
                            <p>{{ labels('front_messages.about_para_3', 'We aim to bring you closer to ingredients that are natural, minimally processed, and aligned with a healthier way of living. A part of every purchase also contributes towards supporting education through our initiative, making each choice more meaningful.') }}</p>
                        @endif

                        <h3 class="ghs-about-subtitle">{{ labels('front_messages.about_our_approach', 'Our Approach') }}</h3>
                        <ul class="ghs-about-list">
                            @foreach ($approach_points as $point)
                                <li>
                                    <ion-icon name="checkmark-circle"></ion-icon>
                                    <span>{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ customUrl('products') }}" wire:navigate
                            class="ghs-about-btn">{{ labels('front_messages.shop_now', 'Shop Now') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== GHS ABOUT — WHY CHOOSE US ===================== --}}
    @php
        $why_points = [
            [
                'icon' => 'sparkles',
                'title' => labels('front_messages.about_why_1_title', 'Premium & Clean Quality'),
                'text' => labels('front_messages.about_why_1_text', 'Our products are 100% natural, free from adulteration, added sugar, artificial colours, or unnecessary additives. Carefully sourced and FSSAI compliant, they are made to deliver pure, honest nutrition.'),
            ],
            [
                'icon' => 'leaf',
                'title' => labels('front_messages.about_why_2_title', 'Rooted in Kashmir & Local Sourcing'),
                'text' => labels('front_messages.about_why_2_text', 'We work closely with local farmers and producers, supporting Kashmiri communities and ensuring authentic, high-quality ingredients while promoting sustainable livelihoods.'),
            ],
            [
                'icon' => 'heart',
                'title' => labels('front_messages.about_why_3_title', 'Purpose Beyond Products'),
                'text' => labels('front_messages.about_why_3_text', 'At least 10% of our profits go towards supporting education through our NGO, Khaliq-Ul-Educational Trust, helping children access better learning opportunities.'),
            ],
            [
                'icon' => 'shield-checkmark',
                'title' => labels('front_messages.about_why_4_title', 'Trusted & Accessible'),
                'text' => labels('front_messages.about_why_4_text', 'Trusted by 1,000+ customers across India, we offer PAN India delivery along with customization options for gifting, making clean wellness accessible and convenient.'),
            ],
        ];
    @endphp
    <section class="ghs-why">

        <div class="container">
            <div class="ghs-about-head text-center">
                <h2 class="ghs-about-title">{{ labels('front_messages.about_why_choose_us', 'Why Choose Us?') }}</h2>
                <span class="ghs-about-divider" aria-hidden="true">
                    <ion-icon name="leaf"></ion-icon>
                </span>
            </div>

            <div class="row align-items-center g-4 g-lg-5">
                {{-- Feature grid --}}
                <div class="col-12 col-lg-6">
                    <div class="row g-4">
                        @foreach ($why_points as $i => $point)
                            {{-- 2nd & 3rd items use the green variant; 1st & 4th stay pink --}}
                            <div class="col-12 col-sm-6">
                                <div class="ghs-why-item {{ in_array($i, [1, 2]) ? 'ghs-why-item--green' : '' }}">
                                    <span class="ghs-why-ico">
                                        <ion-icon name="{{ $point['icon'] }}"></ion-icon>
                                    </span>
                                    <h3 class="ghs-why-title">
                                        <span class="ghs-why-num">{{ $i + 1 }}.</span>{{ $point['title'] }}
                                    </h3>
                                    <p class="ghs-why-text">{{ $point['text'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Product showcase --}}
                <div class="col-12 col-lg-6">
                    <div class="ghs-why-figure">
                        <img src="{{ asset('frontend/ghs/images/about-products.webp') }}"
                            alt="{{ $app_name }}" loading="lazy"
                            onerror="this.closest('.ghs-why-figure').classList.add('is-empty');this.style.display='none'">
                        <img class="ghs-why-fssai" src="{{ asset('frontend/ghs/images/fssai.png') }}"
                            alt="FSSAI Registered" loading="lazy" onerror="this.style.display='none'">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- floral vine straddling the section 2–3 joint --}}
    <div class="ghs-floral-joint ghs-floral-joint--left" aria-hidden="true">
        <img src="{{ asset('frontend/ghs/images/floral-vine.png') }}" alt="" loading="lazy"
            onerror="this.style.display='none'">
    </div>

    {{-- ===================== GHS ABOUT — MORE THAN JUST A BRAND ===================== --}}
    @php
        $brand_slides = [
            [
                'image' => 'frontend/ghs/images/brand-1.jpg',
                'text' => labels('front_messages.about_brand_1', 'We are not just building products, we are building trust. Every step we take, from sourcing to delivery, is guided by the idea of offering something we genuinely believe in.'),
            ],
            [
                'image' => 'frontend/ghs/images/brand-2.jpg',
                'text' => labels('front_messages.about_brand_2', 'We work closely with local Kashmiri beekeepers, farmers and artisans, ensuring authentic sourcing while supporting rural livelihoods. Every product goes through careful selection and quality checks to ensure it is free from additives and meets our standards of purity and consistency.'),
            ],
            [
                'image' => 'frontend/ghs/images/brand-3.jpg',
                'text' => labels('front_messages.about_brand_3', 'As a homegrown brand, we focus on creating products that are simple, natural, and made for everyday use. From maintaining quality to offering personalized gifting options, we aim to make clean and thoughtful wellness accessible to everyone.'),
            ],
            [
                'image' => 'frontend/ghs/images/brand-4.jpg',
                'text' => labels('front_messages.about_brand_4', 'Customer satisfaction remains at the heart of everything we do. We continuously strive to provide a smooth experience, reliable quality, and products that truly meet expectations, building long-term trust with every customer.'),
            ],
            [
                'image' => 'frontend/ghs/images/brand-5.jpg',
                'text' => labels('front_messages.about_brand_5', 'A part of everything we earn goes towards supporting our NGO, Khaliq-Ul-Educational Trust, with at least 10% of our profits dedicated to helping children access better education and opportunities.'),
            ],
            [
                'image' => 'frontend/ghs/images/brand-6.jpg',
                'text' => labels('front_messages.about_brand_6', 'With every purchase, you are not just choosing better for yourself, but also supporting local communities and becoming a part of something meaningful.'),
            ],
        ];
    @endphp
    <section class="ghs-brand">

        <div class="container">
            <div class="ghs-about-head text-center">
                <h2 class="ghs-about-title">{{ labels('front_messages.about_more_than_brand', 'More Than just a Brand') }}</h2>
                <span class="ghs-about-divider" aria-hidden="true">
                    <ion-icon name="leaf"></ion-icon>
                </span>
            </div>

            <div class="ghs-brand-slider">
                <button type="button" class="ghs-brand-nav ghs-brand-prev" aria-label="Previous">
                    <ion-icon name="arrow-back-outline"></ion-icon>
                </button>

                <div class="swiper ghs-brand-swiper">
                    <div class="swiper-wrapper">
                        {{-- two items per slide (stacked); arrows page to the next pair --}}
                        @foreach (array_chunk($brand_slides, 2) as $pair)
                            <div class="swiper-slide">
                                <div class="ghs-brand-pair">
                                    @foreach ($pair as $slide)
                                        <div class="ghs-brand-card">
                                            <div class="ghs-brand-figure">
                                                <img src="{{ asset($slide['image']) }}" alt="{{ $app_name }}" loading="lazy"
                                                    onerror="this.closest('.ghs-brand-figure').classList.add('is-empty');this.style.display='none'">
                                            </div>
                                            <p class="ghs-brand-text">{{ $slide['text'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="button" class="ghs-brand-nav ghs-brand-next" aria-label="Next">
                    <ion-icon name="arrow-forward-outline"></ion-icon>
                </button>
            </div>

            <div class="swiper-pagination ghs-brand-pagination"></div>
        </div>
    </section>

    @script
        <script>
            (function () {
                var el = document.querySelector('.ghs-brand-swiper');
                if (!el || typeof Swiper === 'undefined' || el.swiper) return;
                new Swiper(el, {
                    slidesPerView: 1,
                    spaceBetween: 24,
                    loop: true,
                    autoHeight: true,
                    autoplay: { delay: 6000, disableOnInteraction: false },
                    navigation: {
                        nextEl: '.ghs-brand-next',
                        prevEl: '.ghs-brand-prev',
                    },
                    pagination: {
                        el: '.ghs-brand-pagination',
                        clickable: true,
                    },
                });
            })();
        </script>
    @endscript

    {{-- floral vine straddling the section 3–4 joint --}}
    <div class="ghs-floral-joint ghs-floral-joint--right" aria-hidden="true">
        <img src="{{ asset('frontend/ghs/images/floral-vine.png') }}" alt="" loading="lazy"
            onerror="this.style.display='none'">
    </div>

    {{-- ===================== GHS ABOUT — REGISTRATIONS & CERTIFICATES ===================== --}}
    @php
        $cert_blocks = [
            [
                'icon' => 'person-outline',
                'title' => labels('front_messages.about_cert_1_title', 'Farmers & Source Certifications'),
                'text' => labels('front_messages.about_cert_1_text', 'We work closely with trusted farmers and beekeepers who follow natural and responsible practices, ensuring certified sourcing and authenticity from the origin. Every batch is carefully tested by us to meet strict quality standards and, where applicable, verified through GI tagging. Our products also undergo advanced lab tests such as NMR, SMR, etc to confirm purity, quality, and ensure only the highest standard reaches you.'),
            ],
            [
                'icon' => 'shield-checkmark-outline',
                'title' => labels('front_messages.about_cert_2_title', 'Our Certifications & Registrations'),
                'text' => labels('front_messages.about_cert_2_text', 'As a brand, we are officially registered and compliant with all necessary standards to ensure transparency and trust. Our certifications reflect our commitment to operating responsibly while delivering quality products you can rely on with complete confidence.'),
            ],
        ];

        $cert_badges = [
            [
                'image' => 'frontend/ghs/images/cert-gst.png',
                'icon' => 'ribbon-outline',
                'label' => labels('front_messages.gst_registered', 'GST Registered'),
                'sub' => labels('front_messages.gst_no', 'GST No.'),
                'value' => '07ABFPY0574P1ZW',
            ],
            [
                'image' => 'frontend/ghs/images/cert-msme.png',
                'icon' => 'shield-checkmark-outline',
                'label' => labels('front_messages.msme_registered', 'MSME Registered'),
                'sub' => labels('front_messages.regd_no', 'Regd. No.'),
                'value' => '4480751',
            ],
            [
                'image' => 'frontend/ghs/images/cert-trademark.png',
                'icon' => 'checkmark-circle-outline',
                'label' => labels('front_messages.trademark_certified', 'Trademark Certified'),
                'sub' => labels('front_messages.regd_no', 'Regd. No.'),
                'value' => '223002000934',
            ],
        ];
    @endphp
    <section class="ghs-cert">
        <span class="ghs-about-leaf ghs-about-leaf--br" aria-hidden="true"></span>

        <div class="container">
            <div class="ghs-about-head text-center">
                <h2 class="ghs-about-title">{{ labels('front_messages.about_registrations_certificates', 'Registrations & Certificates') }}</h2>
                <span class="ghs-about-divider" aria-hidden="true">
                    <ion-icon name="leaf"></ion-icon>
                </span>
            </div>

            <div class="ghs-cert-blocks">
                @foreach ($cert_blocks as $block)
                    <div class="ghs-cert-item">
                        <span class="ghs-cert-ico">
                            <ion-icon name="{{ $block['icon'] }}"></ion-icon>
                        </span>
                        <div class="ghs-cert-body">
                            <h3 class="ghs-cert-title">{{ $block['title'] }}</h3>
                            <p class="ghs-cert-text">{{ $block['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="ghs-cert-badges">
                @foreach ($cert_badges as $badge)
                    <div class="ghs-cert-badge">
                        <span class="ghs-cert-badge-logo">
                            <img src="{{ asset($badge['image']) }}" alt="{{ $badge['label'] }}" loading="lazy"
                                onerror="this.style.display='none';this.nextElementSibling.style.display='inline-flex'">
                            <span class="ghs-cert-badge-fallback" style="display:none">
                                <ion-icon name="{{ $badge['icon'] }}"></ion-icon>
                            </span>
                        </span>
                        <div class="ghs-cert-badge-meta">
                            <div class="ghs-cert-badge-label">{{ $badge['label'] }}</div>
                            <div class="ghs-cert-badge-sub">{{ $badge['sub'] }}</div>
                            <div class="ghs-cert-badge-value">{{ $badge['value'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="service-section section section-color-light pt-0">
        <x-utility.others.serviceSection />
    </div>
</div>
