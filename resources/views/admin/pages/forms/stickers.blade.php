@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.stickers', 'Stickers') }}
@endsection
@section('content')
    <x-admin.breadcrumb :title="labels('admin_labels.stickers', 'Stickers')" :subtitle="labels(
        'admin_labels.manage_reusable_product_stickers',
        'Create reusable stickers (icon + text) to attach to products',
    )" :breadcrumbs="[['label' => labels('admin_labels.stickers', 'Stickers')]]" />

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">
                            {{ labels('admin_labels.add_sticker', 'Add Sticker') }}
                        </h5>
                    </div>
                    <form action="{{ route('stickers.store') }}" class="submit_form" enctype="multipart/form-data"
                        method="POST">
                        @csrf
                        <div class="card-body pt-0">
                            <div class="mb-3">
                                <label for="text" class="form-label">{{ labels('admin_labels.text', 'Text') }}<span
                                        class="text-asterisks text-sm">*</span></label>
                                <input type="text" name="text" class="form-control"
                                    placeholder="{{ labels('admin_labels.sticker_text_placeholder', 'e.g. FSSAI Certified') }}"
                                    value="">
                            </div>

                            <label for="" class="form-label">{{ labels('admin_labels.image', 'Image') }}<span
                                    class="text-asterisks text-sm">*</span></label>
                            <div class="col-md-12">
                                <div class="row form-group">
                                    <div class="col-md-6 file_upload_box border file_upload_border mt-4">
                                        <div class="mt-2">
                                            <div class="col-md-12 text-center">
                                                <div>
                                                    <a class="media_link" data-input="image" data-isremovable="0"
                                                        data-is-multiple-uploads-allowed="0" data-bs-toggle="modal"
                                                        data-bs-target="#media-upload-modal" value="Upload Photo">
                                                        <h4><i class='bx bx-upload'></i> Upload</h4>
                                                    </a>
                                                    <p class="image_recommendation">Recommended Size: 80 x 80 pixels</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 container-fluid row mt-3 image-upload-section">
                                        <div
                                            class="col-md-12 col-sm-12 p-3 mb-5 bg-white rounded m-4 text-center grow image d-none">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="reset"
                                        class="btn mx-2 reset_button">{{ labels('admin_labels.reset', 'Reset') }}</button>
                                    <button type="submit"
                                        class="btn btn-primary submit_button">{{ labels('admin_labels.add_sticker', 'Add Sticker') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {{-- table --}}
            <div class="col-md-12 col-xl-8 mt-xl-0 mt-md-2">
                <section class="overview-data">
                    <div class="card content-area p-4">
                        <div class="row align-items-center d-flex heading mb-5">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>{{ labels('admin_labels.manage_stickers', 'Manage Stickers') }}</h4>
                                    </div>

                                    <div class="col-sm-12 d-flex justify-content-end mt-md-0 mt-sm-2">
                                        <div class="input-group me-2 search-input-grp">
                                            <span class="search-icon"><i class='bx bx-search-alt'></i></span>
                                            <input type="text" data-table="admin_sticker_table"
                                                class="form-control searchInput"
                                                placeholder="{{ labels('admin_labels.search', 'Search') }}">
                                            <span
                                                class="input-group-text">{{ labels('admin_labels.search', 'Search') }}</span>
                                        </div>
                                        <a class="btn me-2" id="tableFilter" data-bs-toggle="offcanvas"
                                            data-bs-target="#columnFilterOffcanvas" data-table="admin_sticker_table"
                                            StatusFilter='true'><i class='bx bx-filter-alt'></i></a>
                                        <a class="btn me-2" id="tableRefresh" data-table="admin_sticker_table"><i
                                                class='bx bx-refresh'></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-primary btn-sm delete_selected_data"
                                    data-table-id="admin_sticker_table"
                                    data-delete-url="{{ route('stickers.delete') }}">{{ labels('admin_labels.delete_selected', 'Delete Selected') }}</button>
                            </div>
                            <div class="col-md-12">
                                <div class="pt-0">
                                    <div class="table-responsive">
                                        <table class='table' id="admin_sticker_table" data-toggle="table"
                                            data-loading-template="loadingTemplate"
                                            data-url="{{ route('stickers.list') }}" data-click-to-select="true"
                                            data-side-pagination="server" data-pagination="true"
                                            data-page-list="[5, 10, 20, 50, 100, 200]" data-search="false"
                                            data-show-columns="false" data-show-refresh="false" data-trim-on-search="false"
                                            data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true"
                                            data-toolbar="" data-show-export="false" data-maintain-selected="true"
                                            data-query-params="sticker_query_params">
                                            <thead>
                                                <tr>
                                                    <th data-checkbox="true" data-field="delete-checkbox">
                                                        <input name="select_all" type="checkbox">
                                                    </th>
                                                    <th data-field="id" data-sortable="true" data-visible="true">
                                                        {{ labels('admin_labels.id', 'ID') }}
                                                    </th>
                                                    <th class="d-flex justify-content-center" data-field="image"
                                                        data-sortable="false">
                                                        {{ labels('admin_labels.image', 'Image') }}
                                                    </th>
                                                    <th data-field="text" data-disabled="1" data-sortable="false">
                                                        {{ labels('admin_labels.text', 'Text') }}
                                                    </th>
                                                    <th data-field="status" data-sortable="false">
                                                        {{ labels('admin_labels.status', 'Status') }}
                                                    </th>
                                                    <th data-field="operate" data-sortable="false">
                                                        {{ labels('admin_labels.action', 'Action') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
        function sticker_query_params(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search,
                status: p.status,
            };
        }

        // The global submit handler only calls form.reset(), which does not remove the
        // image preview injected by the media picker (the <img> + hidden name="image").
        // Clear that preview after a successful add so the next sticker starts fresh.
        $(document).ajaxSuccess(function (event, xhr, settings) {
            var storeUrl = "{{ route('stickers.store') }}";
            if (!settings.url || settings.url.indexOf(storeUrl) === -1) return;
            if (xhr.responseJSON && xhr.responseJSON.error) return;

            var $form = $('form.submit_form[action="' + storeUrl + '"]');
            $form.find('.image-upload-section').html(
                '<div class="col-md-12 col-sm-12 p-3 mb-5 bg-white rounded m-4 text-center grow image d-none"></div>'
            );
        });
    </script>
@endsection
