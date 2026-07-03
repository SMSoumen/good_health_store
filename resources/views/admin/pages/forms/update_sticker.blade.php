@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.update_sticker', 'Update Sticker') }}
@endsection
@section('content')
    <x-admin.breadcrumb :title="labels('admin_labels.update_sticker', 'Update Sticker')" :subtitle="labels(
        'admin_labels.manage_reusable_product_stickers',
        'Create reusable stickers (icon + text) to attach to products',
    )" :breadcrumbs="[['label' => labels('admin_labels.update_sticker', 'Update Sticker')]]" />
    @php
        use App\Services\MediaService;
    @endphp
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-3">
                    {{ labels('admin_labels.update_sticker', 'Update Sticker') }}
                </h5>
                <div class="form-group">
                    <form action="{{ url('/admin/stickers/update/' . $data->id) }}" enctype="multipart/form-data"
                        method="POST" class="submit_form">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="text" class="form-label">{{ labels('admin_labels.text', 'Text') }}<span
                                            class="text-asterisks text-sm">*</span></label>
                                    <input type="text" class="form-control" placeholder="FSSAI Certified" name="text"
                                        value="{{ $data->text }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image">{{ labels('admin_labels.image', 'Image') }}
                                        <span class='text-asterisks text-sm'>*</span></label>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="file_upload_box border file_upload_border mt-4">
                                                    <div class="mt-2 text-center">
                                                        <a class="media_link" data-input="image" data-isremovable="0"
                                                            data-is-multiple-uploads-allowed="0" data-bs-toggle="modal"
                                                            data-bs-target="#media-upload-modal" value="Upload Photo">
                                                            <h4><i class='bx bx-upload'></i> Upload</h4>
                                                        </a>
                                                        <p class="image_recommendation">Recommended Size: 80 x 80 pixels</p>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($data->image && !empty($data->image))
                                                <div class="col-md-6">
                                                    <label for="" class="text-danger">*Only Choose When Update is
                                                        necessary</label>
                                                    <div class="container-fluid row image-upload-section">
                                                        <div
                                                            class="col-md-8 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                            <div class='image-upload-div'>
                                                                <img class="img-fluid mb-2"
                                                                    src="{{ route('admin.dynamic_image', [
                                                                        'url' => app(MediaService::class)->getMediaImageUrl($data->image),
                                                                        'width' => 120,
                                                                        'quality' => 90,
                                                                    ]) }}"
                                                                    alt="Not Found">
                                                            </div>
                                                            <input type="hidden" name="image"
                                                                value='{{ $data->image }}'>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 d-flex justify-content-end">
                            <button type="submit"
                                class="btn btn-sm btn-primary submit_button">{{ labels('admin_labels.update_sticker', 'Update Sticker') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
