@props([
    'stickers' => [],
])

@php
    use App\Services\MediaService;

    // Normalise rows (support both array and object shapes) and drop empties.
    $rows = [];
    foreach ($stickers as $sticker) {
        $text = is_array($sticker) ? ($sticker['text'] ?? '') : ($sticker->text ?? '');
        $image = is_array($sticker) ? ($sticker['image'] ?? '') : ($sticker->image ?? '');
        if (trim((string) $text) === '' && trim((string) $image) === '') {
            continue;
        }
        $rows[] = [
            'text' => $text,
            'image' => !empty($image) ? app(MediaService::class)->dynamic_image($image, 120) : '',
        ];
    }
@endphp

@if (!empty($rows))
    <div class="ghs-stickers">
        @foreach ($rows as $row)
            <div class="ghs-sticker">
                @if (!empty($row['image']))
                    <span class="ghs-sticker__icon">
                        <img src="{{ $row['image'] }}" alt="{{ $row['text'] }}" loading="lazy" />
                    </span>
                @endif
                <span class="ghs-sticker__text">{{ $row['text'] }}</span>
            </div>
        @endforeach
    </div>

    @once
        <style>
            .ghs-stickers {
                display: flex;
                flex-wrap: wrap;
                align-items: stretch;
                gap: 8px;
                margin: 16px 0;
            }
            .ghs-sticker {
                flex: 1 1 0;
                min-width: 120px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-start;
                text-align: center;
                gap: 8px;
                padding: 14px 10px;
                background: #f6f6f6;
                border-radius: 10px;
            }
            .ghs-sticker__icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 48px;
                height: 48px;
                border-radius: 50%;
                background: var(--ghs-green-tint, #e5f0e4);
            }
            .ghs-sticker__icon img {
                width: 28px;
                height: 28px;
                object-fit: contain;
            }
            .ghs-sticker__text {
                font-size: 0.8rem;
                font-weight: 600;
                color: #3a3a3a;
                line-height: 1.2;
            }
            @media (max-width: 575.98px) {
                .ghs-sticker {
                    flex: 1 1 40%;
                }
            }
        </style>
    @endonce
@endif
