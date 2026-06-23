@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.bulk_upload', 'Bulk Upload') }}
@endsection

@section('content')
<x-admin.breadcrumb
    :title="labels('admin_labels.bulk_upload', 'Bulk Upload')"
    :subtitle="labels('admin_labels.simplify_tasks_with_powerful_bulk_upload_capabilities','Simplify Tasks with Powerful Bulk Upload Capabilities.')"
    :breadcrumbs="[
        ['label' => labels('admin_labels.product', 'Product'), 'url' => route('admin.products.index')],
        ['label' => labels('admin_labels.bulk_upload', 'Bulk Upload')],
    ]"
/>

<div class="row">
    <!-- Upload Form -->
    <div class="col-md-12 col-lg-6">
        <div class="card">
            <form class="form-horizontal" action="{{ route('admin.product.bulk_upload') }}" method="POST" enctype="multipart/form-data" id="bulk_upload_form">
                @csrf
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        {{ labels('admin_labels.bulk_upload', 'Bulk Upload') }}
                    </h5>

                    <div class="mb-3">
                        <label for="type" class="form-label">
                            {{ labels('admin_labels.type', 'Type') }} <small>[upload/update]</small>
                            <span class="text-asterisks">*</span>
                        </label>
                        <select class="form-control form-select" name="type" id="type" required>
                            <option value="">Select</option>
                            <option value="upload">Upload</option>
                            <option value="update">Update</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="file" class="form-label">
                            {{ labels('admin_labels.file', 'File') }}
                            <span class="text-asterisks">*</span>
                        </label>
                        <input type="file" name="upload_file" class="form-control" accept=".csv" required>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="reset" class="btn btn-secondary">{{ labels('admin_labels.reset', 'Reset') }}</button>
                        <button type="submit" class="btn btn-primary">{{ labels('admin_labels.submit', 'Submit') }}</button>
                    </div>

                    <hr class="my-4">

                    <label class="form-label">
                        {{ labels('admin_labels.instruction_files', 'Instruction Files') }}
                    </label>

                    <div class="row mt-2">
                        <div class="col-md-6 mb-2">
                            <a href="{{ asset('storage/product-bulk-upload.zip') }}" download="product-bulk-upload.zip"
                               class="btn btn-outline-primary btn-sm w-100">
                                {{ labels('admin_labels.download', 'Download Example CSV') }}
                                <i class="fas fa-download ms-2"></i>
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="{{ asset('storage/bulk_upload_product.pdf') }}" download="bulk_upload_product.pdf"
                               class="btn btn-outline-primary btn-sm w-100">
                                {{ labels('admin_labels.download', 'Download Guide') }}
                                <i class="fas fa-download ms-2"></i>
                            </a>
                        </div>
                    </div>

                    <div id="upload_result" class="mt-3 text-center"></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Instruction Card -->
    {{-- <div class="col-md-12 col-lg-6 mt-md-4 mt-lg-0">
        <div class="card h-100">
            <div class="card-header">
                <strong>{{ labels('admin_labels.instructions', 'Instructions') }}</strong>
            </div>
            <div class="card-body">
                <ul class="list-unstyled ms-2">
                    <li class="mb-1">Read and follow instructions carefully while preparing data</li>
                    <li class="mb-1">Download and save the sample file to reduce errors</li>
                    <li class="mb-1">The file must be in .csv format</li>
                    <li class="mb-1">Images must have valid paths available in Media section</li>
                    <li><b>Ensure valid data entry to avoid upload failure</b></li>
                </ul>
            </div>
        </div>
    </div> --}}
</div>
@endsection
