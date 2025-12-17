@php
    $user = \App\Models\User::find(auth('customer')->id());
@endphp
@extends('layouts.front-end.app')

@section('title', translate('Wishlist Collections'))

@section('content')

<div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
<div class="row">
@include('web-views.partials._profile-aside')
<section class="col-lg-9 __customer-profile customer-profile-wishlist px-0">

{{-- desktop --}}
<div class="card __card d-none d-lg-flex web-direction customer-profile-orders h-100">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-3 gap-2">
<h5 class="font-bold fs-16 mb-0">{{ translate('Wishlist Collection') }}</h5>
<div class="d-flex justify-content-end align-items-center mb-3 gap-2">

    @if($user->hasPermissionTo('web-viewcreat-edite-remove-collection'))
        <a  href="{{route('create-wishlist-collection')}}"  class="btn btn--primary text-capitalize btn-sm d-flex align-items-center gap-1" >
            <i class="navbar-tool-icon czi-heart"></i>
            Add Collection
        </a>
    @else
        <button class="btn btn--primary text-capitalize btn-sm d-flex align-items-center gap-1 disabled">{{ __('Add Collection') }}</button>
    @endif

<div class="d-flex justify-content-end d-lg-none">
<button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1">
<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
<path fill-rule="evenodd" clip-rule="evenodd" d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z" fill="white"></path>
</svg>
</button>
</div>
</div>

</div>



   @if($all_collections->count()>0)
<div class="table-responsive">
    <table class="table __table __table-2 text-center">
        <thead>
<tr>
<th>#</th>
<th>{{ __('Name') }}</th>
<th>{{ __('Due Date') }}</th>
<th>{{ __('Created By') }}</th>
<th>{{ __('Created On') }}</th>
<th>{{ __('Priority') }}</th>
<th>{{ __('Eng. Approve') }}</th>
<th>{{ __('Proc. Approve') }}</th>
<th>{{ __('Action') }}</th>
</tr>
</thead>
   <tbody>

   @foreach($all_collections as $key=>$collection)
<tr>
<td>{{$key + 1}}</td>
    <td>{{$collection->name}}</td>
    <td>{{$collection->due_date}}</td>
    <td>{{$collection->user->name}} </td>
    <td>{{$collection->created_at->format('d/m/Y')}} </td>
    <td>{{$collection->priority}} </td>
<td>
    <div class="">
        <!--<input class="check-input" type="checkbox" id="eng_approve"-->
        <!--       {{ $collection->eng_approve!='' ? 'checked' : '' }}-->
        <!--       data-wishlist_id="{{ $collection->id ?? '' }}"-->
        <!--        {{ !$user->hasPermissionTo('web-engineering-confirm') ? 'disabled' : '' }}>-->
        <!--<label for="eng_approve" class="checkbox-label"> {{ __('Approved') }}  </label>-->
        @if($collection->eng_approve!=null )
            <div for="proc_approve" class="checkbox-label"> {{ __('Approved') }}  </div>
            <p> {{$collection?->user_eng_approve?->name}} <br>
                {{$collection?->eng_approve}}
            </p>
        @else
            <label for="proc_approve" class="checkbox-label"> {{ __('Not Approved') }}  </label>
        @endif
    </div>
</td>
<td>
    <div class="">
        <!--<input class="check-input" type="checkbox" id="proc_approve"-->
        <!--       {{ $collection->eng_proc!='' ? 'checked' : '' }}-->
        <!--       data-wishlist_id="{{ $collection->id ?? '' }}"-->
        <!--        {{ $collection->eng_approve === null || !$user->hasPermissionTo('web-procurement-confirm') ? 'disabled' : '' }}>-->
        <!--<label for="proc_approve" class="checkbox-label"> {{ __('Approved') }}  </label>-->

        @if($collection->eng_proc!=null )
            <div for="proc_approve" class="checkbox-label"> {{ __('Approved') }}  </div>
            <p> {{$collection?->user_eng_proc?->name}} <br>
                {{$collection?->eng_proc}}
            </p>
        @else
            <label for="proc_approve" class="checkbox-label"> {{ __('Not Approved') }}  </label>
        @endif
    </div>
</td>
<td id="wishlist-actions">
<a href="{{route('view-wishlist-collection' , ['id'=>$collection->id])}}" class="btn btn-secondary btn-sm rounded-btn mb-2 me-1" style="background-color:#1b1c25b3 !important">   <i class="czi-eye"></i></a>

</td>
</tr>
   @endforeach
</tbody>

 </table>

</div>

   @else
<div class="d-flex justify-content-center align-items-center h-100">
<div class="text-center">
<img src="{{ theme_asset(path:'public/assets/front-end/img/empty-icons/empty-orders.svg') }}" width="100">
<h5 class="text-muted fs-14">{{ translate('You_have_not_any_collection_yet') }}!</h5>
</div>
</div>
    @endif


 <div class="card-footer border-0">
{{-- {{ $orders->links() }} --}}
</div>

</div>
</div>





{{-- mobile --}}
<div class="bg-white d-lg-none web-direction">
<div class="card-body py-0 customer-profile-orders d-flex flex-column gap-3">
<div class="d-flex justify-content-between align-items-center mb-3">
<h5 class="font-bold fs-16 mb-0">{{ translate('my_Order') }}</h5>
<button class="profile-aside-btn btn btn--primary rounded py-1 px-2">
<i class="tio-menu-hamburger"></i>
</button>
</div>



<!-------هنا هنضيف الداتا مرة تانية للموبايل بس بديزاين مختلف------->







{{-- @if(!$orders->count()) --}}
<div class="d-flex justify-content-center align-items-center h-100 pt-5">
<div class="text-center">
<img src="{{ theme_asset(path:'public/assets/front-end/img/empty-icons/empty-orders.svg') }}" width="100">
<h5 class="text-muted fs-14">{{ translate('You_have_not_any_order_yet') }}!</h5>
</div>
</div>

{{-- @endif --}}

<div class="card-footer border-0">
{{-- {{ $orders->links() }} --}}
</div>


</div>
</div>
</section>


</div>
</div>

@endsection
