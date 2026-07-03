@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.bulk_gifting_inquiries', 'Bulk Gifting Inquiries') }}
@endsection
@section('content')
    <x-admin.breadcrumb :title="labels('admin_labels.bulk_gifting_inquiries', 'Bulk Gifting Inquiries')" :subtitle="labels(
        'admin_labels.manage_bulk_gifting_inquiries',
        'Manage bulk gifting order inquiries',
    )" :breadcrumbs="[['label' => labels('admin_labels.bulk_gifting_inquiries', 'Bulk Gifting Inquiries')]]" />

    <div class="col-md-12">
        <div
            class="col-12 {{ $user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view bulk_gifting_inquiries') ? '' : 'd-none' }}">
            <section class="overview-data">
                <div class="card content-area p-4 ">
                    <div class="row align-items-center d-flex heading mb-5">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-6">
                                    <h4>{{ labels('admin_labels.bulk_gifting_inquiries', 'Bulk Gifting Inquiries') }}</h4>
                                </div>
                                <div class="col-md-12 col-lg-6 d-flex justify-content-end mt-md-0 mt-sm-2">
                                    <div class="input-group me-2 search-input-grp ">
                                        <span class="search-icon"><i class='bx bx-search-alt'></i></span>
                                        <input type="text" data-table="admin_bulk_gifting_table"
                                            class="form-control searchInput"
                                            placeholder="{{ labels('admin_labels.search', 'Search') }}">
                                        <span class="input-group-text">{{ labels('admin_labels.search', 'Search') }}</span>
                                    </div>
                                    <a class="btn me-2" id="tableFilter" data-bs-toggle="offcanvas"
                                        data-bs-target="#columnFilterOffcanvas" data-table="admin_bulk_gifting_table"
                                        dateFilter='false' orderStatusFilter='false' paymentMethodFilter='false'
                                        orderTypeFilter='false'><i class='bx bx-filter-alt'></i></a>
                                    <a class="btn me-2" id="tableRefresh" data-table="admin_bulk_gifting_table"><i
                                            class='bx bx-refresh'></i></a>
                                    {{-- Server-side CSV export: covers ALL inquiries, not just the current page --}}
                                    <a class="btn btn-primary me-2" href="{{ route('admin.bulk_gifting_inquiries.export') }}">
                                        <i class='bx bx-download'></i>
                                        {{ labels('admin_labels.export_csv', 'Export CSV') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-outline-primary btn-sm delete_selected_data"
                                data-table-id="admin_bulk_gifting_table"
                                data-delete-url="{{ route('admin.bulk_gifting_inquiries.delete') }}">{{ labels('admin_labels.delete_selected', 'Delete Selected') }}</button>
                        </div>
                        <div class="col-md-12">
                            <div class="pt-0">
                                <div class="table-responsive">
                                    <table class='table' id="admin_bulk_gifting_table" data-toggle="table"
                                        data-loading-template="loadingTemplate"
                                        data-url="{{ route('admin.bulk_gifting_inquiries.list') }}"
                                        data-click-to-select="false" data-side-pagination="server" data-pagination="true"
                                        data-page-list="[5, 10, 20, 50, 100, 200]" data-search="false"
                                        data-show-columns="false" data-show-refresh="false" data-trim-on-search="false"
                                        data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true"
                                        data-toolbar="" data-show-export="false" data-maintain-selected="true"
                                        data-export-types='["txt","excel"]' data-query-params="queryParams">
                                        <thead>
                                            <tr>
                                                <th data-checkbox="true" data-field="delete-checkbox">
                                                    <input name="select_all" type="checkbox">
                                                </th>
                                                <th data-field="id" data-sortable="true">
                                                    {{ labels('admin_labels.id', 'ID') }}
                                                </th>
                                                <th data-field="name" data-sortable="true">
                                                    {{ labels('admin_labels.name', 'Name') }}
                                                </th>
                                                <th data-field="phone" data-sortable="false">
                                                    {{ labels('admin_labels.phone', 'Phone') }}
                                                </th>
                                                <th data-field="email" data-sortable="true">
                                                    {{ labels('admin_labels.email', 'Email') }}
                                                </th>
                                                <th data-field="company" data-sortable="false">
                                                    {{ labels('admin_labels.company', 'Company') }}
                                                </th>
                                                <th data-field="requirement_types" data-sortable="false">
                                                    {{ labels('admin_labels.requirement_type', 'Requirement Type') }}
                                                </th>
                                                <th data-field="estimated_quantity" data-sortable="false">
                                                    {{ labels('admin_labels.estimated_quantity', 'Estimated Quantity') }}
                                                </th>
                                                <th data-field="message" data-sortable="false">
                                                    {{ labels('admin_labels.message', 'Message') }}
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
@endsection
