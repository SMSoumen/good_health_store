@props([
    'features' => [],
])

@php
    // On a validation re-render, prefer the previously submitted rows so the
    // admin/seller doesn't lose what they typed. Otherwise use the saved rows.
    $oldRows = old('key_features');
    $source = !empty($oldRows) ? $oldRows : $features;

    // Normalise to a simple array of ['feature' => ..., 'details' => ...]
    $rows = [];
    foreach ($source as $feature) {
        $rows[] = [
            'feature' => is_array($feature) ? ($feature['feature'] ?? '') : ($feature->feature ?? ''),
            'details' => is_array($feature) ? ($feature['details'] ?? '') : ($feature->details ?? ''),
        ];
    }
@endphp

<div class="card p-5 mt-4">
    <div class="col col-xxl-12">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">{{ labels('admin_labels.key_features', 'Key Features') }}</h6>
            <button type="button" class="btn btn-sm btn-primary" id="add_key_feature_btn">
                <i class="fas fa-plus"></i> {{ labels('admin_labels.add_more', 'Add More') }}
            </button>
        </div>
        <p class="text-muted small mt-1 mb-3">
            {{ labels('admin_labels.key_features_hint', 'Add feature rows (e.g. Geographic Origin, Floral Source) that will show on the product details page.') }}
        </p>

        <div id="key_features_wrapper" class="row">
            @forelse ($rows as $index => $row)
                <div class="key-feature-row col-12 mb-3" data-index="{{ $index }}">
                    <div class="row align-items-start">
                        <div class="col-md-4">
                            <label class="form-label">{{ labels('admin_labels.feature', 'Feature') }}</label>
                            <input type="text" class="form-control"
                                name="key_features[{{ $index }}][feature]"
                                value="{{ $row['feature'] }}"
                                placeholder="{{ labels('admin_labels.feature', 'Feature') }}" />
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">{{ labels('admin_labels.details', 'Details') }}</label>
                            <textarea class="form-control" rows="2"
                                name="key_features[{{ $index }}][details]"
                                placeholder="{{ labels('admin_labels.details', 'Details') }}">{{ $row['details'] }}</textarea>
                        </div>
                        <div class="col-md-1 d-flex align-items-end justify-content-center" style="padding-top: 30px;">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-key-feature">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="key-feature-row col-12 mb-3" data-index="0">
                    <div class="row align-items-start">
                        <div class="col-md-4">
                            <label class="form-label">{{ labels('admin_labels.feature', 'Feature') }}</label>
                            <input type="text" class="form-control" name="key_features[0][feature]"
                                placeholder="{{ labels('admin_labels.feature', 'Feature') }}" />
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">{{ labels('admin_labels.details', 'Details') }}</label>
                            <textarea class="form-control" rows="2" name="key_features[0][details]"
                                placeholder="{{ labels('admin_labels.details', 'Details') }}"></textarea>
                        </div>
                        <div class="col-md-1 d-flex align-items-end justify-content-center" style="padding-top: 30px;">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-key-feature">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

@once
    <script>
        (function () {
                document.addEventListener('click', function (e) {
                    // Add a new row
                    if (e.target.closest('#add_key_feature_btn')) {
                        var wrapper = document.getElementById('key_features_wrapper');
                        if (!wrapper) return;

                        // Determine the next index based on existing rows
                        var rows = wrapper.querySelectorAll('.key-feature-row');
                        var maxIndex = -1;
                        rows.forEach(function (row) {
                            var idx = parseInt(row.getAttribute('data-index'), 10);
                            if (!isNaN(idx) && idx > maxIndex) maxIndex = idx;
                        });
                        var newIndex = maxIndex + 1;

                        var html = '' +
                            '<div class="key-feature-row col-12 mb-3" data-index="' + newIndex + '">' +
                                '<div class="row align-items-start">' +
                                    '<div class="col-md-4">' +
                                        '<label class="form-label">{{ labels('admin_labels.feature', 'Feature') }}</label>' +
                                        '<input type="text" class="form-control" name="key_features[' + newIndex + '][feature]" placeholder="{{ labels('admin_labels.feature', 'Feature') }}" />' +
                                    '</div>' +
                                    '<div class="col-md-7">' +
                                        '<label class="form-label">{{ labels('admin_labels.details', 'Details') }}</label>' +
                                        '<textarea class="form-control" rows="2" name="key_features[' + newIndex + '][details]" placeholder="{{ labels('admin_labels.details', 'Details') }}"></textarea>' +
                                    '</div>' +
                                    '<div class="col-md-1 d-flex align-items-end justify-content-center" style="padding-top: 30px;">' +
                                        '<button type="button" class="btn btn-sm btn-outline-danger remove-key-feature"><i class="fas fa-trash"></i></button>' +
                                    '</div>' +
                                '</div>' +
                            '</div>';

                        wrapper.insertAdjacentHTML('beforeend', html);
                    }

                    // Remove a row
                    if (e.target.closest('.remove-key-feature')) {
                        var row = e.target.closest('.key-feature-row');
                        var wrapper = document.getElementById('key_features_wrapper');
                        if (row && wrapper) {
                            // Keep at least one empty row available
                            if (wrapper.querySelectorAll('.key-feature-row').length > 1) {
                                row.remove();
                            } else {
                                row.querySelectorAll('input, textarea').forEach(function (el) { el.value = ''; });
                            }
                        }
                    }
                });
            })();
    </script>
@endonce
