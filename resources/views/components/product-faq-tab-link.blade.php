@props([
    'faqs' => [],
])

@php
    // Only count FAQs that have both a question and an answer (answered/public).
    $hasRows = false;
    foreach ($faqs as $faq) {
        $q = is_array($faq) ? ($faq['question'] ?? '') : ($faq->question ?? '');
        $a = is_array($faq) ? ($faq['answer'] ?? '') : ($faq->answer ?? '');
        if (trim((string) $q) !== '' && trim((string) $a) !== '') {
            $hasRows = true;
            break;
        }
    }
@endphp

@if ($hasRows)
    <li rel="productFaq">
        <a class="tablink" rel="productFaq">{{ labels('front_messages.faq', 'FAQ') }}</a>
    </li>
@endif
