@props([
    'features' => [],
    'productName' => '',
])

@php
    // Normalise rows and drop any that have no feature label.
    $rows = [];
    foreach ($features as $feature) {
        $label = is_array($feature) ? ($feature['feature'] ?? '') : ($feature->feature ?? '');
        $value = is_array($feature) ? ($feature['details'] ?? '') : ($feature->details ?? '');
        if (trim((string) $label) !== '') {
            $rows[] = ['feature' => $label, 'details' => $value];
        }
    }
@endphp

@if (!empty($rows))
    <div class="ghs-key-features">
        {{-- <h5 class="ghs-key-features__title">
            {{ labels('front_messages.key_features_of', 'Key Features of') }} {{ $productName }}
        </h5> --}}
        <div class="table-responsive">
            <table class="ghs-key-features__table">
                <thead>
                    <tr>
                        <th>{{ labels('front_messages.feature', 'Feature') }}</th>
                        <th>{{ labels('front_messages.details', 'Details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <th scope="row">{{ $row['feature'] }}</th>
                            <td>{!! nl2br(e($row['details'])) !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @once
        <style>
            .ghs-key-features__title {
                color: var(--ghs-green, #21512b);
                font-weight: 700;
                margin-bottom: 1rem;
            }
            .ghs-key-features__table {
                width: 100%;
                border-collapse: collapse;
            }
            .ghs-key-features__table thead th {
                color: var(--ghs-green, #21512b);
                font-weight: 700;
                padding: 10px 12px 10px 0;
                border-bottom: 2px solid var(--ghs-green-light, #4e8c4a);
                text-align: left;
            }
            .ghs-key-features__table tbody th,
            .ghs-key-features__table tbody td {
                padding: 4px 12px 4px 0;
                border-bottom: 1px solid var(--ghs-green-light, #4e8c4a);
                vertical-align: top;
                text-align: left;
                font-weight: 400;
            }
            .ghs-key-features__table tbody th {
                color: var(--ghs-green-dark, #163a1e);
                font-weight: 600;
                width: 32%;
                padding-right: 20px;
            }
            .ghs-key-features__table tbody td {
                color: #3a3a3a;
            }
        </style>
    @endonce
@endif
