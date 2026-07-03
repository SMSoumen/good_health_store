@props([
    'sections' => [],
    'count' => 6,
])

@php
    // On a validation re-render, prefer the previously submitted rows so the
    // admin/seller doesn't lose what they typed. Otherwise use the saved rows.
    $oldRows = old('sections');
    $source = !empty($oldRows) ? $oldRows : $sections;

    // Normalise to a simple 0-based indexed array keyed by slot.
    $rows = [];
    foreach ($source as $index => $section) {
        $rows[$index] = [
            'title' => is_array($section) ? ($section['title'] ?? '') : ($section->title ?? ''),
            'content' => is_array($section) ? ($section['content'] ?? '') : ($section->content ?? ''),
            'is_active' => is_array($section)
                ? (int) ($section['is_active'] ?? 1)
                : (int) ($section->is_active ?? 1),
        ];
    }
@endphp

<div class="card p-5 mt-4">
    <div class="col col-xxl-12">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">{{ labels('admin_labels.product_detail_sections', 'Product Detail Sections') }}</h6>
        </div>
        <p class="text-muted small mt-1 mb-3">
            {{ labels('admin_labels.product_detail_sections_hint', 'Add up to 6 extra sections. Each enabled section with a title shows as its own tab on the product details page.') }}
        </p>

        <div class="row">
            @for ($i = 0; $i < $count; $i++)
                @php
                    $row = $rows[$i] ?? ['title' => '', 'content' => '', 'is_active' => 1];
                    $editorId = 'product_section_content_' . $i;
                @endphp
                <div class="col-12 mb-4 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0 fw-600">
                            {{ labels('admin_labels.section', 'Section') }} {{ $i + 1 }}
                        </label>
                        <div class="form-check form-switch mb-0">
                            {{-- Ensure a value is always submitted for the toggle. --}}
                            <input type="hidden" name="sections[{{ $i }}][is_active]" value="0">
                            <input class="form-check-input" type="checkbox"
                                id="section_active_{{ $i }}"
                                name="sections[{{ $i }}][is_active]" value="1"
                                {{ $row['is_active'] ? 'checked' : '' }}>
                            <label class="form-check-label small" for="section_active_{{ $i }}">
                                {{ labels('admin_labels.enable', 'Enable') }}
                            </label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">{{ labels('admin_labels.title', 'Title') }}</label>
                        <input type="text" class="form-control" name="sections[{{ $i }}][title]"
                            value="{{ $row['title'] }}"
                            placeholder="{{ labels('admin_labels.section_title', 'Section Title') }}" />
                    </div>
                    <div>
                        <label class="form-label">{{ labels('admin_labels.content', 'Content') }}</label>
                        <textarea id="{{ $editorId }}" name="sections[{{ $i }}][content]"
                            class="form-control addr_editor"
                            placeholder="{{ labels('admin_labels.content', 'Content') }}">{{ $row['content'] }}</textarea>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
