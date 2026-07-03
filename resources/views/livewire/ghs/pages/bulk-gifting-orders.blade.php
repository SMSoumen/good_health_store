@php
    $title = labels('front_messages.bulk_gifting_orders', 'Bulk Gifting Orders');
    $app_name = $settings->app_name ?? 'Good Health Store';

    // Catalogue button points to the storefront products page (opens in a new tab).
    $catalogue_url = customUrl('products');

    // Trust strip under the banner (matches the brand banner artwork).
    $bulk_trust = [
        ['icon' => 'leaf-outline',            'label' => labels('front_messages.bulk_trust_1', 'Authentic Kashmiri Products')],
        ['icon' => 'gift-outline',            'label' => labels('front_messages.bulk_trust_2', 'Custom Gifting Available')],
        ['icon' => 'flower-outline',          'label' => labels('front_messages.bulk_trust_3', 'Ethical Sourcing')],
        ['icon' => 'people-outline',          'label' => labels('front_messages.bulk_trust_4', 'Supporting Local Artisans & Farmers')],
        ['icon' => 'cube-outline',            'label' => labels('front_messages.bulk_trust_5', 'PAN India Delivery')],
        ['icon' => 'shield-checkmark-outline','label' => labels('front_messages.bulk_trust_6', 'Quality You Can Trust')],
    ];

    // Hamper showcase at the foot of the page (graceful fallback if absent).
    $hamper_examples = [
        'frontend/ghs/images/hamper-1.jpg',
        'frontend/ghs/images/hamper-2.jpg',
        'frontend/ghs/images/hamper-3.jpg',
        'frontend/ghs/images/hamper-4.jpg',
    ];
@endphp
<div class="ghs-bulk">
    <div class="container">
        {{-- ===================== BANNER ===================== --}}
        <div class="ghs-bulk-banner">
            <img src="{{ asset('frontend/ghs/images/bulk-gifting-banner.jpg') }}"
                alt="{{ $app_name }} — {{ $title }}" loading="lazy"
                onerror="this.closest('.ghs-bulk-banner').classList.add('is-empty');this.style.display='none'">
        </div>

        <ul class="ghs-bulk-trust list-unstyled">
            @foreach ($bulk_trust as $item)
                <li>
                    <ion-icon name="{{ $item['icon'] }}"></ion-icon>
                    <span>{{ $item['label'] }}</span>
                </li>
            @endforeach
        </ul>

        {{-- ===================== DESCRIPTION ===================== --}}
        <section class="ghs-bulk-desc">
            <h2 class="ghs-bulk-heading">{{ labels('front_messages.description', 'Description') }}</h2>

            <p>{{ labels('front_messages.bulk_desc_1', 'Looking to place a bulk order for weddings, corporate gifting, reselling, events, festive gifting, or wholesale purchases? ' . $app_name . ' offers premium Kashmiri products in bulk quantities with dedicated support and catalogue-based ordering.') }}</p>
            <p>{{ labels('front_messages.bulk_desc_2', 'You can explore our latest catalogue and submit your requirements through the form below. Our team will get back to you with product suggestions, pricing, customization options, and bulk order assistance.') }}</p>
            <p>{{ labels('front_messages.bulk_desc_3', 'Please note: Our catalogue is not limited to the products displayed. We can also source and provide a wider range of authentic Kashmiri products based on your specific requirements and preferences.') }}</p>

            @if (!empty($catalogue_url))
                <a href="{{ $catalogue_url }}" target="_blank" rel="noopener" class="ghs-bulk-catalogue-btn">
                    <ion-icon name="book-outline"></ion-icon>
                    <span>{{ labels('front_messages.view_catalogue', 'View Our Catalogue') }}</span>
                </a>
            @endif
        </section>

        {{-- ===================== INQUIRY FORM ===================== --}}
        <section class="ghs-bulk-form-wrap">
            <h2 class="ghs-bulk-form-title">{{ labels('front_messages.bulk_order_inquiry_form', 'Bulk Order Inquiry Form') }}</h2>

            @if ($submitted)
                <div class="ghs-bulk-success" role="status">
                    <ion-icon name="checkmark-circle-outline"></ion-icon>
                    <span>{{ labels('front_messages.bulk_inquiry_success', 'Thank you! Our team will get back to you shortly.') }}</span>
                </div>
            @endif

            <form wire:submit="submitInquiry" class="ghs-bulk-form">
                <div class="row g-3 g-md-4">
                    {{-- Full Name --}}
                    <div class="col-12 col-md-6">
                        <label class="ghs-bulk-label" for="bulk_name">{{ labels('front_messages.full_name', 'Full Name') }}</label>
                        <input wire:model="name" type="text" id="bulk_name" class="ghs-bulk-input"
                            placeholder="{{ labels('front_messages.enter_name', 'Enter Name') }}">
                        @error('name') <p class="ghs-bulk-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Phone Number --}}
                    <div class="col-12 col-md-6">
                        <label class="ghs-bulk-label" for="bulk_phone">{{ labels('front_messages.phone_number', 'Phone Number') }}</label>
                        <input wire:model="phone" type="text" id="bulk_phone" class="ghs-bulk-input"
                            placeholder="{{ labels('front_messages.enter_phone_number', 'Enter Phone Number') }}">
                        @error('phone') <p class="ghs-bulk-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email Address --}}
                    <div class="col-12 col-md-6">
                        <label class="ghs-bulk-label" for="bulk_email">{{ labels('front_messages.email_address', 'Email Address') }}</label>
                        <input wire:model="email" type="email" id="bulk_email" class="ghs-bulk-input"
                            placeholder="{{ labels('front_messages.enter_email', 'Enter Email') }}">
                        @error('email') <p class="ghs-bulk-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Company Name (Optional) --}}
                    <div class="col-12 col-md-6">
                        <label class="ghs-bulk-label" for="bulk_company">{{ labels('front_messages.company_name_optional', 'Company Name (Optional)') }}</label>
                        <input wire:model="company" type="text" id="bulk_company" class="ghs-bulk-input"
                            placeholder="{{ labels('front_messages.enter_company_name', 'Enter Company Name') }}">
                        @error('company') <p class="ghs-bulk-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Requirement Type --}}
                    <div class="col-12">
                        <span class="ghs-bulk-label">{{ labels('front_messages.requirement_type', 'Requirement Type') }}</span>
                        <div class="ghs-bulk-checks">
                            @foreach ($this->requirementOptions() as $key => $label)
                                <label class="ghs-bulk-check">
                                    <input wire:model="requirement_types" type="checkbox" value="{{ $key }}">
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('requirement_types') <p class="ghs-bulk-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Estimated Quantity --}}
                    <div class="col-12 col-md-6">
                        <label class="ghs-bulk-label" for="bulk_qty">{{ labels('front_messages.estimated_quantity', 'Estimated Quantity') }}</label>
                        <input wire:model="estimated_quantity" type="text" id="bulk_qty" class="ghs-bulk-input"
                            placeholder="{{ labels('front_messages.enter_quantity', 'Enter Quantity') }}">
                        @error('estimated_quantity') <p class="ghs-bulk-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Message / Requirement --}}
                    <div class="col-12">
                        <label class="ghs-bulk-label" for="bulk_message">{{ labels('front_messages.message_requirement', 'Message / Requirement') }}</label>
                        <textarea wire:model="message" id="bulk_message" rows="5" class="ghs-bulk-input"
                            placeholder="{{ labels('front_messages.tell_us_what_you_are_looking_for', 'Tell us what you are looking for') }}"></textarea>
                        @error('message') <p class="ghs-bulk-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- CTA --}}
                    <div class="col-12">
                        <button type="submit" class="ghs-bulk-submit" wire:loading.attr="disabled" wire:target="submitInquiry">
                            <span wire:loading.remove wire:target="submitInquiry">{{ labels('front_messages.submit_inquiry', 'Submit Inquiry') }}</span>
                            <span wire:loading wire:target="submitInquiry">{{ labels('front_messages.submitting', 'Submitting...') }}</span>
                        </button>
                        <p class="ghs-bulk-note">{{ labels('front_messages.bulk_team_get_back', 'Our team will get back to you shortly.') }}</p>
                    </div>
                </div>
            </form>
        </section>

        {{-- ===================== HAMPER EXAMPLES ===================== --}}
        <section class="ghs-bulk-hampers">
            <h3 class="ghs-bulk-heading">{{ labels('front_messages.hamper_examples', 'Some Hamper Examples') }}</h3>
            <div class="ghs-bulk-hamper-grid">
                @foreach ($hamper_examples as $img)
                    <div class="ghs-bulk-hamper">
                        <img src="{{ asset($img) }}" alt="{{ $app_name }}" loading="lazy"
                            onerror="this.closest('.ghs-bulk-hamper').classList.add('is-empty');this.style.display='none'">
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
