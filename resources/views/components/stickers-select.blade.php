@props([
    'selected' => [],
])

@php
    use App\Services\MediaService;

    // Shared, store-wide sticker master list (see getStickersList helper).
    $stickers = getStickersList();

    // Normalise selected ids to a flat int array for the checked comparison.
    $selectedIds = collect($selected)
        ->map(fn($id) => (int) $id)
        ->all();
@endphp

<div class="card p-5 mt-4">
    <div class="col col-xxl-12">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">{{ labels('admin_labels.stickers', 'Stickers') }}</h6>
        </div>
        <p class="text-muted small mt-1 mb-3">
            {{ labels('admin_labels.stickers_hint', 'Select the stickers (icon + text) to show on the product details page, just before the key features.') }}
        </p>

        @if ($stickers->isEmpty())
            <p class="text-muted small mb-0">
                {{ labels('admin_labels.no_stickers_found', 'No stickers found. Create stickers first to attach them here.') }}
            </p>
        @else
            <div class="row" id="stickers_wrapper">
                @foreach ($stickers as $sticker)
                    @php
                        $stickerImage = !empty($sticker->image)
                            ? app(MediaService::class)->getMediaImageUrl($sticker->image)
                            : '';
                    @endphp
                    <div class="col-md-4 col-sm-6 mb-3">
                        <label class="d-flex align-items-center gap-2 border rounded p-2 h-100 mb-0"
                            style="cursor:pointer;">
                            <input type="checkbox" class="form-check-input mt-0" name="stickers[]"
                                value="{{ $sticker->id }}"
                                {{ in_array((int) $sticker->id, $selectedIds, true) ? 'checked' : '' }}>
                            @if ($stickerImage)
                                <img src="{{ $stickerImage }}" alt="{{ $sticker->text }}" width="32" height="32"
                                    class="rounded" style="object-fit:contain;">
                            @endif
                            <span class="small">{{ $sticker->text }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
