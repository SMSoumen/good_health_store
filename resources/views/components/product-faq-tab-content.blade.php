@props([
    'faqs' => [],
])

@php
    $rows = [];
    foreach ($faqs as $faq) {
        $q = is_array($faq) ? ($faq['question'] ?? '') : ($faq->question ?? '');
        $a = is_array($faq) ? ($faq['answer'] ?? '') : ($faq->answer ?? '');
        if (trim((string) $q) !== '' && trim((string) $a) !== '') {
            $rows[] = ['q' => $q, 'a' => $a];
        }
    }
@endphp

@if (!empty($rows))
    <h3 class="tabs-ac-style d-md-none" rel="productFaq">{{ labels('front_messages.faq', 'FAQ') }}</h3>
    <div id="productFaq" class="tab-content">
        <div class="product-description">
            <div class="ghs-product-faq">
                @foreach ($rows as $index => $row)
                    <details class="ghs-faq-item" @if ($index === 0) open @endif>
                        <summary class="ghs-faq-q">{{ $row['q'] }}</summary>
                        <div class="ghs-faq-a">{!! nl2br(e($row['a'])) !!}</div>
                    </details>
                @endforeach
            </div>
        </div>
    </div>

    @once
        <style>
            .ghs-product-faq {
                width: 100%;
                max-width: 1200px;
                margin-left: auto;
                margin-right: auto;
            }
            .ghs-faq-item {
                border: 1px solid #e3e3e3;
                border-radius: 8px;
                margin-bottom: 12px;
                padding: 0 16px;
                background: #fff;
            }
            .ghs-faq-q {
                list-style: none;
                cursor: pointer;
                font-weight: 600;
                color: #3f5b3a;
                padding: 14px 28px 14px 0;
                position: relative;
            }
            .ghs-faq-q::-webkit-details-marker {
                display: none;
            }
            .ghs-faq-q::after {
                content: "+";
                position: absolute;
                right: 4px;
                top: 50%;
                transform: translateY(-50%);
                font-size: 20px;
                line-height: 1;
                color: #3f5b3a;
            }
            .ghs-faq-item[open] > .ghs-faq-q {
                color: #c358a5;
            }
            .ghs-faq-item[open] > .ghs-faq-q::after {
                content: "\2212"; /* minus */
                color: #c358a5;
            }
            .ghs-faq-a {
                padding: 0 0 16px;
                color: #3a3a3a;
                line-height: 1.6;
            }
        </style>
    @endonce
@endif
