{{--
    GHS entry / welcome popup
    -------------------------
    A brand-styled "first order discount" modal that appears shortly after the
    page loads. Self-contained: its own markup, styles (in ghs.css) and JS.

    Currently used on the home page, but written to be reusable on any GHS page:
        <x-ghs.entry-popup />

    Behaviour:
      - Shows once after `delay` ms, then remembers dismissal in localStorage
        for `remember` days (so it doesn't nag returning visitors).
      - Closes on the X button, on Escape, or by clicking the dim backdrop.
      - Plays nicely with Livewire SPA navigation (wire:navigate).

    Props:
      offer     : the headline discount text (e.g. "10% OFF")
      appName   : store name shown in the body
      logo      : logo path relative to storage/ (defaults to web settings logo)
      delay     : ms to wait after load before showing (default 1500)
      remember  : days to keep the popup hidden after it's dismissed (default 7)
      key       : localStorage key (override to run several independent popups)
--}}
@props([
    'offer' => '10% OFF',
    'appName' => $web_settings['app_name'] ?? 'Good Health Store',
    'logo' => $web_settings['logo'] ?? null,
    'delay' => 1500,
    'remember' => 7,
    'key' => 'ghs_entry_popup',
])

<div class="ghs-entry-popup" id="{{ $key }}" data-delay="{{ (int) $delay }}"
    data-remember="{{ (int) $remember }}" data-key="{{ $key }}" aria-hidden="true" role="dialog"
    aria-modal="true" aria-label="{{ labels('front_messages.welcome_offer', 'Welcome offer') }}">

    <div class="ghs-ep-backdrop" data-ep-close></div>

    <div class="ghs-ep-dialog" role="document">
        <button type="button" class="ghs-ep-close" data-ep-close
            aria-label="{{ labels('front_messages.close', 'Close') }}">
            <ion-icon name="close-outline"></ion-icon>
        </button>

        {{-- Offer header --}}
        <div class="ghs-ep-head">
            @if (!empty($logo))
                <span class="ghs-ep-logo">
                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ $appName }}"
                        onerror="this.closest('.ghs-ep-logo').style.display='none'">
                </span>
            @endif
            <p class="ghs-ep-offer">
                {{ labels('front_messages.enjoy', 'Enjoy') }}
                <strong>{{ $offer }}</strong>
            </p>
            <p class="ghs-ep-offer-sub">{{ labels('front_messages.on_your_first_order', 'on your first order') }}</p>
        </div>

        {{-- Body / form --}}
        <div class="ghs-ep-body">
            <p class="ghs-ep-welcome">{{ labels('front_messages.welcome_to', 'WELCOME TO') }}</p>
            <h3 class="ghs-ep-name">{{ $appName }}</h3>
            <span class="ghs-ep-divider"><ion-icon name="leaf"></ion-icon></span>

            <form class="ghs-ep-form" novalidate>
                <div class="ghs-ep-field">
                    <span class="ghs-ep-flag">🇮🇳 +91</span>
                    <input type="tel" name="mobile" class="ghs-ep-input" inputmode="numeric" maxlength="10"
                        autocomplete="tel" placeholder="{{ labels('front_messages.enter_mobile_number', 'Enter Mobile Number') }}">
                </div>
                <p class="ghs-ep-error" data-ep-error hidden>
                    {{ labels('front_messages.enter_valid_mobile', 'Please enter a valid 10-digit mobile number') }}
                </p>

                <label class="ghs-ep-check">
                    <input type="checkbox" name="opt_in" checked>
                    <span>{{ labels('front_messages.notify_offers_updates', 'Notify me with offers & updates') }}</span>
                </label>

                <button type="submit" class="ghs-ep-submit">
                    {{ labels('front_messages.unlock_offer', 'Unlock My') }} {{ $offer }}
                </button>

                <p class="ghs-ep-terms">
                    {{ labels('front_messages.by_continuing_agree', 'By continuing, you agree to our') }}<br>
                    <a wire:navigate href="{{ customUrl('privacy_policy') }}">{{ labels('front_messages.privacy_policy', 'Privacy Policy') }}</a>
                    &amp;
                    <a wire:navigate href="{{ customUrl('term_and_conditions') }}">{{ labels('front_messages.t_and_c', 'T&Cs') }}</a>
                </p>
            </form>

            {{-- Success state (shown after submit) --}}
            <div class="ghs-ep-success" data-ep-success hidden>
                <span class="ghs-ep-success-ico"><ion-icon name="checkmark-circle"></ion-icon></span>
                <h4>{{ labels('front_messages.offer_unlocked', 'Offer unlocked!') }}</h4>
                <p>{{ labels('front_messages.offer_unlocked_text', 'Use your discount at checkout on your first order.') }}</p>
            </div>
        </div>
    </div>
</div>

@once
    <script>
        (function () {
                function initGhsEntryPopup(root) {
                    if (!root || root.dataset.epReady === '1') return;
                    root.dataset.epReady = '1';

                    var key = root.dataset.key || 'ghs_entry_popup';
                    var delay = parseInt(root.dataset.delay || '1500', 10);
                    var remember = parseInt(root.dataset.remember || '7', 10);
                    var storeKey = key + '_dismissed_until';

                    var dialog = root.querySelector('.ghs-ep-dialog');
                    var form = root.querySelector('.ghs-ep-form');
                    var input = root.querySelector('.ghs-ep-input');
                    var error = root.querySelector('[data-ep-error]');
                    var success = root.querySelector('[data-ep-success]');

                    function dismissedRecently() {
                        try {
                            var until = parseInt(localStorage.getItem(storeKey) || '0', 10);
                            return until && Date.now() < until;
                        } catch (e) { return false; }
                    }
                    function rememberDismissal() {
                        try {
                            localStorage.setItem(storeKey, String(Date.now() + remember * 86400000));
                        } catch (e) {}
                    }

                    function open() {
                        if (root.classList.contains('is-open')) return;
                        root.classList.add('is-open');
                        root.setAttribute('aria-hidden', 'false');
                        document.body.classList.add('ghs-ep-lock');
                    }
                    function close() {
                        root.classList.remove('is-open');
                        root.setAttribute('aria-hidden', 'true');
                        document.body.classList.remove('ghs-ep-lock');
                        rememberDismissal();
                    }

                    // keep numeric input only
                    if (input) {
                        input.addEventListener('input', function () {
                            this.value = this.value.replace(/\D/g, '').slice(0, 10);
                            if (error) error.hidden = true;
                        });
                    }

                    if (form) {
                        form.addEventListener('submit', function (e) {
                            e.preventDefault();
                            var val = (input && input.value || '').trim();
                            if (!/^[6-9]\d{9}$/.test(val)) {
                                if (error) error.hidden = false;
                                if (input) input.focus();
                                return;
                            }
                            // TODO: wire to backend (Livewire) to capture the lead.
                            form.hidden = true;
                            if (success) success.hidden = false;
                            rememberDismissal();
                            setTimeout(close, 2600);
                        });
                    }

                    root.querySelectorAll('[data-ep-close]').forEach(function (el) {
                        el.addEventListener('click', close);
                    });
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape' && root.classList.contains('is-open')) close();
                    });

                    if (!dismissedRecently()) {
                        setTimeout(open, delay);
                    }
                }

                function boot() {
                    document.querySelectorAll('.ghs-entry-popup').forEach(initGhsEntryPopup);
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', boot);
                } else {
                    boot();
                }
                // Re-init after Livewire SPA navigation.
                document.addEventListener('livewire:navigated', boot);
        })();
    </script>
@endonce
