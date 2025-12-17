@php
$user = \App\Models\User::find(auth('customer')->id());
$totalPrice = 0 ;
@endphp
@extends('layouts.front-end.app')

@section('title', __('Add Collection'))

@push('css_or_js')
<link rel="stylesheet" href="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/css/intlTelInput.css') }}">
@endpush
<style>
    .px-4-custome{
        padding-left: 0.9rem !important;
    }
</style>
@section('content')
<div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
<div class="row">
@include('web-views.partials._profile-aside')
<section class="col-lg-9 __customer-profile px-0">
<div class="card">
<div class="card-body">

<div class="d-flex align-items-center justify-content-between gap-2 mb-3">
<h5 class="font-bold m-0 fs-16">{{ $collection->name }}</h5>
<button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1 d-lg-none">
<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
<path fill-rule="evenodd" clip-rule="evenodd"
d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z"
fill="white"/>
</svg>
</button>
    <div class="d-flex justify-content-end align-items-center mb-3 gap-2">

        <a  href="{{route('account-wishlist-collections')}}"  class="btn btn--primary text-capitalize btn-sm d-flex align-items-center gap-1" >
            <i class="navbar-tool-icon czi-heart"></i>
            All Collections
        </a>
        <div class="d-flex justify-content-end d-lg-none">
            <button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z" fill="white"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<div class="card-inner">
<div class="form-row row g-4">

<div class="col-md-3">
<h6>{{ __('Due Date') }}</h6>
<p>{{$collection->due_date}}</p>
</div>
<div class="col-md-3">
<h6>{{ __('Created By') }}</h6>
<p> {{$collection->user?->name}} </p>
</div>
<div class="col-md-3">
<h6>{{ __('Created At') }}</h6>
<p> {{$collection->created_at->format('d/m/Y')}}  </p>
</div>
<div class="col-md-3">
<h6>{{ __('Priority') }}</h6>
<p> {{$collection->priority}}  </p>
</div>

<div class="row" style="width:100%" id="reloadable-section">
<div class="col-md-6">
<h6 style="font-size:16px">{{ __('Eng. Approve') }}</h6>
@if($collection->eng_approve!='' )
<p> {{$collection?->user_eng_approve?->name}} <br>
{{$collection?->eng_approve}}
</p>
@endif

<div class="dashboard-checkbox on-track-checkbox mt-3">
@if($user->hasRole('Account Owner'))
<input class="check-input" type="checkbox" id="eng_approve"
{{ $collection->eng_approve!=null ? 'checked' : '' }}
data-wishlist_id="{{ $collection->id ?? '' }}">
@else
<input class="check-input" type="checkbox" id="eng_approve"
{{ $collection->eng_approve!=null ? 'checked' : '' }}
data-wishlist_id="{{ $collection->id ?? '' }}"
{{!$user->hasPermissionTo('web-engineering-confirm') ? 'disabled' : '' }}>
@endif
<label for="eng_approve" class="checkbox-label"> {{ __('Approved') }}  </label>
</div>
</div>
<div class="col-md-6">
<h6 style="font-size:16px">{{ __('Proc. Approve') }}</h6>
@if($collection->eng_proc!='' )
<p> {{$collection?->user_eng_proc?->name}} <br> {{$collection->eng_proc}} </p>
@endif
@if($user->hasRole('Account Owner'))
<div class="dashboard-checkbox on-track-checkbox mt-3">
<input class="check-input" type="checkbox" id="proc_approve"
{{ $collection->eng_proc !=null ? 'checked' : '' }}
data-wishlist_id="{{ $collection->id ?? '' }}">
@else
<div class="dashboard-checkbox on-track-checkbox mt-3">
<input class="check-input" type="checkbox" id="proc_approve"
{{ $collection->eng_proc !=null ? 'checked' : '' }}
data-wishlist_id="{{ $collection->id ?? '' }}"
{{ !$user->hasPermissionTo('web-procurement-confirm') ? 'disabled' : '' }}>
@endif

<label for="proc_approve" class="checkbox-label"> {{ __('Approved') }}  </label>
</div>
</div>
</div>

<div class="col-md-12">
<h6>{{ __('Notes') }}</h6>
<p>
{{ $collection->notes }}
</p>
</div>
<div class="table-responsive table-wrap">
<table class="table-auto w-full border">
<thead>
<tr class="bg-gray-100">
<th class="px-4-custome py-2">#</th>
<th class="px-4-custome py-2 fs-12" style="font-weight: bold; color: #000;">Name</th>
<th class="px-4-custome py-2 fs-12" style="font-weight: bold; color: #000;">Image</th>
<th class="px-4-custome py-2 fs-12" style="font-weight: bold; color: #000;">Quantity</th>
<th class="px-4-custome py-2 fs-12" style="font-weight: bold; color: #000;">Price</th>
<th class="px-4-custome py-2 fs-12"style="font-weight: bold; color: #000;" >Status</th>
<th class="px-4-custome py-2 fs-12" style="font-weight: bold; color: #000;">Remove</th>
<th class="px-4-custome py-2 fs-12" style="font-weight: bold; color: #000;">Export to Cart</th>
</tr>
</thead>
<tbody>
@forelse ($wishlist as $index => $item)
@php
$product = App\Models\Product::find($item->product_id);
@endphp
<tr class="border-t" id="wishlist-item-{{ $product->id }}">
<td class="px-4-custome py-2">
@if($product && $product->current_stock < $item->quantity)
<input type="checkbox" class="select-item" value="" disabled>
@else
<input type="checkbox" class="select-item" value="{{ $item->id }}">
@endif

</td>
<td class="px-4-custome py-2">
<a href="{{route('product', $product?->slug)}}"
class="fs-12 flash-product-title text-capitalize fw-semibold">
{{ $product['name'] }}
</a>
</td>
<td class="px-4-custome py-2">
<div class="d-flex align-items-center justify-content-center p-12px">
<div class="flash-deals-background-image">
<img class="__img-125px" alt=""
src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}">
</div>
</div>
</td>
<td class="px-4-custome py-2 fs-12">
{{--{{ $item->quantity }} <br>--}}
 in stock : {{ $product->current_stock}}
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex justify-content-center align-items-center quantity-box border rounded border-base web-text-primary" style="max-width:70% !important;">
        <span class="input-group-btn">
            <button class="btn btn-number __p-10 web-text-primary" type="button"
                    data-type="minus"
                    data-field="quantity"
                    data-wishlist-id="{{ $item->id }}"
                    data-max-stock="{{ $product->current_stock }}"
                    {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                -
            </button>
        </span>
            <input type="text" name="quantity"
                   class="form-control input-number text-center cart-qty-field __inline-29 border-0 "
                   placeholder="{{ translate('1') }}"
                   value="{{ $item->quantity }}"
                   data-wishlist-id="{{ $item->id }}"
                   data-max-stock="{{ $product->current_stock }}"
                   min="1"
                   max="{{ $product->current_stock }}">
            <span class="input-group-btn">
            <button class="btn btn-number __p-10 web-text-primary" type="button"
                    data-type="plus"
                    data-field="quantity"
                    data-wishlist-id="{{ $item->id }}"
                    data-max-stock="{{ $product->current_stock }}"
                    {{ $item->quantity >= $product->current_stock ? 'disabled' : '' }}>
                +
            </button>
        </span>
        </div>
    </div>
</td>
<td class="px-4-custome py-2">
{{--<span class="font-weight-normal text-accent d-flex align-items-end gap-2">--}}
{{--    @if(getProductPriceByType(product: $product, type: 'discount', result: 'value') > 0)--}}
{{--        <del class="product-total-unit-price align-middle text-muted fs-14 font-semibold ">--}}
{{--{{ webCurrencyConverter(amount: $product->unit_price) }}--}}
{{--</del>--}}
{{--    @endif--}}

{{--<span class="discounted-unit-price fs-14 font-bold">--}}
{{--{{ getProductPriceByType(product: $product, type: 'discounted_unit_price', result: 'string') }}--}}
{{--</span>--}}

{{--</span>--}}
    <div class="font-weight-normal text-accent d-flex flex-column">
         <span class="discounted-unit-price fs-14 font-bold">
                {{ webCurrencyConverter(amount: $product->unit_price) }}
            </span>
    </div>
    <div class="mb-3">

    </div>

@php
$unitPrice = $product->unit_price;
$itemTotal = $unitPrice * $item->quantity;
$totalPrice += $itemTotal;
@endphp
</td>
<td class="px-4-custome py-2">
@if($product && $product->current_stock >= $item->qty)
<span class="text-green-600"><img src="/public/images/in-stock.png" width="50" title="available"></span>
@else
<span class="text-red-600"><img src="/public/images/out-of-stock.png" width="50" title="Sold Out"></span>
@endif
</td>
<td class="px-4-custome py-2">
@if( $collection->eng_approve == null && $collection->eng_proc == null )
@php
$checkCart = \App\Models\Cart::where('customer_id' , (auth('customer')->id()))
->where('product_id' , $product->id)
->first();
@endphp
<a data-label="Close" data-type="tr"
data-product_hash_id="{{ $product->id }}" href="#1" data-cart-key="{{$checkCart->id ?? null}}"
class="ff-jost remove-wishlist px-3 btn btn-danger {{(!$user->hasPermissionTo('web-adddelete-products-to-wishlist') ? 'disabled' : '' )}}">
<span class="icon-close">
<i class="czi-trash"></i>
</span>
</a>
@else
<a  data-type="tr" class="ff-jost  px-3 btn  btn-danger disabled" onclick="showApproveAlert()">
<span class="icon-close text-light">
<i class="czi-trash"></i>
</span>
</a>
@endif
</td>
<td class="px-4-custome py-2">
@if( $collection->eng_proc != null && $product && $product->current_stock >= $item->qty)
@if($user->hasPermissionTo('web-export-to-cart'))
<a data-label="Move" data-type="tr"
data-product_hash_id="{{ $product->id }}" href="#1"  data-product-quantity="{{ $item->quantity }}"
class="ff-jost move-cart px-3 btn btn-info">
<span class="icon-close text-light">
<i class="czi-cart"></i>

</span>
</a>
@else
<a data-label="Move" data-type="tr" href="#1"
data-product_hash_id="{{ $product->id }}" class="ff-jost move-cart  px-3 btn btn-info disabled">
<span class="icon-close text-light">
<i class="czi-cart"></i>
</span>
</a>
@endif

@elseif( !$user->hasPermissionTo('web-viewcreat-edite-remove-collection'))
<a  data-type="tr" class="ff-jost  px-3 btn btn-info disabled" onclick="showPermissionAlert()">
<span class="icon-close text-light">
<i class="czi-cart"></i>
</span>
</a>
@else
@if($product &&  $product->current_stock < $item->qty)
<a  data-type="tr"   class="ff-jost  px-3 btn btn-info disabled" >
<span class="icon-close text-light">
<i class="las la-shopping-cart"> <i class="czi-cart"></i></i>
</span>
</a>
@else
<a  data-type="tr"  data-product_hash_id="{{ $product->id }}" class="ff-jost move-cart px-3 btn btn-info disabled" data-product-quantity="{{ $item->quantity }}>
<span class="icon-close text-light">
<i class="las la-shopping-cart"> <i class="czi-cart"></i></i>
</span>
</a>
@endif

@endif
</td>
</tr>
@empty
<tr>
<td colspan="8" class="text-center py-4">No items found in this collection.</td>
</tr>
@endforelse
<tr>
<td class="py-4 px-4-custome" colspan="2"><h6 class="font-bold">{{ __('Total Cost') }}</h6></td>
<td class="py-4 px-4-custome" colspan="6" id="wishlist-total-cost">{{number_format($totalPrice, 2)}}</td>
</tr>
</tbody>
</table>

</div>
<div class="col-md-3" >
<div class="btn-wrapper" id="export-section">
@if($user->hasPermissionTo('web-export-to-cart'))
{{--                                    <button id="export-all-wishlist" class="btn-info {{ ( $collection->eng_approve == null || $collection->eng_proc == null ) ? 'btn btn-buyNow btn-bg-2 disabled' : 'cmn_btn btn_bg_2'}} ">{{ __('Export all to cart') }}</button>--}}
<button id="export-all-wishlist" class="btn-success btn btn-buyNow btn-bg-2">{{ __('Export all to cart') }}</button>

@else
<button class="btn-success btn btn-buyNow btn-bg-2 disabled">{{ __('Export all to cart') }}</button>
@endif

</div>
</div>
<div class="col-md-3" id="export-section-selected">
<div class="btn-wrapper">
@if($user->hasPermissionTo('web-export-to-cart'))
{{--                                <button id="export-selected-wishlist" class="btn-success {{ ( $collection->eng_approve == null || $collection->eng_proc == null ) ? 'btn btn-buyNow btn-bg-2 disabled': 'btn btn-buyNow btn-bg-2' }}">{{ __('Export selected to cart') }}</button>--}}
<button id="export-selected-wishlist" class="btn-info btn btn-buyNow btn-bg-2">{{ __('Export selected to cart') }}</button>

@else
<button  class="btn-success btn btn-buyNow btn-bg-2 disabled">{{ __('Export selected to cart') }}</button>

@endif
</div>
</div>
<div class="col-md-3">
<div class="btn-wrapper text-end" id="edit-wishlist">
@if( $collection->eng_approve == null && $collection->eng_proc == null)
<a tabindex="0" class="btn btn-warning btn-bg-2 {{(!$user->hasPermissionTo('web-viewcreat-edite-remove-collection')) ? 'disabled' : ''}}" href="{{route('edit-wishlist-collection' , $collection->id)}}">
Edit
</a>
@else
<a tabindex="0" class="btn btn-warning btn-bg-2 disabled" href="{{route('edit-wishlist-collection' , $collection->id)}}">
Edit
</a>
@endif
</div>
</div>
<div class="col-md-3">
<div class="btn-wrapper text-end" id="delete-wishlist">
@if(!$user->hasPermissionTo('web-viewcreat-edite-remove-collection'))
<a class="btn btn-danger btn-bg-2  mb-2 me-1 disabled" onclick="showPermissionAlert()">
Delete
</a>
@else
<form action="{{ route('delete-wishlist-collection' , $collection->id) }}" method="POST" >
@csrf
@if( $collection->eng_approve == null && $collection->eng_proc == null)
<button type="submit" class="btn btn-sm btn-danger btn-xs mb-2 me-1 " >
Delete
</button>
@else
<button type="submit" class="btn btn-danger btn-bg-2 btn-bg-1 pt-3 py-12 disabled" >
<i class="fa fa-trash fa-lg"></i>
</button>
@endif
</form>
@endif


</div>
</div>
</div>

</div>
</div>
</div>
</div>
</section>
</div>
</div>

<div class="bottom-sticky_offset"></div>


@endsection

@push('script')
<script src="{{ theme_asset(path: 'public/assets/front-end/plugin/intl-tel-input/js/intlTelInput.js') }}"></script>
<script src="{{ theme_asset(path: 'public/assets/front-end/js/country-picker-init.js') }}"></script>
<script>
const userCanConfirm = @json($user->hasPermissionTo('web-export-to-cart'));
const userCanRemoveItem = @json($user->hasPermissionTo('web-adddelete-products-to-wishlist'));
const userCanRemoveCollection = @json($user->hasPermissionTo('web-viewcreat-edite-remove-collection'));
const eng_proc = @json($collection->eng_proc);
const eng_approve = @json($collection->eng_approve);
const collectionId = @json($collection->id);
function updateExportButtonsState() {
const isProcApproved = $('#proc_approve').is(':checked');
const isEngApproved = $('#eng_approve').is(':checked');
const userCanExport = @json($user->hasPermissionTo('web-export-to-cart'));

// Enable/disable export buttons based on conditions
if (isProcApproved && isEngApproved &&  userCanExport) {
$('#export-all-wishlist').removeClass('disabled').prop('disabled', false);
$('#export-selected-wishlist').removeClass('disabled').prop('disabled', false);
// $('.move-cart').removeClass('disabled').prop('disabled', true);
if ($('.move-cart').is(':disabled')) {
$('.move-cart').removeClass('disabled').prop('disabled', false);
}
} else {
$('#export-all-wishlist').addClass('disabled').prop('disabled', true);
$('#export-selected-wishlist').addClass('disabled').prop('disabled', true);
$('.move-cart').addClass('disabled').prop('disabled', true);
}
}
(function($) {
"use strict";
$(document).ready(function($) {
updateExportButtonsState();
$(document).on('click', '.bodyUser_overlay', function() {
$('.user-dashboard-wrapper, .bodyUser_overlay').removeClass('show');
});

$(document).on('click', '.mobile_nav', function() {
$('.user-dashboard-wrapper, .bodyUser_overlay').addClass('show');
});

$('#export-selected-wishlist, #export-all-wishlist').on('click', function(e) {
e.preventDefault();
if (!userCanConfirm) {
alert("You do not have permission to export items to cart.");
return;
}

let items = [];

if ($(this).is('#export-selected-wishlist')) {
// selected checkboxes
$('.select-item:checked').each(function() {
// let row = $(this).closest('tr');
// let productId = row.find('.move-cart').data('product_hash_id');
//let quantity  = row.find('.move-cart').data('product-quantity');
    let row = $(this).closest('tr');
    let productId = row.find('.move-cart').data('product_hash_id');
    let quantity  = parseInt(row.find('.cart-qty-field').val()); // ✅ Get from input
    items.push({id: productId, quantity: quantity , collection_id : collectionId});
// ✅ Get from input instead
items.push({id: productId, quantity: quantity , collection_id : collectionId});
});
} else {
// all items
$('.move-cart').each(function() {
    let row = $(this).closest('tr');
let productId = $(this).data('product_hash_id');
//let quantity  = $(this).data('product-quantity');
let quantity  = parseInt(row.find('.cart-qty-field').val());
items.push({id: productId, quantity: quantity , collection_id : collectionId});
});
}

if (items.length === 0) {
alert('Please select at least one item to export.');
return;
}

items.forEach(function(item) {
$.ajax({
url: "{{ route('cart.add') }}",
type: "POST",
data: {
id: item.id,
quantity: item.quantity,   // ✅ send quantity
 collection_id : collectionId,
_token: "{{ csrf_token() }}"
},
success: function(response) {
//loadHeaderCardAndWishlistArea(response);
//toastr.success(response.msg || 'Item moved to cart successfully');
toastr.success('Item moved to cart successfully');
//let countCartElement = $("#cart_items .navbar-tool-label");
//countCartElement.html(parseInt({{\App\Models\Cart::where('customer_id' , $user?->id)->count()}}));
    updateNavCart();
},
error: function(xhr) {
toastr.success('Error moving item to cart');
//toastr.error(xhr.responseJSON.message || 'Error moving item to cart');
}
});
});
});



$(document).on("click", ".move-cart", function(e) {
e.preventDefault();
    let $this = $(this); // ✅ Store reference to the clicked element
    let $row = $this.closest('tr')

let rowId = $(this).data('product_hash_id');
//let quantity = $(this).data('product-quantity');
    let quantity = parseInt($row.find('.cart-qty-field').val()); // ✅ Get from input

let data = {
id: rowId,
quantity : quantity,
collection_id : collectionId,
_token: "{{ csrf_token() }}"
};
if (!userCanConfirm) {
alert("You do not have permission to export items to cart.");
return;
}
$.ajax({
url: "{{ route('cart.add') }}",
type: "POST",
data: data,
success: function(response) {
// alert('Cart response: ' + JSON.stringify(response));
//loadHeaderCardAndWishlistArea(response);
//$('#wishlist-item-' + rowId).remove();
//updateWishlistTotal();
//toastr.success(response.msg || 'Item moved to cart successfully');
toastr.success('Item moved to cart successfully');
//let countCartElement = $("#cart_items .navbar-tool-label");
//countCartElement.html(parseInt({{\App\Models\Cart::where('customer_id' , $user?->id)->count()}}));
    updateNavCart();
},
error: function(xhr) {
toastr.success('Error moving item to cart');
//toastr.error(xhr.responseJSON.message || 'Error moving item to cart');
}
});
});

$(document).on("click", ".remove-wishlist", function(e) {
e.preventDefault(); // Prevent default anchor behavior

if (!userCanRemoveItem) {
Swal.fire({
icon: 'error',
title: 'Permission Denied',
text: 'You do not have permission to remove items from the wishlist.',
});
return;
}

let rowId = $(this).data('product_hash_id');
let cartKey = $(this).data('cart-key');

let formData = new FormData();
formData.append("id", rowId);
formData.append("_token", "{{ csrf_token() }}");
let formDataCart = new FormData();
formDataCart.append("_token", "{{ csrf_token() }}");
formDataCart.append("key", cartKey);
if(cartKey != null){
$.ajax({
url: "{{ route('cart.remove') }}",
type: "POST",
data: formDataCart,
processData: false,
contentType: false,
beforeSend: function() {
// Show loading spinner if needed
$('.loader-wrapper').show();
},
success: function(response) {
// Remove the item from the DOM
//$('#wishlist-item-' + rowId).remove();

// Update the total price
//updateWishlistTotal();

// Reload the header cart/wishlist section
//loadHeaderCardAndWishlistArea(response);

// Show success message
//toastr.success(response.msg || 'Item removed successfully');
toastr.success('Item removed successfully');
let countCartElement = $("#cart_items .navbar-tool-label");
countCartElement.html(parseInt({{\App\Models\Cart::where('customer_id' , $user?->id)->count()}}));

},
error: function(xhr) {
// Show error message
//toastr.error(xhr.responseJSON.message || 'Error removing item');
},
complete: function() {
// Hide loading spinner
$('.loader-wrapper').hide();
}
});
}

$.ajax({
url: "{{ route('delete-wishlist') }}",
type: "POST",
data: formData,
processData: false,
contentType: false,
beforeSend: function() {
// Show loading spinner if needed
$('.loader-wrapper').show();
},
success: function(response) {
// Remove the item from the DOM
let selector = '#wishlist-item-' + rowId;
let element = $(selector);
element.remove();

// Update the total price
updateWishlistTotal();

// Reload the header cart/wishlist section
//loadHeaderCardAndWishlistArea(response);
toastr.success('Item removed successfully');
let countWishlistElement = $(".countWishlist");
countWishlistElement.html(
parseInt(countWishlistElement.html()) - 1
);


// Show success message
//toastr.success(response.msg || 'Item removed successfully');

},
error: function(xhr) {
toastr.success('Error removing item');
// Show error message
//toastr.error(xhr.responseJSON.message || 'Error removing item');
},
complete: function() {
// Hide loading spinner
$('.loader-wrapper').hide();
}
});
});
$(document).on('change', '#eng_approve', function () {
        const approved = $(this).is(':checked') ? 1 : 0;
        const wishlistId = $(this).data('wishlist_id');
        const isProcApproved = $('#proc_approve').is(':checked');
        const isEngApproved = $(this).is(':checked');


        let formData = new FormData();
        formData.append('wishlist_id', wishlistId);
        formData.append('approved', approved);
        formData.append('_token', "{{ csrf_token() }}");

        $.ajax({
            url: "{{ route('approve-eng') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                // Show loading if needed
            },
            success: function (data) {
                toastr.success(data.message ?? "تم التنفيذ بنجاح");

                // Reload section only
                $('#reloadable-section').load(location.href + ' #reloadable-section > *', function() {
                    // Reattach event handlers if needed
                });

                updateExportButtonsState();

                // Update buttons based on BOTH checkboxes
                if (isEngApproved || isProcApproved) {
                    // Both are checked - disable everything
                    $('#delete-wishlist button').addClass('disabled').attr('disabled', true);
                    $('#edit-wishlist a').addClass('disabled');
                    $('.remove-wishlist').addClass('disabled');
                } else {
                    // At least one is unchecked - enable delete/edit/remove
                    $('#edit-wishlist a').removeClass('disabled');
                    $('#delete-wishlist button').removeClass('disabled').attr('disabled', false);
                    $('.remove-wishlist').removeClass('disabled');
                }
            },
            error: function (xhr) {
                alert("حصل خطأ غير متوقع");
                $('#eng_approve').prop('checked', !approved);
            }
        });
    });



    $(document).on('change', '#proc_approve', function () {
        const approved = $(this).is(':checked') ? 1 : 0;
        const wishlistId = $(this).data('wishlist_id');
        const isProcApproved = $(this).is(':checked');
        const isEngApproved = $('#eng_approve').is(':checked');

        let formData = new FormData();
        formData.append('wishlist_id', wishlistId);
        formData.append('approved', approved);
        formData.append('_token', "{{ csrf_token() }}");

        $.ajax({
            url: "{{ route('approve-proc') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                // لو عايز تحط لودينج هنا
            },
            success: function (data) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(data.message ?? "تم التنفيذ بنجاح");
                } else {
                    alert(data.message ?? "تم التنفيذ بنجاح");
                }

                // Reload section only
                $('#reloadable-section').load(location.href + ' #reloadable-section > *');

                updateExportButtonsState();

                // Update buttons based on BOTH checkboxes
                if ((isEngApproved || isProcApproved)) {
                    // Both are checked - disable everything
                    $('#delete-wishlist button').addClass('disabled').attr('disabled', true);
                    $('#edit-wishlist a').addClass('disabled');
                    $('.remove-wishlist').addClass('disabled');
                } else {
                    if(userCanRemoveCollection){
                        // At least one is unchecked - enable delete/edit/remove
                        $('#edit-wishlist a').removeClass('disabled');
                        $('#delete-wishlist button').removeClass('disabled').attr('disabled', false);
                        $('.remove-wishlist').removeClass('disabled');
                    }
                }

                toggleMoveCartButtons();
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    let messages = [];
                    $.each(errors, function (key, value) {
                        messages.push(value[0]);
                    });

                    if (typeof toastr !== 'undefined') {
                        toastr.error(messages.join("<br>"));
                    } else {
                        alert(messages.join("\n"));
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error("حصل خطأ غير متوقع");
                    } else {
                        alert("حصل خطأ غير متوقع");
                    }
                }

                // رجّع الـ checkbox زي ما كان
                $('#proc_approve').prop('checked', !approved);
            }
        });
    });




});
})(jQuery);

</script>
<script>
    function updateWishlistTotal() {
        let total = 0;

        $('tbody tr[id^="wishlist-item-"]').each(function() {
            // Get the price - it's in the discounted-unit-price span
            const priceText = $(this).find('.discounted-unit-price').text().trim();
            const price = parseFloat(priceText.replace(/[^\d.-]/g, '')) || 0;

            // Get the quantity from the input field
            const quantityInput = $(this).find('input[name="quantity"]');
            const qty = parseInt(quantityInput.val()) || 0;

            total += price * qty;
        });

        $('#wishlist-total-cost').text(total.toFixed(2));
    }
function showPermissionAlert() {
Swal.fire({
icon: 'warning',
title: 'Permission Denied',
text: 'You do not have permission',
confirmButtonText: 'OK'
});
}
function showApproveAlert() {
Swal.fire({
icon: 'warning',
title: 'Deletion Denied',
text: "The collection has been approved.",
confirmButtonText: 'OK'
});
}

function toggleMoveCartButtons() {
    const isEngApproved = $('#eng_approve').is(':checked');
    const isProcApproved = $('#proc_approve').is(':checked');

    // Enable move cart buttons only if BOTH approvals are checked
    if (isEngApproved && isProcApproved) {
        $('.move-cart').removeClass('disabled').prop('disabled', false);
    } else {
        $('.move-cart').addClass('disabled').prop('disabled', true);
    }
}
toggleMoveCartButtons();

    $(document).on('click', '.btn-number', function(e) {
        e.preventDefault();

        // Store original value before any changes
        var input = $(this).closest('.quantity-box').find('input[name="quantity"]');
        var originalValue = parseInt(input.val()) || 1;

        var type = $(this).attr('data-type');
        var currentVal = originalValue;
        var wishlistId = $(this).attr('data-wishlist-id');
        var maxStock = parseInt($(this).attr('data-max-stock')) || 999999;

        // Calculate new value
        var newValue = currentVal;
        if (type == 'minus') {
            if (currentVal > 1) {
                newValue = currentVal - 1;
            }
        } else if (type == 'plus') {
            if (currentVal < maxStock) {
                newValue = currentVal + 1;
            } else {
                toastr.warning('{{ translate("Maximum_available_stock_reached") }}');
                return;
            }
        }

        // Update UI immediately for better UX
        input.val(newValue);
        updateQuantityButtons(wishlistId, newValue);

        // Check permissions and update database
        const isEngApproved = $('#eng_approve').is(':checked');
        const isProcApproved = $('#proc_approve').is(':checked');

        if (isEngApproved || isProcApproved) {
            toastr.error('Cannot update quantity - collection has been approved');
            // Revert to original value
            input.val(originalValue);
            updateQuantityButtons(wishlistId, originalValue);
            return;
        }
        if(!userCanRemoveItem){
            toastr.error('You do not have permission to update wishlist');
            // Revert to original value
            input.val(originalValue);
            updateQuantityButtons(wishlistId, originalValue);
            return;
        }

        // Proceed with API call
        updateWishlistQuantity(wishlistId, newValue, originalValue);
    });

// Handle direct input changes
    $(document).on('change', 'input[name="quantity"]', function() {
        var input = $(this);
        var originalValue = parseInt(input.attr('data-last-value')) || parseInt(input.val());
        var wishlistId = input.attr('data-wishlist-id');
        var newValue = parseInt(input.val()) || 1;
        var maxStock = parseInt(input.attr('data-max-stock')) || 999999;

        // Validate input
        if (newValue < 1) {
            newValue = 1;
            input.val(newValue);
        }

        if (newValue > maxStock) {
            newValue = maxStock;
            input.val(newValue);
            toastr.warning('{{ translate("Maximum_available_stock_reached") }}');
        }

        // Store the current value as last value for future reverts
        input.attr('data-last-value', newValue);

        // Check permissions
        const isEngApproved = $('#eng_approve').is(':checked');
        const isProcApproved = $('#proc_approve').is(':checked');

        if (isEngApproved || isProcApproved) {
            toastr.error('Cannot update quantity - collection has been approved');
            // Revert to original value
            input.val(originalValue);
            updateQuantityButtons(wishlistId, originalValue);
            return;
        }
        if(!userCanRemoveItem){
            toastr.error('You do not have permission to update wishlist');
            // Revert to original value
            input.val(originalValue);
            updateQuantityButtons(wishlistId, originalValue);
            return;
        }

        // Update buttons immediately
        updateQuantityButtons(wishlistId, newValue);

        // Proceed with API call
        updateWishlistQuantity(wishlistId, newValue, originalValue);
    });

    function updateWishlistQuantity(wishlistId, quantity, fallbackValue = null) {
        $.ajax({
            url: '{{ route("wishlist.update-quantity") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                wishlist_id: wishlistId,
                quantity: quantity
            },
            success: function(response) {
                if (response.value == 1) {
                    toastr.success(response.success);
                    // Update total cost after successful quantity change
                    updateWishlistTotal();

                    // Store the successful value as last known good value
                    $(`input[name="quantity"][data-wishlist-id="${wishlistId}"]`)
                        .attr('data-last-value', quantity);
                } else {
                    toastr.error(response.error);
                    // Revert to fallback value or reload from server
                    if (fallbackValue !== null) {
                        $(`input[name="quantity"][data-wishlist-id="${wishlistId}"]`).val(fallbackValue);
                        updateQuantityButtons(wishlistId, fallbackValue);
                    } else {
                        reloadWishlistItemQuantity(wishlistId);
                    }
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                if (response && response.error) {
                    toastr.error(response.error);
                } else {
                    toastr.error('{{ translate("Something_went_wrong") }}');
                }

                // Revert to fallback value or reload from server
                if (fallbackValue !== null) {
                    $(`input[name="quantity"][data-wishlist-id="${wishlistId}"]`).val(fallbackValue);
                    updateQuantityButtons(wishlistId, fallbackValue);
                } else {
                    reloadWishlistItemQuantity(wishlistId);
                }
            }
        });
    }

function reloadWishlistItemQuantity(wishlistId) {
    $.ajax({
        url: '{{ route("wishlist.get-quantity") }}', // You'll need to create this route
        method: 'GET',
        data: {
            wishlist_id: wishlistId
        },
        success: function(response) {
// Update the input field with the actual quantity from server
            $(`input[name="quantity"][data-wishlist-id="${wishlistId}"]`).val(response.quantity);

// Update button states
            updateQuantityButtons(wishlistId, response.quantity);
        },
        error: function() {
// Fallback: reload the entire section
            $('#reloadable-section').load(location.href + ' #reloadable-section > *');
        }
    });
}

function updateQuantityButtons(wishlistId, quantity) {
    const input = $(`input[name="quantity"][data-wishlist-id="${wishlistId}"]`);
    const maxStock = parseInt(input.attr('data-max-stock')) || 999999;
    const minusBtn = input.closest('.quantity-box').find('.btn-number[data-type="minus"]');
    const plusBtn = input.closest('.quantity-box').find('.btn-number[data-type="plus"]');

    if (quantity <= 1) {
        minusBtn.attr('disabled', true);
    } else {
        minusBtn.removeAttr('disabled');
    }

    if (quantity >= maxStock) {
        plusBtn.attr('disabled', true);
    } else {
        plusBtn.removeAttr('disabled');
    }
}

    $(document).ready(function() {
        $('input[name="quantity"]').each(function() {
            var currentValue = parseInt($(this).val()) || 1;
            $(this).attr('data-last-value', currentValue);
        });
    });


    function updateNavCart() {
        $.post(
            $("#route-cart-nav-cart").data("url"),
            {
                _token: $('meta[name="_token"]').attr("content"),
            },
            function (response) {
                $("#cart_items").html(response.data);
                cartListQuantityUpdateInit();
            }
        );
    }

    function cartListQuantityUpdateInit() {
        $(".action-update-cart-quantity").on("click", function () {
            let cartId = $(this).data("cart-id");
            let productId = $(this).data("product-id");
            let action = $(this).data("action");
            let event = $(this).data("event");
            updateCartQuantity(cartId, productId, action, event);
        });

        $(".action-update-cart-quantity-list").on("click", function () {
            let minimumOrderQuantity = $(this).data("minimum-order");
            let key = $(this).data("cart-id");
            let increment = $(this).data("increment");
            let event = $(this).data("event");
            updateCartQuantityList(minimumOrderQuantity, key, increment, event);
        });

        $(".action-change-update-cart-quantity-list").on("change", function () {
            let minimumOrderQuantity = $(this).data("minimum-order");
            let key = $(this).data("cart-id");
            let increment = $(this).data("increment");
            let event = $(this).data("event");
            updateCartQuantityList(minimumOrderQuantity, key, increment, event);
        });

        $(".action-update-cart-quantity-list-mobile").on("click", function () {
            let minimumOrderQuantity = $(this).data("minimum-order");
            let key = $(this).data("cart-id");
            let increment = $(this).data("increment");
            let event = $(this).data("event");
            updateCartQuantityListMobile(
                minimumOrderQuantity,
                key,
                increment,
                event
            );
        });

        $(".action-change-update-cart-quantity-list-mobile").on(
            "change",
            function () {
                let minimumOrderQuantity = $(this).data("minimum-order");
                let key = $(this).data("cart-id");
                let increment = $(this).data("increment");
                let event = $(this).data("event");
                updateCartQuantityListMobile(
                    minimumOrderQuantity,
                    key,
                    increment,
                    event
                );
            }
        );

        $(document).ready(function () {
            $(".cart_information").each(function () {
                let allShopItemsInChecked = true;
                $(this)
                    .find(".shop-item-check")
                    .each(function () {
                        if (!$(this).prop("checked")) {
                            allShopItemsInChecked = false;
                            return false;
                        }
                    });
                $(this)
                    .find(".shop-head-check")
                    .prop("checked", allShopItemsInChecked);
            });
        });

        $(".shop-head-check").on("change", function () {
            $(this)
                .parents(".cart_information")
                .find(".shop-item-check")
                .prop("checked", this.checked);
        });

        $(".shop-item-check").on("change", function () {
            var allChecked = true;
            $(this)
                .parents(".cart_information")
                .find(".shop-item-check")
                .each(function () {
                    if (!$(this).prop("checked")) {
                        allChecked = false;
                        return false;
                    }
                });
            $(this)
                .parents(".cart_information")
                .find(".shop-head-check")
                .prop("checked", allChecked);
        });

        $(".shop-head-check-desktop").on("change", function () {
            getCartSelectCartItemsCheckedValues(
                '.cart_information input[type="checkbox"].shop-item-check-desktop'
            );
        });

        $(".shop-head-check-mobile").on("change", function () {
            getCartSelectCartItemsCheckedValues(
                '.cart_information input[type="checkbox"].shop-item-check-mobile'
            );
        });

        $(".shop-item-check-desktop").on("change", function () {
            getCartSelectCartItemsCheckedValues(
                '.cart_information input[type="checkbox"].shop-item-check-desktop'
            );
        });

        $(".shop-item-check-mobile").on("change", function () {
            getCartSelectCartItemsCheckedValues(
                '.cart_information input[type="checkbox"].shop-item-check-mobile'
            );
        });

        function getCartSelectCartItemsCheckedValues(elementSelector) {
            let checkedValues = [];
            $(elementSelector).each(function () {
                if ($(this).prop("checked")) {
                    checkedValues.push($(this).val());
                }
            });
            getCartSelectCartItemsRequest(checkedValues);
        }

        function getCartSelectCartItemsRequest(checkedValues) {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="_token"]').attr("content"),
                },
            });
            $.ajax({
                url: $("#get-cart-select-cart-items").data("route"),
                type: "POST",
                data: {
                    ids: checkedValues,
                },
                beforeSend: function () {
                    $("#loading").show();
                },
                success: function (response) {
                    $("#cart-summary").empty().html(response.htmlView);
                    toastr.success(response.message);
                    cartListQuantityUpdateInit();
                    setShippingIdFunctionCartDetails();
                    $('[data-toggle="tooltip"]').tooltip();
                    actionCheckoutFunctionInit();
                    couponCode();
                    updateNavCart();
                },
                complete: function () {
                    $("#loading").hide();
                },
            });
        }
    }
</script>
@endpush
