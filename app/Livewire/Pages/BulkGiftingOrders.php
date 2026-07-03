<?php

namespace App\Livewire\Pages;

use App\Models\BulkGiftingInquiry;
use App\Services\MailService;
use App\Services\SettingService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class BulkGiftingOrders extends Component
{
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $company = '';
    public array $requirement_types = [];
    public string $estimated_quantity = '';
    public string $message = '';

    /** Toggled to the on-page success state after a successful submission. */
    public bool $submitted = false;

    /**
     * Selectable requirement types. Keys are stored; labels are translatable.
     */
    public function requirementOptions(): array
    {
        return [
            'corporate' => labels('front_messages.bulk_req_corporate', 'Corporate Gifting'),
            'wedding'   => labels('front_messages.bulk_req_wedding', 'Wedding Gifting'),
            'festive'   => labels('front_messages.bulk_req_festive', 'Festive Hampers'),
            'reselling' => labels('front_messages.bulk_req_reselling', 'Reselling'),
            'bulk'      => labels('front_messages.bulk_req_bulk', 'Bulk Purchase'),
            'other'     => labels('front_messages.bulk_req_other', 'Other'),
        ];
    }

    protected function rules(): array
    {
        return [
            'name'               => 'required|string|max:191',
            'phone'              => 'required|string|max:32',
            'email'              => 'required|email|max:191',
            'company'            => 'nullable|string|max:191',
            'requirement_types'  => 'nullable|array',
            'requirement_types.*' => 'string|in:' . implode(',', array_keys($this->requirementOptions())),
            'estimated_quantity' => 'nullable|string|max:191',
            'message'            => 'nullable|string|max:5000',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required'  => labels('front_messages.bulk_name_required', 'Please enter your name.'),
            'phone.required' => labels('front_messages.bulk_phone_required', 'Please enter your phone number.'),
            'email.required' => labels('front_messages.bulk_email_required', 'Please enter your email address.'),
            'email.email'    => labels('front_messages.bulk_email_invalid', 'Please enter a valid email address.'),
        ];
    }

    public function render()
    {
        $settings = app(SettingService::class)->getSettings('web_settings', true);
        $settings = json_decode($settings);

        return view('livewire.' . config('constants.theme') . '.pages.bulk-gifting-orders', [
            'settings' => $settings,
        ])->title('Bulk Gifting Orders |');
    }

    public function submitInquiry()
    {
        $this->validate();

        $inquiry = BulkGiftingInquiry::create([
            'name'               => trim($this->name),
            'phone'              => trim($this->phone),
            'email'              => strtolower(trim($this->email)),
            'company'            => $this->company ? trim($this->company) : null,
            'requirement_types'  => array_values($this->requirement_types),
            'estimated_quantity' => $this->estimated_quantity ? trim($this->estimated_quantity) : null,
            'message'            => $this->message ? trim($this->message) : null,
            'status'             => 0,
            'store_id'           => session('store_id'),
            'ip_address'         => request()->ip(),
        ]);

        // Notify the store team. A mail failure must not lose the inquiry, which
        // is already persisted above — so we log and still report success.
        try {
            $this->notifyTeam($inquiry);
        } catch (\Throwable $e) {
            Log::error('Bulk gifting inquiry mail failed: ' . $e->getMessage());
        }

        $this->reset(['name', 'phone', 'email', 'company', 'requirement_types', 'estimated_quantity', 'message']);
        $this->submitted = true;

        $this->dispatch('bulk-inquiry-result', type: 'success', message: labels('front_messages.bulk_inquiry_success', 'Thank you! Our team will get back to you shortly.'));
    }

    private function notifyTeam(BulkGiftingInquiry $inquiry): void
    {
        $email_settings = json_decode(app(SettingService::class)->getSettings('email_settings', true), true);
        $to = $email_settings['email'] ?? null;
        if (empty($to)) {
            return;
        }

        $options = $this->requirementOptions();
        $types = array_map(fn ($key) => $options[$key] ?? $key, $inquiry->requirement_types ?? []);

        $rows = [
            'Name'               => $inquiry->name,
            'Phone'              => $inquiry->phone,
            'Email'              => $inquiry->email,
            'Company'            => $inquiry->company ?: '-',
            'Requirement Type'   => $types ? implode(', ', $types) : '-',
            'Estimated Quantity' => $inquiry->estimated_quantity ?: '-',
            'Message'            => $inquiry->message ?: '-',
        ];

        $html = '<h2>New Bulk Gifting Inquiry</h2><table cellpadding="6" border="1" style="border-collapse:collapse">';
        foreach ($rows as $label => $value) {
            $html .= '<tr><td><strong>' . e($label) . '</strong></td><td>' . nl2br(e($value)) . '</td></tr>';
        }
        $html .= '</table>';

        app(MailService::class)->sendCustomMail($to, 'New Bulk Gifting Inquiry', $html, '');
    }
}
