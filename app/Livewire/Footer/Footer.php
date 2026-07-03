<?php

namespace App\Livewire\Footer;

use App\Models\NewsletterSubscriber;
use App\Services\SettingService;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Footer extends Component
{
    public string $newsletter_email = '';

    public function render()
    {
        $settings = app(SettingService::class)->getSettings('web_settings', true);
        $settings = json_decode($settings);

        // GHS is a dedicated brand theme: use its footer regardless of the
        // store's home-page theme variant (mirrors getHeaderStyle()).
        $view = config('constants.theme') === 'ghs'
            ? 'components.footer.ghs'
            : 'components.footer.footer';

        return view($view, [
            'settings' => $settings,
        ]);
    }

    /**
     * Store a newsletter signup coming from the storefront footer form.
     * Feedback is surfaced via a toast (see the `newsletter-result` listener
     * in the app layout) so it works without inline error markup.
     */
    public function subscribe()
    {
        $validator = Validator::make(
            ['newsletter_email' => $this->newsletter_email],
            ['newsletter_email' => 'required|email|max:191'],
            [
                'newsletter_email.required' => labels('front_messages.newsletter_email_required', 'Please enter your email address.'),
                'newsletter_email.email' => labels('front_messages.newsletter_email_invalid', 'Please enter a valid email address.'),
                'newsletter_email.max' => labels('front_messages.newsletter_email_invalid', 'Please enter a valid email address.'),
            ]
        );

        if ($validator->fails()) {
            $this->dispatch('newsletter-result', type: 'error', message: $validator->errors()->first());
            return;
        }

        $email = strtolower(trim($this->newsletter_email));

        if (NewsletterSubscriber::where('email', $email)->exists()) {
            $this->dispatch('newsletter-result', type: 'info', message: labels('front_messages.newsletter_already_subscribed', 'You are already subscribed.'));
            return;
        }

        NewsletterSubscriber::create([
            'email' => $email,
            'status' => 1,
            'store_id' => session('store_id'),
            'ip_address' => request()->ip(),
        ]);

        $this->newsletter_email = '';
        // Trigger the branded success popup (see the `newsletter-subscribed`
        // listener in the app layout). Errors/duplicates still use a toast.
        $this->dispatch('newsletter-subscribed', message: labels('front_messages.newsletter_subscribed', 'Thank you for subscribing!'));
    }
}
