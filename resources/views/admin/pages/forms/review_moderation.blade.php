@extends('admin/layout')
@section('title')
    {{ $page_title }}
@endsection
@section('content')
    <x-admin.breadcrumb :title="$page_title" :subtitle="labels(
        'admin_labels.manage_product_reviews',
        'Approve, reject or remove customer reviews',
    )" :breadcrumbs="[['label' => labels('admin_labels.review_moderation', 'Review Moderation')], ['label' => $page_title]]" />

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <section class="overview-data">
                    <div class="card content-area p-4 ">
                        <div class="row align-items-center d-flex heading mb-5">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 col-lg-6">
                                        <h4>{{ $page_title }}</h4>
                                    </div>
                                    <div class="col-md-12 col-lg-6 d-flex justify-content-end mt-md-0 mt-sm-2">
                                        <div class="input-group me-2 search-input-grp ">
                                            <span class="search-icon"><i class='bx bx-search-alt'></i></span>
                                            <input type="text" data-table="{{ $table_id }}"
                                                class="form-control searchInput" placeholder="{{ labels('admin_labels.search', 'Search') }}">
                                            <span
                                                class="input-group-text">{{ labels('admin_labels.search', 'Search') }}</span>
                                        </div>
                                        <a class="btn me-2" id="tableFilter" data-bs-toggle="offcanvas"
                                            data-bs-target="#columnFilterOffcanvas" data-table="{{ $table_id }}"
                                            dateFilter='false' orderStatusFilter='false' paymentMethodFilter='false'
                                            orderTypeFilter='false'><i class='bx bx-filter-alt'></i></a>
                                        <a class="btn me-2" id="tableRefresh"data-table="{{ $table_id }}"><i
                                                class='bx bx-refresh'></i></a>
                                        <div class="dropdown">
                                            <a class="btn dropdown-toggle export-btn" type="button"
                                                id="exportOptionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class='bx bx-download'></i>
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="exportOptionsDropdown">
                                                <li><button class="dropdown-item" type="button"
                                                        onclick="exportTableData('{{ $table_id }}','csv')">CSV</button>
                                                </li>
                                                <li><button class="dropdown-item" type="button"
                                                        onclick="exportTableData('{{ $table_id }}','json')">JSON</button>
                                                </li>
                                                <li><button class="dropdown-item" type="button"
                                                        onclick="exportTableData('{{ $table_id }}','excel')">Excel</button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-primary btn-sm delete_selected_data"
                                    data-table-id="{{ $table_id }}"
                                    data-delete-url="{{ $delete_url }}">{{ labels('admin_labels.delete_selected', 'Delete Selected') }}</button>
                            </div>
                            <div class="col-md-12">
                                <div class="pt-0">
                                    <div class="table-responsive">
                                        <table class='table' id="{{ $table_id }}" data-toggle="table"
                                            data-loading-template="loadingTemplate"
                                            data-url="{{ $list_url }}" data-click-to-select="true"
                                            data-side-pagination="server" data-pagination="true"
                                            data-page-list="[5, 10, 20, 50, 100, 200]" data-search="false"
                                            data-show-columns="false" data-show-refresh="false"
                                            data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                                            data-mobile-responsive="true" data-toolbar="" data-show-export="false"
                                            data-maintain-selected="true" data-export-types='["txt","excel"]'
                                            data-query-params="queryParams">
                                            <thead>
                                                <tr>
                                                    <th data-checkbox="true" data-field="delete-checkbox">
                                                        <input name="select_all" type="checkbox">
                                                    </th>
                                                    <th data-field="id" data-sortable="true">
                                                        {{ labels('admin_labels.id', 'ID') }}
                                                    </th>
                                                    <th data-field="product" data-sortable="false">
                                                        {{ labels('admin_labels.product', 'Product') }}
                                                    </th>
                                                    <th data-field="user" data-sortable="false">
                                                        {{ labels('admin_labels.user', 'User') }}
                                                    </th>
                                                    <th data-field="rating" data-sortable="true">
                                                        {{ labels('admin_labels.rating', 'Rating') }}
                                                    </th>
                                                    <th data-field="title" data-sortable="false">
                                                        {{ labels('admin_labels.title', 'Title') }}
                                                    </th>
                                                    <th data-field="comment" data-sortable="false">
                                                        {{ labels('admin_labels.comment', 'Comment') }}
                                                    </th>
                                                    <th data-field="images" data-sortable="false">
                                                        {{ labels('admin_labels.images', 'Images') }}
                                                    </th>
                                                    <th data-field="status" data-sortable="false">
                                                        {{ labels('admin_labels.status', 'Status') }}
                                                    </th>
                                                    <th data-field="created_at" data-sortable="true">
                                                        {{ labels('admin_labels.submitted_on', 'Submitted On') }}
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
@endsection
