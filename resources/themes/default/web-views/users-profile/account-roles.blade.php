@php
$user =  \App\Models\User::where('id', auth('customer')->id())->first();
@endphp
@extends('layouts.front-end.app')
<link rel="stylesheet" href="https://new.hubspareparts.com/public/assets/front-end/css/custom2.css">
<!-- Bootstrap 4.6 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

<style>

.card-header-custom:not(.collapsed) {
    color: #ffffff;
    background-color: #140b49 !important;
     border-radius: 5px !important;
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .125);
}
.card-header-custom button{
    color: #fff;
    font-weight: bold;
     margin: 5px;
}
.card-header-custom button:hover{
    color: #ffff00;
}
.permissions{
    margin: 10px;
}
.permissions input[type="checkbox" ]{
    transform: scale(1.2);

}
.permissions label{
    margin-left: 10px;
}

</style>

@section('title',translate('Roles'))

@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">


@endpush

@section('content')
<div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">





<div class="row">
@include('web-views.partials._profile-aside')
<section class="col-lg-9 __customer-profile px-0">
<div class="card">
<div class="card-body">
<div id="accordion">
<div class="d-flex align-items-center justify-content-between gap-2 mb-3">
<h5 class="font-bold m-0 fs-16">{{ translate('Roles') }}</h5>
</div>

<div class="accordion  card-inner">







@foreach($roles as $key=>$role)
<form class="mt-3 px-sm-2 pb-2" action="{{route('account-roles-update'  , $role->id)}}" method="post"
id="profile_form"
enctype="multipart/form-data">
@csrf
<div class="card">
<div class="card-header card-header-custom" id="heading{{$key}}">
<h5 class="mb-0">
<button class=" btn btn-link {{ $key != 0 ? 'collapsed' : '' }}"  type="button" data-toggle="collapse" data-target="#collapse{{$key}}" aria-expanded="{{ $key == 0 ? 'true' : 'false' }}" aria-controls="collapse{{$key}}">
{{$role->name}}
</button>
</h5>
</div>
<div id="collapse{{$key}}" class="collapse" aria-labelledby="heading{{$key}}" data-parent="#accordion">
@foreach($roleAllowedPermissions as $permission)

<div class="permissions ">
<input type ="checkbox" name="permissions[]" value="{{$permission['id']}}"
{{ in_array($permission['id'], $role->permissions->pluck('id')->toArray()) ? 'checked' : '' }}
> <label>{{$permission['menu_name']}}
</label>
</div>



@endforeach

<div class="col-12 text-end d-none d-md-block">
@if($user->hasRole('Account Owner'))
<button type="submit" class="btn btn--primary px-4 fs-14 font-semi-bold py-2">
{{ translate('update') }}
</button>
@else
<button type="button" class="btn btn-secondary" onclick="showPermissionAlert()"> {{ __('Update') }}</button>
@endif
</div>
</div>
</div>
</form>
@endforeach
</div>


</div>

</div>
</div>
</section>
</div>
</div>

<div class="bottom-sticky_offset"></div>
<div class="bottom-sticky_ele bg-white d-md-none p-3 ">
<button type="submit" class="btn btn--primary w-100 update-account-info">
{{translate('update')}}
</button>
</div>

@endsection

@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
@endpush

@section('script')
<script>
function showPermissionAlert() {
Swal.fire({
icon: 'warning',
title: 'Permission Denied',
text: 'You do not have permission to edit the profile.',
confirmButtonText: 'OK'
}).then(() => {
// Reload the page to reset checkboxes to original state
window.location.reload();
});
}
</script>

@endsection
