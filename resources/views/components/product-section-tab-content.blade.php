@props([
    'sections' => [],
])

@foreach ($sections as $index => $section)
    @php
        $title = is_array($section) ? ($section['title'] ?? '') : ($section->title ?? '');
        $content = is_array($section) ? ($section['content'] ?? '') : ($section->content ?? '');
    @endphp
    @continue(trim((string) $title) === '' || trim(strip_tags((string) $content)) === '')
    <h3 class="tabs-ac-style d-md-none" rel="product_section_{{ $index }}">{{ $title }}</h3>
    <div id="product_section_{{ $index }}" class="tab-content">
        <div class="product-description">
            <div class="row">
                <div class="col-12 product-description">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </div>
@endforeach
