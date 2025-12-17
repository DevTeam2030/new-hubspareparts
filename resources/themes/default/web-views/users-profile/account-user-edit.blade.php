@extends('layouts.front-end.app')

@section('title', $user->name)

@push('css_or_js')
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/vendor/nouislider/distribute/nouislider.min.css')}}"/>
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/address.css') }}">
    <link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush
@php
    $authUser = \App\Models\User::find(auth('customer')->id());
@endphp
@section('content')
<div class="container py-4 rtl __account-address text-align-direction">

    <div class="row g-3">
        @include('web-views.partials._profile-aside')
        <section class="col-lg-9 col-md-8">

            <div class="card">
                <div class="card-body">
                    <h5 class="font-bold m-0 fs-16">{{translate('Update_User')}}</h5>
                    <form action="{{route('update-user')}}" method="post">
                        @csrf
                        <div class="row pb-1">
                            <div class="col-md-6">
                                <input type="hidden" name="id" value="{{$user->id}}">

                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="f_name ">
                                    {{translate('First name')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input class="form-control" type="text" id="f_name" name="f_name" value="{{$user->f_name}}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="f_name ">
                                    {{translate('Last name')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input class="form-control" type="text" id="l_name" name="l_name" value="{{$user->l_name}}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="firstName">
                                    {{translate('phone')}}
                                    <span class="text-danger">*</span>
                                </label>

                                <input class="form-control phone-input-with-country-picker" id="phone" type="text" name="phone"
                                       placeholder="{{ translate('enter_phone_number') }}" value="{{$user->phone}}" required>
                                <input type="hidden" class="country-picker-phone-number w-50" name="phone" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="address-city">
                                    {{translate('email')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input class="form-control" type="text" id="email" name="email" value="{{$user->email}}"
                                       required>
                            </div>

                            <div class="form-group col-md-6 mb-0">
                                <label for="governorate" class="mb-2 text-capitalize">{{ translate('governorate') }}</label>
                                <select name="governorate_id" id="governorate" class="form-control" required>
                                    <option value="">{{ translate('choose_governorate') }}</option>
                                    @foreach ($governorates as $governorate)
                                        <option value="{{ $governorate->id }}" @if($governorate->id == $user->governorate_id)  selected @endif>{{ $governorate->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="address-city">
                                    {{translate('password')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input class="form-control" type="password" id="password" name="password" >
                            </div>
                            <div class="form-group col-md-6">
                                <label for="address-city">
                                    {{translate('confirm_password')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" >
                            </div>


                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="address-city">
                                    {{translate('role')}}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" name="role" id="role">
                                    <option value="">{{ __('Select Role') }}</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ $role->id == $user->load('roles')->roles->first()?->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="form-group col-md-6">
                                <label for="address-city">
                                    {{translate('position')}}

                                </label>
                                <input class="form-control" type="text" id="position" name="position" value="{{$user->position}}">
                            </div>

                        </div>
                        <div class="modal-footer">
{{--                            <a href="{{ route('account-address') }}" class="closeB btn btn-secondary fs-14 font-semi-bold py-2 px-4">{{translate('close')}}</a>--}}
                            @if($authUser->hasPermissionTo('web-addremove-members'))
                            <button type="submit" class="btn btn--primary fs-14 font-semi-bold py-2 px-4">{{translate('update')}}  </button>
                                @else
                                <button type="button" class="btn btn-secondary" onclick="showPermissionAlert()">{{ __('Submit') }}</button>
                                @endif
                        </div>
                    </form>
                </div>
            </div>

        </section>
    </div>
</div>

@endsection

@push('script')

<script src="{{ theme_asset(path: 'public/assets/front-end/js/bootstrap-select.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/account-address.js') }}"></script>

<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>

<script>
    function showPermissionAlert() {
        Swal.fire({
            icon: 'warning',
            title: 'Permission Denied',
            text: 'You do not have permission to edit the profile.',
            confirmButtonText: 'OK'
        });
    }
</script>
@endpush
