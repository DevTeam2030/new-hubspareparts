@extends('layouts.front-end.app')

@section('title', translate('shipping_Address'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">

    <style>
        .custom-alert {
            margin-top: 10px;
            padding: 10px 12px;
            border: 1px solid transparent;
            border-radius: 5px;
            font-weight: 500;
        }
        .custom-alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        .custom-alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffeeba;
        }
        .custom-disabled {
            pointer-events: none;
            opacity: 0.5;
        }
        .dropdown-item.active, .dropdown-item:active {
            color: #140b49 !important;
        }
    </style>
@endpush

@section('content')
    @php($billingInputByCustomer = getWebConfig(name: 'billing_input_by_customer'))
    <div class="container py-4 rtl __inline-56 px-0 px-md-3 text-align-direction">
        <div class="row mx-max-md-0">
            <div class="col-md-12 mb-3">
                <h3 class="font-weight-bold text-center text-lg-left">{{ translate('checkout') }}</h3>
            </div>

            <section class="col-lg-8 px-max-md-0">
                <div class="checkout_details">
                    <div class="px-3 px-md-3">
                        @include('web-views.partials._checkout-steps', ['step' => 2])
                    </div>

                    @php($defaultLocation = getWebConfig(name: 'default_location'))

                    {{-- Shipping Address Section --}}
                    @if($physical_product_view)
                        <input type="hidden" id="physical_product" name="physical_product" value="{{ $physical_product_view ? 'yes' : 'no' }}">

                        <div class="px-3 px-md-0">
                            <h4 class="pb-2 mt-4 fs-18 text-capitalize">{{ translate('shipping_address') }}</h4>
                        </div>
                        @php($shippingAddresses = \App\Models\ShippingAddress::whereIn('customer_id', $relatedAccounts)->where('is_guest',0)->get() )
                        <form method="post" class="card __card" id="address-form">
                            <div class="card-body p-0">
                                <ul class="list-group">
                                    <li class="list-group-item add-another-address">
                                        @if($shippingAddresses->count() > 0)
                                            <div class="d-flex align-items-center justify-content-end gap-3">
                                                <div class="dropdown">
                                                    <button class="form-control dropdown-toggle text-capitalize" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        {{ translate('saved_address') }}
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right saved-address-dropdown scroll-bar-saved-address" aria-labelledby="dropdownMenuButton">
                                                        @foreach($shippingAddresses as $key => $address)
                                                            <div class="dropdown-item select_shipping_address {{ $key == 0 ? 'active' : '' }}" id="shippingAddress{{ $key }}">
                                                                <input type="hidden" class="selected_shippingAddress{{ $key }}" value="{{ $address }}">
                                                                <input type="hidden" name="shipping_method_id" value="{{ $address['id'] }}">
                                                                <div class="media gap-2">
                                                                    <div><i class="tio-briefcase"></i></div>
                                                                    <div class="media-body">
                                                                        <div class="mb-1 text-capitalize">{{ $address->address_type }}</div>
                                                                        <div class="text-muted fs-12 text-capitalize text-wrap">{{ $address->address }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div id="accordion">
                                            <div class="">
                                                <div class="mt-3">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label>{{ translate('contact_person_name') }}<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" name="contact_person_name" {{ $shippingAddresses->count() == 0 ? 'required' : '' }} id="name">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label>{{ translate('phone') }}<span class="text-danger">*</span></label>
                                                                <input type="tel" class="form-control phone-input-with-country-picker-3" id="phone" {{ $shippingAddresses->count() == 0 ? 'required' : '' }}>
                                                                <input type="hidden" id="shipping_phone_view" class="country-picker-phone-number-3 w-50" name="phone" readonly>
                                                            </div>
                                                        </div>
                                                        @if(!auth('customer')->check())
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label>{{ translate('email') }}<span class="text-danger">*</span></label>
                                                                    <input type="email" class="form-control" name="email" id="email" {{ $shippingAddresses->count() == 0 ? 'required' : '' }}>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label>{{ translate('address_type') }}</label>
                                                                <select class="form-control" name="address_type" id="address_type">
                                                                    <option value="permanent">{{ translate('permanent') }}</option>
                                                                    <option value="home">{{ translate('home') }}</option>
                                                                    <option value="others">{{ translate('others') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 d-flex flex-wrap gap-2">
                                                            <div class="form-group flex-grow-1">
                                                                <label>{{ translate('Governorate') }}<span class="text-danger">*</span></label>
                                                                <select name="governorate_id" id="governorate_id" class="form-control selectpicker" data-live-search="true" required>
                                                                    <option value="" selected disabled>{{ translate('choose_governorate') }}</option>
                                                                         @isset($governorates)
                                                                        <option value="{{ $governorates->id }}">{{ $governorates->name }}</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <div class="form-group" style="min-width: 200px;">
                                                                <label style="font-weight: 600;">{{ translate('Delivery Time') }}<span class="text-danger">*</span></label>
                                                                <select class="form-control" name="delivery_time" id="delivery_time_id" required >
                                                                    <option value="" selected disabled>{{ translate('اختر الوقت') }}</option>
                                                                </select>
                                                            </div>

                                                            <input type="hidden" name="delivery_date" id="delivery_date_hidden">
                                                            <div class="form-group" style="min-width: 200px;">
                                                                <label style="font-weight: 600;">{{ translate('Delivery Date') }}</label>
                                                                <div id="delivery_date_label" style="border: 1px solid #ccc; padding: 6px; min-height: 40px; border-radius: 4px;">

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label>{{ translate('city') }}<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" required name="city" id="city" {{ $shippingAddresses->count() == 0 ? 'required' : '' }}>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group mb-1">
                                                                <label>{{ translate('address') }}<span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="address" required name="address" {{ $shippingAddresses->count() == 0 ? 'required' : '' }}></textarea>
                                                                <span class="fs-14 text-danger font-semi-bold opacity-0 map-address-alert">
                                                                    {{ translate('note') }}: {{ translate('you_need_to_select_address_from_your_selected_country') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id="governorate-note-div" class="custom-alert custom-alert-warning" style="display: none;">
                                                        <p id="governorate-note" class="m-0"></p>
                                                    </div>

                                                    @if(getWebConfig('map_api_status') == 1)
                                                        <div class="form-group location-map-canvas-area map-area-alert-border">
                                                            <div class="__h-200px" id="location_map_canvas"></div>
                                                        </div>
                                                    @endif

                                                    <div class="d-flex gap-3 align-items-center">
                                                        <label class="form-check-label d-flex gap-2 align-items-center" id="save_address_label">
                                                            <input type="hidden" name="shipping_method_id" id="shipping_method_id" value="0">
                                                            @if(auth('customer')->check())
                                                                <input type="checkbox" name="save_address" id="save_address">
                                                                {{ translate('save_this_Address') }}
                                                            @endif
                                                        </label>
                                                    </div>

                                                    <input type="hidden" id="latitude" name="latitude" class="form-control d-inline" value="{{ $defaultLocation ? $defaultLocation['lat'] : 0 }}" required readonly>
                                                    <input type="hidden" name="longitude" class="form-control" id="longitude" value="{{ $defaultLocation ? $defaultLocation['lng'] : 0 }}" required readonly>

                                                    <div id="shipping-error-div" class="custom-alert custom-alert-danger" style="display: none;">
                                                        <p id="shipping-error-message" class="m-0"></p>
                                                    </div>

                                                    {{-- Hidden submit button --}}
                                                    <button type="submit" class="btn btn--primary d--none" id="address_submit"></button>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </form>

                        @if(!Auth::guard('customer')->check() && $web_config['guest_checkout_status'])
                            <div class="card __card mt-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center flex-wrap justify-content-between gap-3">
                                        <div class="min-h-45 form-check d-flex gap-3 align-items-center cursor-pointer user-select-none">
                                            <input type="checkbox" id="is_check_create_account" name="is_check_create_account" class="form-check-input mt-0" value="1">
                                            <label class="form-check-label font-weight-bold fs-13" for="is_check_create_account">
                                                {{ translate('Create_an_account_with_the_above_info') }}
                                            </label>
                                        </div>
                                        <div class="is_check_create_account_password_group d--none">
                                            <div class="d-flex gap-3 flex-wrap flex-sm-nowrap">
                                                <div class="w-100">
                                                    <div class="password-toggle rtl">
                                                        <input class="form-control text-align-direction" name="customer_password" type="password" id="customer_password" placeholder="{{ translate('new_Password') }}" required>
                                                        <label class="password-toggle-btn">
                                                            <input class="custom-control-input" type="checkbox">
                                                            <i class="tio-hidden password-toggle-indicator"></i>
                                                            <span class="sr-only">{{ translate('show_password') }}</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="w-100">
                                                    <div class="password-toggle rtl">
                                                        <input class="form-control text-align-direction" name="customer_confirm_password" type="password" id="customer_confirm_password" placeholder="{{ translate('confirm_Password') }}" required>
                                                        <label class="password-toggle-btn">
                                                            <input class="custom-control-input" type="checkbox">
                                                            <i class="tio-hidden password-toggle-indicator"></i>
                                                            <span class="sr-only">{{ translate('show_password') }}</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Billing Address Section --}}
                    @if($billingInputByCustomer)
                        <div>
                            <div class="billing-methods_label d-flex flex-wrap justify-content-between gap-2 mt-4 pb-3 px-3 px-md-0">
                                <h4 class="mb-0 fs-18 text-capitalize">{{ translate('billing_address') }}</h4>
                                @php($billingAddresses = \App\Models\ShippingAddress::where(['customer_id' => auth('customer')->id(), 'is_guest' => 0])->get())
                                @if($physical_product_view)
                                    <div class="form-check d-flex gap-3 align-items-center">
                                        <input type="checkbox" id="same_as_shipping_address" name="same_as_shipping_address" class="form-check-input action-hide-billing-address mt-0" {{ $billingInputByCustomer == 1 ? '' : 'checked' }}>
                                        <label class="form-check-label user-select-none" for="same_as_shipping_address">
                                            {{ translate('same_as_shipping_address') }}
                                        </label>
                                    </div>
                                @endif
                            </div>

                            @if(!$physical_product_view)
                                <div class="mb-3 alert--info">
                                    <div class="d-flex align-items-center gap-2">
                                        <img class="mb-1" src="{{ theme_asset('public/assets/front-end/img/icons/info-light.svg') }}" alt="Info">
                                        <span>{{ translate('When_you_input_all_the_required_information_for_this_billing_address_it_will_be_stored_for_future_purchases') }}</span>
                                    </div>
                                </div>
                            @endif

                            <form method="post" class="card __card" id="billing-address-form">
                                <div id="hide_billing_address" class="">
                                    <ul class="list-group">
                                        <li class="list-group-item action-billing-address-hide">
                                            @if($billingAddresses->count() > 0)
                                                <div class="d-flex align-items-center justify-content-end gap-3">
                                                    <div class="dropdown">
                                                        <button class="form-control dropdown-toggle text-capitalize" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            {{ translate('saved_address') }}
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right saved-address-dropdown scroll-bar-saved-address" aria-labelledby="dropdownMenuButton">
                                                            @foreach($billingAddresses as $key => $address)
                                                                <div class="dropdown-item select_billing_address {{ $key == 0 ? 'active' : '' }}" id="billingAddress{{ $key }}">
                                                                    <input type="hidden" class="selected_billingAddress{{ $key }}" value="{{ $address }}">
                                                                    <input type="hidden" name="billing_method_id" value="{{ $address['id'] }}">
                                                                    <div class="media gap-2">
                                                                        <div><i class="tio-briefcase"></i></div>
                                                                        <div class="media-body">
                                                                            <div class="mb-1 text-capitalize">{{ $address->address_type }}</div>
                                                                            <div class="text-muted fs-12 text-capitalize text-wrap">{{ $address->address }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div id="accordion">
                                                <div class="">
                                                    <div class="">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>{{ translate('contact_person_name') }}<span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control" name="billing_contact_person_name" id="billing_contact_person_name" {{ $billingAddresses->count()==0 ? 'required' : '' }}>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label>{{ translate('phone') }}<span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control phone-input-with-country-picker-2" id="billing_phone" {{ $billingAddresses->count()==0 ? 'required' : '' }}>
                                                                    <input type="hidden" class="country-picker-phone-number-2 w-50" name="billing_phone" readonly>
                                                                </div>
                                                            </div>
                                                            @if(!auth('customer')->check())
                                                                <div class="col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="exampleInputEmail1">{{ translate('email') }}<span class="text-danger">*</span></label>
                                                                        <input type="text" class="form-control" name="billing_contact_email" id="billing_contact_email" {{ $billingAddresses->count()==0 ? 'required' : '' }}>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label>{{ translate('address_type') }}</label>
                                                                    <select class="form-control" name="billing_address_type" id="billing_address_type">
                                                                        <option value="permanent">{{ translate('permanent') }}</option>
                                                                        <option value="home">{{ translate('home') }}</option>
                                                                        <option value="others">{{ translate('others') }}</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label>{{ translate('governorate') }}<span class="text-danger">*</span></label>
                                                                    <select name="billing_governorate_id" class="form-control selectpicker" data-live-search="true" id="billing_governorate_id">

                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>{{ translate('city') }}<span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control" id="billing_city" name="billing_city" {{ $billingAddresses->count()==0 ? 'required' : '' }}>
                                                                </div>
                                                            </div>
                                                            {{-- ZIP Code removed --}}
                                                            <div class="col-12">
                                                                <div class="form-group mb-1">
                                                                    <label>{{ translate('address') }}<span class="text-danger">*</span></label>
                                                                    <textarea class="form-control" id="billing_address" name="billing_address" {{ $billingAddresses->count()==0 ? 'required' : '' }}></textarea>
                                                                    <span class="fs-14 text-danger font-semi-bold opacity-0 map-address-alert">
                                                                        {{ translate('note') }}: {{ translate('you_need_to_select_address_from_your_selected_country') }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="billing_method_id" id="billing_method_id" value="0">
                                                        @if(auth('customer')->check())
                                                            <div class="d-flex gap-3 align-items-center">
                                                                <label class="form-check-label d-flex gap-2 align-items-center" id="save-billing-address-label">
                                                                    <input type="checkbox" name="save_address_billing" id="save_address_billing">
                                                                    {{ translate('save_this_Address') }}
                                                                </label>
                                                            </div>
                                                        @endif

                                                        <input type="hidden" id="billing_latitude" name="billing_latitude" class="form-control d-inline" value="{{ $defaultLocation ? $defaultLocation['lat'] : 0 }}" required readonly>
                                                        <input type="hidden" name="billing_longitude" class="form-control" id="billing_longitude" value="{{ $defaultLocation ? $defaultLocation['lng'] : 0 }}" required readonly>
                                                        <button type="submit" class="btn btn--primary d--none" id="address_submit"></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </form>
                        </div>

                        @if(!Auth::guard('customer')->check() && $web_config['guest_checkout_status'] && !$physical_product_view)
                            <div class="card __card mt-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center flex-wrap justify-content-between gap-3">
                                        <div class="min-h-45 form-check d-flex gap-3 align-items-center cursor-pointer user-select-none">
                                            <input type="checkbox" id="is_check_create_account" name="is_check_create_account" class="form-check-input mt-0" value="1">
                                            <label class="form-check-label font-weight-bold fs-13" for="is_check_create_account">
                                                {{ translate('Create_an_account_with_the_above_info') }}
                                            </label>
                                        </div>
                                        <div class="is_check_create_account_password_group d--none">
                                            <div class="d-flex gap-3 flex-wrap flex-sm-nowrap">
                                                <div class="w-100">
                                                    <div class="password-toggle rtl">
                                                        <input class="form-control text-align-direction" name="customer_password" type="password" id="customer_password" placeholder="{{ translate('new_Password') }}" required>
                                                        <label class="password-toggle-btn">
                                                            <input class="custom-control-input" type="checkbox">
                                                            <i class="tio-hidden password-toggle-indicator"></i>
                                                            <span class="sr-only">{{ translate('show_password') }}</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="w-100">
                                                    <div class="password-toggle rtl">
                                                        <input class="form-control text-align-direction" name="customer_confirm_password" type="password" id="customer_confirm_password" placeholder="{{ translate('confirm_Password') }}" required>
                                                        <label class="password-toggle-btn">
                                                            <input class="custom-control-input" type="checkbox">
                                                            <i class="tio-hidden password-toggle-indicator"></i>
                                                            <span class="sr-only">{{ translate('show_password') }}</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </section>

            @include('web-views.partials._order-summary')
        </div>
    </div>

    <span id="message-update-this-address" data-text="{{ translate('Update_this_Address') }}"></span>
    <span id="route-customer-choose-shipping-address-other" data-url="{{ route('customer.choose-shipping-address-other') }}"></span>
    <span id="default-latitude-address" data-value="{{ $defaultLocation ? $defaultLocation['lat'] : '-33.8688' }}"></span>
    <span id="default-longitude-address" data-value="{{ $defaultLocation ? $defaultLocation['lng'] : '151.2195' }}"></span>
    <span id="route-action-checkout-function" data-route="checkout-details"></span>
    <span id="system-country-restrict-status" data-value="{{ $country_restrict_status }}"></span>
@endsection

@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/shipping.js') }}"></script>

   <script>
        "use strict";

        // Convert 24h format to 12h with صباحاً/مساءً
        function formatTime12h(time24) {
            if(!time24) return '';
            // Possible format: "HH:MM:SS" or "HH:MM"
            let [hh, mm, ss] = time24.split(':');
            let hour = parseInt(hh) || 0;
            let minute = parseInt(mm) || 0;

            // Determine period
            let period = (hour >= 12) ? 'مساءً' : 'صباحاً';

            // Convert hour to 12h
            if(hour === 0) {
                hour = 12; // 00 => 12 صباحاً
            } else if(hour > 12) {
                hour -= 12; // 13 => 1 مساءً
            }

            let hourStr = hour.toString();
            let minuteStr = minute < 10 ? ('0' + minute) : minute;

            return hourStr + ':' + minuteStr + ' ' + period;
        }

        // Convert a time or time-range string to a Date object
        function parseTimeToDateObj(str) {
            let now = new Date();
            // Replace Arabic indicators with English
            let replaced = str.replace('صباحاً','AM').replace('مساءً','PM');
            let [time, period] = replaced.split(' ');
            if(!time) return now;

            let [hh, mm] = time.split(':');
            let hours = parseInt(hh) || 0;
            let minutes = parseInt(mm) || 0;

            // Convert to 24-hour
            if(period && period.toUpperCase() === 'PM' && hours < 12) {
                hours += 12;
            }
            if(period && period.toUpperCase() === 'AM' && hours === 12) {
                hours = 0;
            }

            now.setHours(hours, minutes, 0, 0);
            return now;
        }

        // Extract the end portion of a time range  for logic
        function parseTimeRangeEnd(timeRange) {
            let parts = timeRange.split('-');
            if(parts.length < 2) return parseTimeToDateObj(timeRange.trim());
            return parseTimeToDateObj(parts[1].trim());
        }

        // Auto-set the delivery date to today or tomorrow based on the chosen time
        function autoSetDeliveryDate(timeText) {
            let endTimeObj = parseTimeRangeEnd(timeText);
            let now = new Date();
            let todayStr = new Date().toISOString().split('T')[0];
            let tomorrowStr = new Date(Date.now() + 86400000).toISOString().split('T')[0];
            // If the end time is in the past, choose tomorrow, otherwise today
            let chosenDate = (endTimeObj < now) ? tomorrowStr : todayStr;

            const dateHidden = document.getElementById('delivery_date_hidden');
            const dateLabel = document.getElementById('delivery_date_label');
            if(dateHidden && dateLabel) {
                dateHidden.value = chosenDate;
                let labelText = (chosenDate === todayStr) ? '{{ translate("اليوم") }}' : '{{ translate("غدا") }}';
                dateLabel.textContent = labelText;

                // Save selected date in session
                fetch("{{ route('store-shipping-details-in-session') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ selected_delivery_date: chosenDate })
                }).then(() => {
                    console.log("Saved selected date:", chosenDate);
                }).catch(err => {
                    console.error("Error saving delivery date:", err);
                });
            }
        }

        // Disable/enable the "Proceed" button
        function disableProceed() {
            document.querySelectorAll('.proceed_to_next_button').forEach(btn => {
                btn.classList.add('custom-disabled');
                if(btn.tagName === 'A') {
                    btn.setAttribute('href', 'javascript:void(0)');
                }
            });
        }
        function enableProceed() {
            document.querySelectorAll('.proceed_to_next_button').forEach(btn => {
                btn.classList.remove('custom-disabled');
                if(btn.tagName === 'A') {
                    btn.setAttribute('href', '{{ route("checkout-payment") }}');
                }
            });
        }

        // Start disabled
        disableProceed();

        // Main function that fetches times, calculates shipping, and updates the map
    async function showOnMap() {
    const govSelect      = document.getElementById('governorate_id');
    const governorateId   = govSelect?.value;
    const governorateName = govSelect?.options[govSelect.selectedIndex]?.text ?? '';
    const city            = document.getElementById('city')?.value ?? '';
    const address         = document.getElementById('address')?.value ?? '';
    const timeSelect      = document.getElementById('delivery_time_id');
    const dateHidden      = document.getElementById('delivery_date_hidden');
    const dateLabel       = document.getElementById('delivery_date_label');

    // If no governorate, hide time picker and clear date, then check conditions
    if (!governorateId) {
        if (timeSelect) {
            timeSelect.style.display = 'none';
            timeSelect.innerHTML = '<option value="" selected disabled>{{ translate('choose_time') }}</option>';
        }
        if (dateHidden) dateHidden.value = '';
        if (dateLabel)  dateLabel.textContent = '';
        checkIfCanProceed();    // was disableProceed()
        return;
    }

    // 1) Fetch delivery times
    try {
        let oldTimeValue = timeSelect?.value || '';
     let timesUrl = "{{ route('delivery-times.by-governorate') }}"
              + "?governorate_id=" + governorateId;
        let timesRes     = await fetch(timesUrl);
        let timesData    = await timesRes.json();

        let noteDiv = document.getElementById('governorate-note-div');
        let noteP   = document.getElementById('governorate-note');
        if (timesData.note) {
            noteDiv.style.display = 'block';
            noteP.innerText = timesData.note;
        } else {
            noteDiv.style.display = 'none';
            noteP.innerText = '';
        }

        if (timeSelect) {
            timeSelect.innerHTML = '<option value="" selected disabled>{{ translate('choose_time') }}</option>';
            if (timesData.times && timesData.times.length > 0) {
                timeSelect.style.display = 'block';
                timesData.times.forEach(t => {
                    let option = document.createElement('option');
                    option.value = t.id;
                    option.text  = formatTime12h(t.start_time) + " - " + formatTime12h(t.end_time);
                    timeSelect.appendChild(option);
                });

                // If old time is still valid, restore it, reset date and re-enable proceed
                if (oldTimeValue) {
                    let found = timesData.times.find(t => t.id == oldTimeValue);
                    if (found) {
                        timeSelect.value = oldTimeValue;
                        autoSetDeliveryDate(timeSelect.options[timeSelect.selectedIndex].text);
                        checkIfCanProceed();
                    }
                }

                // If there's exactly one time, auto-select it
                if (timesData.times.length === 1) {
                    timeSelect.value = timesData.times[0].id;
                    fetch("{{ route('store-shipping-details-in-session') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ selected_delivery_time: timesData.times[0].id })
                    }).then(() => {
                        console.log("Saved time (auto):", timesData.times[0].id);
                        autoSetDeliveryDate(timeSelect.options[timeSelect.selectedIndex].text);
                        checkIfCanProceed();
                    }).catch(err => {
                        console.error("Error saving time:", err);
                    });
                }
            } else {
                timeSelect.style.display = 'none';
            }
        }
    } catch (err) {
        console.error("Error fetching times:", err);
    }

    // 2) If city or address is empty, skip geocoding/shipping and check conditions
    if (!city || !address) {
        checkIfCanProceed();    // was disableProceed()
        return;
    }

    // 3) Geocode the address
    const fullAddress = `${governorateName}, ${city}, ${address}`;
    const apiKey      = "{{ env('MAP_KEY') }}";
    const geoUrl      = `https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(fullAddress)}&key=${apiKey}`;
    try {
        const response = await fetch(geoUrl);
        const data     = await response.json();
        if (data.results && data.results.length > 0) {
            const location = data.results[0].geometry.location;
            document.getElementById('latitude').value  = location.lat;
            document.getElementById('longitude').value = location.lng;
            initMap(location.lat, location.lng);
        }
    } catch (error) {
        console.error("Error fetching coords:", error);
    }

    // 4) Calculate shipping cost
    try {
        const shippingErrorDiv = document.getElementById('shipping-error-div');
        const shippingErrorMsg = document.getElementById('shipping-error-message');
        if (shippingErrorDiv && shippingErrorMsg) {
            shippingErrorDiv.style.display = 'none';
            shippingErrorMsg.innerText = '';
        }

        let shippingUrl = "{{ route('areas.calculate-shipping') }}"
            + "?governorate_id=" + governorateId
            + "&lat=" + document.getElementById('latitude').value
            + "&lng=" + document.getElementById('longitude').value;
        let shippingRes  = await fetch(shippingUrl);
        let shippingData = await shippingRes.json();

        if (shippingData.success) {
            let shippingCost     = Math.ceil(shippingData.shipping_cost);
            let cartShippingCost = document.getElementById('cart-shipping-cost');
            if (cartShippingCost) {
                cartShippingCost.innerText = shippingCost.toFixed(2);
            }

            // Save shipping cost in session
            fetch("{{ route('store-shipping-details-in-session') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ shipping_cost: shippingCost })
            }).then(() => {
                console.log("Shipping cost saved:", shippingCost);
            }).catch(err => {
                console.error("Error saving shipping cost:", err);
            });

            // ===== Begin: Add Service Fee Calculation =====
            let serviceFeeRate = {{ getWebConfig('service_fee') ? getWebConfig('service_fee') : 0 }};
            let serviceFee     = shippingCost * serviceFeeRate / 100;
            let serviceFeeElem = document.getElementById('cart-service-fee');
            if (serviceFeeElem) {
                serviceFeeElem.innerText = serviceFee.toFixed(2);
            }
            fetch("{{ route('store-shipping-details-in-session') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ service_fee: serviceFee })
            }).then(() => {
                console.log("Service fee saved:", serviceFee);
                updateOrderTotal();
            }).catch(err => {
                console.error("Error saving service fee:", err);
            });
            // ===== End: Add Service Fee Calculation =====

            updateOrderTotal();
            checkIfCanProceed();
        } else {
            if (shippingErrorDiv && shippingErrorMsg) {
                shippingErrorDiv.style.display = 'block';
                shippingErrorMsg.innerText = shippingData.message;
            }
            disableProceed();
        }
    } catch (err) {
        console.error("Error calculating shipping:", err);
        disableProceed();
    }

    // Final check at end of function to re-enable if all conditions met
    checkIfCanProceed();
}



        // Update total in the cart summary
     function updateOrderTotal() {
            const subTotalText     = document.getElementById('cart-subtotal')?.innerText || '0';
            const taxText          = document.getElementById('cart-tax')?.innerText || '0';
            const shippingText     = document.getElementById('cart-shipping-cost')?.innerText || '0';
            let discountText       = document.getElementById('cart-discount')?.innerText || '0';
            const serviceFeeText   = document.getElementById('cart-service-fee')?.innerText || '0';


            discountText = discountText.replace(/−|-/g, '');

            const subTotal   = parseValue(subTotalText);
            const tax        = parseValue(taxText);
            const shipping   = parseValue(shippingText);
            const discount   = parseValue(discountText);
            const serviceFee = parseValue(serviceFeeText);

            const total = subTotal + tax + shipping + serviceFee - discount;

            const totalElem = document.getElementById('cart-total-amount');
            if (totalElem) totalElem.innerText = total.toFixed(2);
        }

        // Initialize the Google Map
        function initMap(lat, lng) {
            const mapCanvas = document.getElementById('location_map_canvas');
            if(!mapCanvas) return;
            const mapOptions = {
                center: { lat: parseFloat(lat), lng: parseFloat(lng) },
                zoom: 13
            };
            const map = new google.maps.Map(mapCanvas, mapOptions);
            new google.maps.Marker({
                position: { lat: parseFloat(lat), lng: parseFloat(lng) },
                map: map
            });
        }

        // Check if we can proceed to next step
        // Enhanced check: require name, phone, governorate, time, date, city, address
        function checkIfCanProceed() {
            const shippingErrorDiv = document.getElementById('shipping-error-div');
            if (shippingErrorDiv && shippingErrorDiv.style.display !== 'none') {
                disableProceed();
                return;
            }

            const name    = document.getElementById('name')?.value.trim();
            const phone   = document.getElementById('shipping_phone_view')?.value.trim();
            const gov     = document.getElementById('governorate_id')?.value;
            const timeEl  = document.getElementById('delivery_time_id');
            const timeVis = timeEl && timeEl.style.display !== 'none';
            const timeVal = timeEl?.value;
            const dateVal = document.getElementById('delivery_date_hidden')?.value;
            const city    = document.getElementById('city')?.value.trim();
            const addr    = document.getElementById('address')?.value.trim();

            if (
                name &&
                phone &&
                gov &&
                (!timeVis || timeVal) &&
                dateVal &&
                city &&
                addr
            ) {
                enableProceed();
            } else {
                disableProceed();
            }
        }



        // Governorate changes => fetch times,
        const govSelectEl = document.getElementById('governorate_id');
        if(govSelectEl) {
            govSelectEl.addEventListener('change', function() {
                showOnMap();
            });
        }

        // Delivery time changes => store in session + set date
        const timeSelectEl = document.getElementById('delivery_time_id');
        if(timeSelectEl) {
            let previousTimeValue = timeSelectEl.value;

            // If user clicks the same selected time, re-store it
            timeSelectEl.addEventListener('click', function() {
                if(timeSelectEl.value === previousTimeValue && timeSelectEl.value) {
                    fetch("{{ route('store-shipping-details-in-session') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ selected_delivery_time: timeSelectEl.value })
                    }).then(() => {
                        console.log("Saved same time on click:", timeSelectEl.value);
                        autoSetDeliveryDate(timeSelectEl.options[timeSelectEl.selectedIndex].text);
                    }).catch(err => {
                        console.error("Error saving shipping time:", err);
                    });
                }
            });

            // If user changes time, store it
            timeSelectEl.addEventListener('change', function() {
                fetch("{{ route('store-shipping-details-in-session') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ selected_delivery_time: timeSelectEl.value })
                }).then(() => {
                    console.log("Saved time on change:", timeSelectEl.value);
                    autoSetDeliveryDate(timeSelectEl.options[timeSelectEl.selectedIndex].text);
                }).catch(err => {
                    console.error("Error saving shipping time:", err);
                });
                previousTimeValue = timeSelectEl.value;
                checkIfCanProceed();
            });
        }

        // If user chooses a saved shipping address from the dropdown
        document.querySelectorAll('.select_shipping_address').forEach(item => {
            item.addEventListener('click', function(){
                let shippingAddressId = item.querySelector('input[name="shipping_method_id"]').value;
                fetch("{{ route('store-shipping-details-in-session') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ address_id: shippingAddressId })
                }).then(() => {
                    console.log("Saved shipping address id:", shippingAddressId);
                }).catch(err => {
                    console.error("Error saving shipping address id:", err);
                });
            });
        });

        // Validate on form submission => ensure time is chosen if visible
        document.getElementById('address-form').addEventListener('submit', function(e) {
            const timeSelect = document.getElementById('delivery_time_id');
            if(timeSelect && timeSelect.style.display !== 'none' && !timeSelect.value) {
                e.preventDefault();
                alert('{{ translate("يرجى اختيار وقت التوصيل") }}');
                timeSelect.focus();
            }
        });

        // run showOnMap whenever city/address changes, to recalc shipping
        const cityEl    = document.getElementById('city');
        const addressEl = document.getElementById('address');
        if(cityEl) {
            cityEl.addEventListener('blur', showOnMap);
            cityEl.addEventListener('change', showOnMap);
        }
        if(addressEl) {
            addressEl.addEventListener('blur', showOnMap);
            addressEl.addEventListener('change', showOnMap);
        }

        // Watch all required fields and re-check
        [
            'name',
            'shipping_phone_view',
            'governorate_id',
            'delivery_time_id',
            'delivery_date_hidden',
            'city',
            'address'
        ].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            ['input','change','blur'].forEach(evt => {
                el.addEventListener(evt, checkIfCanProceed);
            });
        });

        // Function to load delivery times on page load
        async function loadInitialDeliveryTimes() {
            const govSelect = document.getElementById('governorate_id');
            const timeSelect = document.getElementById('delivery_time_id');

            if (!govSelect || !timeSelect) return;

            const governorateId = govSelect.value;

            if (!governorateId) {
                // If no governorate selected, show empty time select but keep it visible
                timeSelect.innerHTML = '<option value="" selected disabled>{{ translate('اختر الوقت') }}</option>';
                return;
            }

            try {
                let timesUrl = "{{ route('delivery-times.by-governorate') }}"
                    + "?governorate_id=" + governorateId;
                let timesRes = await fetch(timesUrl);
                let timesData = await timesRes.json();

                // Populate the time select
                timeSelect.innerHTML = '<option value="" selected disabled>{{ translate('اختر الوقت') }}</option>';

                if (timesData.times && timesData.times.length > 0) {
                    timesData.times.forEach(t => {
                        let option = document.createElement('option');
                        option.value = t.id;
                        option.text = formatTime12h(t.start_time) + " - " + formatTime12h(t.end_time);
                        timeSelect.appendChild(option);
                    });

                    // If there's exactly one time, auto-select it
                    if (timesData.times.length === 1) {
                        timeSelect.value = timesData.times[0].id;
                        fetch("{{ route('store-shipping-details-in-session') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ selected_delivery_time: timesData.times[0].id })
                        }).then(() => {
                            console.log("Saved time (auto):", timesData.times[0].id);
                            autoSetDeliveryDate(timeSelect.options[timeSelect.selectedIndex].text);
                            checkIfCanProceed();
                        }).catch(err => {
                            console.error("Error saving time:", err);
                        });
                    }
                }
            } catch (err) {
                console.error("Error loading initial delivery times:", err);
            }
        }

        // Update the showOnMap function to remove the display:none manipulation
        async function showOnMap() {
            const govSelect = document.getElementById('governorate_id');
            const governorateId = govSelect?.value;
            const governorateName = govSelect?.options[govSelect.selectedIndex]?.text ?? '';
            const city = document.getElementById('city')?.value ?? '';
            const address = document.getElementById('address')?.value ?? '';
            const timeSelect = document.getElementById('delivery_time_id');
            const dateHidden = document.getElementById('delivery_date_hidden');
            const dateLabel = document.getElementById('delivery_date_label');

            // If no governorate, clear time picker but keep it visible
            if (!governorateId) {
                if (timeSelect) {
                    timeSelect.innerHTML = '<option value="" selected disabled>{{ translate('اختر الوقت') }}</option>';
                }
                if (dateHidden) dateHidden.value = '';
                if (dateLabel) dateLabel.textContent = '';
                checkIfCanProceed();
                return;
            }

            // 1) Fetch delivery times (rest of the function remains the same, but remove display manipulation)
            try {
                let oldTimeValue = timeSelect?.value || '';
                let timesUrl = "{{ route('delivery-times.by-governorate') }}"
                    + "?governorate_id=" + governorateId;
                let timesRes = await fetch(timesUrl);
                let timesData = await timesRes.json();

                let noteDiv = document.getElementById('governorate-note-div');
                let noteP = document.getElementById('governorate-note');
                if (timesData.note) {
                    noteDiv.style.display = 'block';
                    noteP.innerText = timesData.note;
                } else {
                    noteDiv.style.display = 'none';
                    noteP.innerText = '';
                }

                if (timeSelect) {
                    timeSelect.innerHTML = '<option value="" selected disabled>{{ translate('اختر الوقت') }}</option>';
                    if (timesData.times && timesData.times.length > 0) {
                        timesData.times.forEach(t => {
                            let option = document.createElement('option');
                            option.value = t.id;
                            option.text = formatTime12h(t.start_time) + " - " + formatTime12h(t.end_time);
                            timeSelect.appendChild(option);
                        });

                        // If old time is still valid, restore it, reset date and re-enable proceed
                        if (oldTimeValue) {
                            let found = timesData.times.find(t => t.id == oldTimeValue);
                            if (found) {
                                timeSelect.value = oldTimeValue;
                                autoSetDeliveryDate(timeSelect.options[timeSelect.selectedIndex].text);
                                checkIfCanProceed();
                            }
                        }

                        // If there's exactly one time, auto-select it
                        if (timesData.times.length === 1) {
                            timeSelect.value = timesData.times[0].id;
                            fetch("{{ route('store-shipping-details-in-session') }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ selected_delivery_time: timesData.times[0].id })
                            }).then(() => {
                                console.log("Saved time (auto):", timesData.times[0].id);
                                autoSetDeliveryDate(timeSelect.options[timeSelect.selectedIndex].text);
                                checkIfCanProceed();
                            }).catch(err => {
                                console.error("Error saving time:", err);
                            });
                        }
                    }
                    // Remove the else block that was hiding the timeSelect
                }
            } catch (err) {
                console.error("Error fetching times:", err);
            }

            // ... rest of your existing showOnMap function remains the same ...
        }




        // Call loadInitialDeliveryTimes when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadInitialDeliveryTimes();
            checkIfCanProceed();
        });
    </script>



    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('MAP_KEY') }}&callback=mapsShopping&libraries=places&v=3.56" defer></script>
@endpush
