@extends('admin/layout')

@section('title')
    {{ labels('admin_labels.bulk_upload', 'Multi Language Bulk Import') }}
@endsection

@section('content')
    <x-admin.breadcrumb :title="labels('admin_labels.bulk_upload', 'Multi Language Bulk Import')" :subtitle="labels(
        'admin_labels.simplify_tasks_with_powerful_bulk_upload_capabilities',
        'Simplify Tasks with Powerful Multi Language Bulk Import Capabilities.',
    )" :breadcrumbs="[
        ['label' => labels('admin_labels.language', 'Language')],
        ['label' => labels('admin_labels.bulk_upload', 'Multi Language Bulk Import')],
    ]" />

    <div class="row">
        <!-- Bulk Import Form -->
        <div class="col-md-12 col-lg-6">
            <div class="card shadow-sm border-0">
                <form id="translation_bulk_upload_form" action="{{ route('admin.translation_bulk_upload') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <h5 class="card-title mb-3 fw-semibold text-dark">
                            {{ labels('admin_labels.bulk_upload', 'Multi Language Bulk Import') }}
                        </h5>

                        <!-- Type Selection -->
                        <div class="mb-3">
                            <label for="type" class="form-label fw-medium">
                                {{ labels('admin_labels.type', 'Type') }}
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="type" id="type" >
                                <option value="">{{ labels('admin_labels.select', 'Select') }}</option>
                                <option value="brands">Brands</option>
                                <option value="categories">Categories</option>
                                <option value="cities">Cities</option>
                                <option value="stores">Stores</option>
                                <option value="taxes">Taxes</option>
                                <option value="products">Products</option>
                                <option value="combo_products">Combo Products</option>
                                <option value="offers">Offers</option>
                                <option value="offer_sliders">Offer Sliders</option>
                                <option value="promo_codes">Promo Codes</option>
                                <option value="sections">Featured Sections</option>
                                <option value="zones">Zones</option>
                                <option value="blog_categories">Blog Categories</option>
                                <option value="blogs">Blogs</option>
                                <option value="category_sliders">Category Sliders</option>
                            </select>
                        </div>

                        <!-- File Upload -->
                        <div class="mb-3">
                            <label for="upload_file" class="form-label fw-medium">
                                {{ labels('admin_labels.file', 'File') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="upload_file" id="upload_file" class="form-control" accept=".csv"
                                >
                            <small class="text-muted d-block mt-1">
                                {{ labels('admin_labels.only_csv_allowed', 'Only .csv files are allowed') }}
                            </small>
                        </div>

                        <!-- Form Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="reset" class="btn btn-light border reset_button">
                                {{ labels('admin_labels.reset', 'Reset') }}
                            </button>
                            <button type="submit" class="btn btn-primary submit_button">
                                {{ labels('admin_labels.submit', 'Submit') }}
                            </button>
                        </div>

                        <hr class="my-4">

                        <!-- Instruction & Sample Files -->
                        <label class="form-label fw-semibold">
                            {{ labels('admin_labels.instruction_files', 'Sample Files') }}
                        </label>

                        <div class="row mt-2">
                            {{-- <div class="col-md-4 mb-2">
                                <a href="{{ asset('storage/bulk_translation.zip') }}" download="bulk_translation.zip"
                                    class="btn btn-outline-primary btn-sm w-100 instructions_files">
                                    <i class="fas fa-download me-2"></i>
                                    {{ labels('admin_labels.download', 'Download Sample File') }}
                                </a>
                            </div> --}}
                            <div class="col-md-4 mb-2">
                                <a href="{{ url('admin/export/translation_csv') }}"
                                    class="btn btn-outline-primary btn-sm w-100 instructions_files">
                                    <i class="fas fa-download me-2"></i>
                                    {{ labels('admin_labels.download_data_for_translation', 'Download Data for Translation') }}
                                </a>
                            </div>
                            <div class="col-md-4 mb-2">
                                <a href="{{ asset('storage/bulk_upload_transactions.pdf') }}"
                                    download="bulk_upload_transactions.pdf"
                                    class="btn btn-outline-primary btn-sm w-100 instructions_files">
                                    {{ labels('admin_labels.download', 'Download Guide') }}
                                    <i class="fas fa-download ms-2"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Upload Result -->
                        <div id="upload_result" class="mt-4 text-center"></div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Instruction Card -->
        {{-- Uncomment this section if you want to display the translation guide --}}
        {{--
        <div class="col-md-12 col-lg-6 mt-4 mt-lg-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light fw-semibold">
                    {{ labels('admin_labels.instructions', 'Instructions') }}
                </div>
                <div class="card-body">
                    <ul class="list-unstyled ps-2 mb-0">
                        <li class="mb-2">1. Read and follow all instructions carefully before importing data.</li>
                        <li class="mb-2">2. Download and use the sample file to avoid format issues.</li>
                        <li class="mb-2">3. Ensure your translation data is in <b>.csv</b> format.</li>
                        <li class="mb-2">4. The “Download Data for Translation” option provides a ZIP containing all translation data (not store-specific).</li>
                        <li><b>5. Validate all fields before import to prevent errors.</b></li>
                    </ul>
                </div>
            </div>
        </div>
        --}}
    </div>
@endsection
