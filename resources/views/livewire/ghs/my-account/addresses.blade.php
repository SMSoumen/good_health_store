@props(['user_info'])
<?php
use App\Models\City;
use App\Services\TranslationService;
$bread_crumb['page_main_bread_crumb'] = labels('front_messages.addresses', 'Addresses');
$language_code = app(TranslationService::class)->getLanguageCode();
?>

<div>
    <div id="page-content">
        <x-utility.breadcrumbs.breadcrumbTwo :$bread_crumb />
        <div class="container-fluid">
            <div class="row">
                <x-utility.my_account_slider.account_slider :$user_info />
                <div class="col-12 col-sm-12 col-md-12 col-lg-9">
                    <div class="dashboard-conten h-100">
                        <div class="h-100" id="address">
                            <div class="address-card mt-0 h-100">
                                <div class="top-sec d-flex-justify-center justify-content-between mb-4">
                                    <h2 class="mb-0">{{ labels('front_messages.address_book', 'Address Book') }}</h2>
                                    <button type="button" wire:click="resetForm" class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#addNewModal">
                                        <ion-icon name="add-outline" class="me-1 fs-5"></ion-icon>
                                        {{ labels('front_messages.add_new', 'Add New') }}
                                    </button>
                                </div>

                                <div class="address-book-section dashboard-content">
                                    @if (count($addresses) < 1)
                                        <div class="d-flex flex-column justify-content-center align-items-center py-5">
                                            <div class="opacity-50">
                                                <ion-icon name="location-outline"
                                                    class="address-location-icon text-muted"></ion-icon>
                                            </div>
                                            <div class="fs-6 fw-500">
                                                {{ labels('front_messages.delivery_address_not_added', 'Delivery Address is Not Added Yet') }}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row g-4 row-cols-lg-3 row-cols-md-2 row-cols-sm-2 row-cols-1">
                                        @foreach ($addresses as $address)
                                            @php $address = json_decode(json_encode($address), true); @endphp
                                            <div
                                                class="address-select-box {{ $address['is_default'] == 1 ? 'active' : '' }}">
                                                <div class="address-box bg-block">
                                                    <div class="top d-flex-justify-center justify-content-between mb-3">
                                                        <h5 class="m-0">{{ $address['name'] }}</h5>
                                                        <span class="product-labels start-auto end-0">
                                                            <span class="lbl pr-label1">{{ $address['type'] }}</span>
                                                        </span>
                                                    </div>
                                                    <div class="middle">
                                                        <div class="address mb-2 text-muted">
                                                            <address class="m-0">
                                                                {{ $address['landmark'] }}<br />
                                                                {{ $address['address'] }},
                                                                {{ app(TranslationService::class)->getDynamicTranslation(City::class, 'name', $address['city_id'], $language_code) }},
                                                                <br />{{ $address['state'] }} ,
                                                                {{ $address['pincode'] }}
                                                            </address>
                                                        </div>
                                                        <div class="number">
                                                            <p>
                                                                {{ labels('front_messages.mobile', 'Mobile') }}:
                                                                <a
                                                                    href="tel:{{ $address['country_code'] }}{{ $address['mobile'] }}">
                                                                    (+{{ $address['country_code'] }})
                                                                    &nbsp; {{ $address['mobile'] }}
                                                                </a>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="bottom d-flex-justify-center justify-content-between">
                                                        <button type="button" wire:click="edit({{ $address['id'] }})"
                                                            class="bottom-btn btn btn-gray btn-sm"
                                                            data-bs-toggle="modal" data-bs-target="#addNewModal">
                                                            {{ labels('front_messages.edit', 'Edit') }}
                                                        </button>

                                                        <button wire:click.prevent="setDefault({{ $address['id'] }})"
                                                            class="bottom-btn btn btn-sm {{ $address['is_default'] == 1 ? '' : 'btn-gray' }}">
                                                            {{ labels('front_messages.default', 'Default') }}
                                                        </button>
                                                        <button class="bottom-btn btn btn-gray btn-sm delete_address"
                                                            data-address-id="{{ $address['id'] }}">
                                                            {{ labels('front_messages.remove', 'Remove') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Modal still inside same root but ignored by Livewire -->
    <div wire:ignore class="modal fade" id="addNewModal" tabindex="-1" aria-labelledby="addNewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="addNewModalLabel">
                        {{ labels('front_messages.address_details', 'Address details') }}
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-row row-cols-lg-2 row-cols-md-2 row-cols-sm-1 row-cols-1">
                        <div class="form-group">
                            <input wire:model='name' name="name" placeholder="Name" id="name" type="text" />
                        </div>
                        <div class="form-group" wire:ignore>
                            <select name="type" id="type">
                                <option value="">
                                    {{ labels('front_messages.select_address_type', 'Select Address type') }}</option>
                                <option value="home">{{ labels('front_messages.home', 'Home') }}</option>
                                <option value="office">{{ labels('front_messages.office', 'Office') }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input wire:model='mobile' name="mobile" placeholder="Mobile number" id="mobile"
                                type="number">
                        </div>
                        <div class="form-group">
                            <input wire:model='alternate_mobile' name="alternate_mobile"
                                placeholder="Alternative mobile number" id="alternate_mobile" type="number">
                        </div>
                        <div class="form-group">
                            <input wire:model='address' name="address" placeholder="Address" id="form_address"
                                type="text" />
                        </div>
                        <div class="form-group">
                            <input wire:model='landmark' name="landmark" placeholder="Landmark" id="landmark"
                                type="text" />
                        </div>
                        <div class="form-group city_list_div">
                            <div wire:ignore>
                                <select class="col-md-12 form-control city_list" id="city_list"
                                    name="city"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <input wire:model='pincode' name="pincode" placeholder="Post Code" id="postcode"
                                type="text" />
                        </div>
                        <div class="form-group">
                            <input wire:model='state' name="state" placeholder="State" id="state"
                                type="text" />
                        </div>
                        <div class="form-group country_list_div">
                            <div wire:ignore>
                                <select class="col-md-12 form-control country_list" id="country_list"
                                    name="country"></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <input wire:model='latitude' name="latitude" placeholder="Latitude" id="latitude"
                                type="text">
                        </div>
                        <div class="form-group">
                            <input wire:model='longitude' name="longitude" placeholder="Longitude" id="longitude"
                                type="text">
                        </div>
                    </div>
                    <input type="hidden" name="edit_address_id" id="edit_address_id" value="">
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-primary add_address">
                            <span>{{ labels('front_messages.add_address', 'Add Address') }}</span>
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Sync Select2 changes with Livewire
        $('#type').on('change', function() {
            @this.set('type', $(this).val());
        });

        $('#city_list').on('change', function() {
            @this.set('city', $(this).val());
        });

        $('#country_list').on('change', function() {
            @this.set('country', $(this).val());
        });

        // Close modal listener
        window.addEventListener('close-modal', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addNewModal'));
            if (modal) {
                modal.hide();
            }
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        });
    </script>
@endpush
