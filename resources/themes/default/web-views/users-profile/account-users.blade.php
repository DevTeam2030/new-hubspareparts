@php
    $authUser = \App\Models\User::find(auth('customer')->id());
@endphp
@extends('layouts.front-end.app')

@section('title', translate('Users'))

@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/vendor/nouislider/distribute/nouislider.min.css')}}"/>
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/bootstrap-select.min.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/css/address.css') }}">
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush

@section('content')
<div class="__account-address">
 <div class="modal fade rtl text-align-direction" id="exampleModal" tabindex="-1" role="dialog"
aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog  modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title font-name">{{translate('add_new_user')}}</h5>
</div>
<div class="modal-body">
<form action="{{route('store-user')}}" method="post">
@csrf

<div class="tab-content">
<div id="home" class="container tab-pane active"><br>
<div class="form-row">
<div class="form-group col-md-6">
<label for="f_name ">
{{translate('First name')}}
<span class="text-danger">*</span>
</label>
<input class="form-control" type="text" id="f_name" name="f_name" required>
</div>
    <div class="form-group col-md-6">
        <label for="f_name ">
            {{translate('Last name')}}
            <span class="text-danger">*</span>
        </label>
        <input class="form-control" type="text" id="l_name" name="l_name" required>
    </div>
<div class="form-group col-md-6">
<label for="firstName">
{{translate('phone')}}
<span class="text-danger">*</span>
</label>

<input class="form-control phone-input-with-country-picker" id="phone" type="text"
placeholder="{{ translate('enter_phone_number') }}" required>
<input type="hidden" class="country-picker-phone-number w-50" name="phone" readonly>
</div>
</div>
<div class="form-row">
<div class="form-group col-md-6">
<label for="address-city">
{{translate('email')}}
<span class="text-danger">*</span>
</label>
<input class="form-control" type="text" id="email" name="email"
required>
</div>

    <div class="form-group col-md-6 mb-0">
        <label for="governorate" class="mb-2 text-capitalize">{{ translate('governorate') }}</label>
        <select name="governorate_id" id="governorate" class="form-control" required>
            <option value="">{{ translate('choose_governorate') }}</option>
            @foreach ($governorates as $governorate)
                <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
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
<input class="form-control" type="text" id="password" name="password"
required>
</div>
 <div class="form-group col-md-6">
<label for="address-city">
{{translate('confirm_password')}}
<span class="text-danger">*</span>
</label>
<input class="form-control" type="password" id="password_confirmation" name="password_confirmation"
required>
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
<input class="form-control" type="text" id="position" name="position"
required>
</div>

</div>




</div>


<div class="modal-footer">
<button type="button" class="btn btn-secondary"
data-dismiss="modal">{{translate('close')}}</button>
<button type="submit"
class="btn btn--primary">{{translate('add_information')}}  </button>
</div>
</div>
</form>
</div>
</div>

</div>
</div>


<div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
<div class="row ">
@include('web-views.partials._profile-aside')
<section class="col-lg-9 __customer-profile px-0">

<div class="card card-body border-0">
<div class="d-flex justify-content-between align-items-center mb-3 gap-2">
<h5 class="font-bold m-0 fs-16">{{translate('User management')}}</h5>
<div class="d-flex justify-content-end align-items-center mb-3 gap-2">
    @if($authUser->hasPermissionTo('web-addremove-members'))
<button type="submit" class="btn btn--primary text-capitalize btn-sm d-flex align-items-center gap-1" data-toggle="modal"
data-target="#exampleModal" id="add_new_address">
 <i class="navbar-tool-icon czi-user"></i>
{{translate('add_user')}}
</button>
    @endif

<div class="d-flex justify-content-end d-lg-none">
<button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1">
<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"
fill="none">
<path fill-rule="evenodd" clip-rule="evenodd"
d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z"
fill="white"/>
</svg>
</button>
</div>
</div>

</div>
@if ($all_users->count() ==0)
<div class="text-center text-capitalize pb-5 pt-5">
<img class="mb-4" src="{{theme_asset(path: 'public/assets/front-end/img/icons/address.svg')}}"
alt="" width="70">
<h5 class="fs-14">{{translate('no_users_found')}}!</h5>
</div>
@endif
<div class="row g-3">
<div class="table-responsive table-wrap">
<table class="table">
<thead>
<tr>
<th>#</th>
<th>{{ __('Name') }}</th>
<th>{{ __('Email') }}</th>
<th>{{ __('Role') }}</th>
	@if($authUser->hasPermissionTo('web-addremove-members'))
<th>{{ __('Action') }}</th>
	 @endif
</tr>
</thead>
<tbody>
 @foreach($all_users as $key=>$user)
 <tr>
     <td>{{$key+1}}</td>
<td>{{$user->name}}</td>
<td>{{ $user->email }} .</td>
     <td>{{ $user->load('roles')->roles->first()?->name ?? 'No Role' }}</td>
     @if($authUser->hasPermissionTo('web-addremove-members'))
         <td>
             <div class="d-flex justify-content-between gap-2 align-items-center">

             <a tabindex="0" href="{{route('edit-user' , $user->id)}}">
                 <img
                     src="{{theme_asset(path: 'public/assets/front-end/img/address-edit-icon.png')}}"
                     width="19" alt="">
             </a>
             <a tabindex="0" href="{{route('user-delete' , $user->id)}}">
                 <i class="fa fa-trash fa-lg"></i>
             </a>
             </div>
         </td>
     @endif
</tr>

 @endforeach
</tbody>
</table>
</div>
</div>
</div>
</section>
</div>
</div>
</div>
 @endsection

@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/js/bootstrap-select.min.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/account-address.js') }}"></script>

<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>

@endpush
