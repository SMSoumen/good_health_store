@props([
    'sections' => [],
])

@foreach ($sections as $index => $section)
    @php
        $title = is_array($section) ? ($section['title'] ?? '') : ($section->title ?? '');
        $content = is_array($section) ? ($section['content'] ?? '') : ($section->content ?? '');
    @endphp
    @continue(trim((string) $title) === '' || trim(strip_tags((string) $content)) === '')
    <li rel="product_section_{{ $index }}">
        <a class="tablink" rel="product_section_{{ $index }}">{{ $title }}</a>
    </li>
@endforeach
