@extends('layouts.back-end.app')

@section('title', translate('deal_Product'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize">
                <img src="{{dynamicAsset(path: 'public/assets/back-end/img/inhouse-product-list.png')}}" class="mb-1 mr-1" alt="">
                {{translate('add_new_product')}}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 text-capitalize">{{$deal['title']}}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.deal.add-product',[$deal['id']])}}" method="post">
                            @csrf
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4 mt-3">
                                        <label for="category_id" class="title-color">{{ translate('category')}}</label>
                                        <select class="form-control" name="category_id" id="category_id">
                                            <option value="" selected disabled>{{ translate('select_category')}}</option>
                                            @foreach($categories as $category)
                                                <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4 mt-3">
                                        <label for="sub_category_id" class="title-color">{{ translate('sub_category')}}</label>
                                        <select class="form-control" name="sub_category_id" id="sub_category_id">
                                            <option value="" selected disabled>{{ translate('select_sub_category')}}</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mt-3">
                                        <label for="sub_sub_category_id" class="title-color">{{ translate('sub_sub_category')}}</label>
                                        <select class="form-control" name="sub_sub_category_id" id="sub_sub_category_id">
                                            <option value="" selected disabled>{{ translate('select_sub_sub_category')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mt-3">
                                        <label for="name" class="title-color">{{ translate('select_products')}}</label>
                                        <div class="dropdown select-product-search w-100">
                                            <div class="search-form" data-toggle="dropdown" aria-expanded="false">
                                                <button type="button" class="btn"><i class="tio-down-ui"></i></button>
                                                <input type="text" class="js-form-search form-control search-bar-input search-product" placeholder="{{translate('search_by_product_name').'...'}}" multiple>
                                            </div>
                                            <div class="dropdown-menu w-100 px-2">
                                                <div class="d-flex flex-column max-h-300 overflow-y-auto overflow-x-hidden search-result-box">
                                                    @include('admin-views.partials._search-product',['products'=>$products])
                                                </div>
                                            </div>
                                        </div>
                                        <div class="selected-products d-flex flex-wrap gap-3 mt-3" id="selected-products">
                                            @include('admin-views.partials._select-product')
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-3 justify-content-end">
                                <button type="button" class="btn btn-secondary font-weight-bold px-4 reset-selected-products">{{ translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary font-weight-bold px-4">{{ translate('add')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <h5 class="mb-0 text-capitalize">
                            {{ translate('product_table')}}
                            <span class="badge badge-soft-dark radius-50 fz-12 ml-1">{{ $dealProducts->total() }}</span>
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table
                            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('SL')}}</th>
                                <th>{{ translate('image')}}</th>
                                <th>{{ translate('name')}}</th>
                                <th>{{ translate('shop')}}</th>
                                <th>{{ translate('price')}}</th>
                                <th class="text-center">{{ translate('action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @php($companyName = getWebConfig(name: 'company_name'))
                                @foreach($dealProducts as $key => $product)
                                    <tr>
                                        <td>{{$dealProducts->firstitem() + $key}}</td>
                                        <td>
                                            <div class="avatar-60 d-flex align-items-center rounded">
                                                <img class="img-fluid aspect-1 rounded border" alt=""
                                                     src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}">
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:" target="_blank"
                                               class="font-weight-semibold title-color hover-c1">{{$product['name']}}</a>
                                        </td>
                                        <td>
                                            @if($product->added_by == 'admin')
                                                <a href="javascript:"
                                                   class="font-weight-semibold title-color hover-c1">
                                                    {{ $companyName }}
                                                </a>
                                            @else
                                                <a href="javascript:"
                                                   class="font-weight-semibold title-color hover-c1">{{$product?->seller?->shop['name'] ?? translate('shop_not_found').'!!!'}}</a>
                                            @endif
                                        </td>
                                        <td>{{setCurrencySymbol(usdToDefaultCurrency(amount: $product['unit_price']))}}</td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <a title="{{ translate ('delete')}}"
                                                   class="btn btn-outline-danger btn-sm delete-data-without-form"
                                                   data-action="{{route('admin.deal.delete-product')}}" data-id="{{$product['id']}}">
                                                    <i class="tio-delete"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table>
                            <tfoot>
                            {!! $dealProducts->links() !!}
                            </tfoot>
                        </table>
                    </div>
                    @if(count($dealProducts)==0)
                        @include('layouts.back-end._empty-state',['text'=>'no_product_select_yet'],['image'=>'default'])
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{dynamicAsset(path: 'public/assets/back-end/js/search-and-select-multiple-product.js')}}"></script>
    <script>
        $('#category_id').on('change', function () {
            let id = $(this).val();
            $.get({
                url: '{{url('/')}}/admin/category/get-sub-category?category_id=' + id,
                dataType: 'json',
                success: function (data) {
                    $('#sub_category_id').empty().append('<option value="" selected disabled>{{translate("select_sub_category")}}</option>');
                    $('#sub_sub_category_id').empty().append('<option value="" selected disabled>{{translate("select_sub_sub_category")}}</option>');
                    $.each(data, function (index, value) {
                        $('#sub_category_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                },
            });
        });

        $('#sub_category_id').on('change', function () {
            let id = $(this).val();
            $.get({
                url: '{{url('/')}}/admin/category/get-sub-category?category_id=' + id,
                dataType: 'json',
                success: function (data) {
                    $('#sub_sub_category_id').empty().append('<option value="" selected disabled>{{translate("select_sub_sub_category")}}</option>');
                    $.each(data, function (index, value) {
                        $('#sub_sub_category_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                },
            });
        });

        $(document).ready(function() {
            // Function to fetch products based on categories
            function filterProducts() {
                let category_id = $('#category_id').val();
                let sub_category_id = $('#sub_category_id').val();
                let sub_sub_category_id = $('#sub_sub_category_id').val();

                $.get({
                    url: '{{ route("admin.ajax-get-products") }}',
                    data: {
                        category_id: category_id,
                        sub_category_id: sub_category_id,
                        sub_sub_category_id: sub_sub_category_id
                    },
                    beforeSend: function () {
                        $('.search-result-box').html('<div class="text-center p-4"><i class="tio-dev-loader-1 spin"></i></div>');
                    },
                    success: function (data) {
                        $('.search-result-box').html(data.result);
                    },
                });
            }

            // Trigger filter when any dropdown changes
            $('#category_id, #sub_category_id, #sub_sub_category_id').on('change', function () {
                filterProducts();
            });
        });
    </script>
    <script>
        $('#category_id').on('change', function () {
            let id = $(this).val();
            $.get({
                url: '{{url('/')}}/admin/category/get-sub-category?category_id=' + id,
                dataType: 'json',
                success: function (data) {
                    $('#sub_category_id').empty().append('<option value="" selected disabled>{{translate("select_sub_category")}}</option>');
                    $('#sub_sub_category_id').empty().append('<option value="" selected disabled>{{translate("select_sub_sub_category")}}</option>');
                    $.each(data, function (index, value) {
                        $('#sub_category_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                },
            });
        });

        $('#sub_category_id').on('change', function () {
            let id = $(this).val();
            $.get({
                url: '{{url('/')}}/admin/category/get-sub-category?category_id=' + id,
                dataType: 'json',
                success: function (data) {
                    $('#sub_sub_category_id').empty().append('<option value="" selected disabled>{{translate("select_sub_sub_category")}}</option>');
                    $.each(data, function (index, value) {
                        $('#sub_sub_category_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                },
            });
        });

        $(document).ready(function() {
            // Function to fetch products based on categories
            function filterProducts() {
                let category_id = $('#category_id').val();
                let sub_category_id = $('#sub_category_id').val();
                let sub_sub_category_id = $('#sub_sub_category_id').val();

                $.get({
                    url: '{{ route("admin.ajax-get-products") }}',
                    data: {
                        category_id: category_id,
                        sub_category_id: sub_category_id,
                        sub_sub_category_id: sub_sub_category_id
                    },
                    beforeSend: function () {
                        $('.search-result-box').html('<div class="text-center p-4"><i class="tio-dev-loader-1 spin"></i></div>');
                    },
                    success: function (data) {
                        $('.search-result-box').html(data.result);
                    },
                });
            }

            // Trigger filter when any dropdown changes
            $('#category_id, #sub_category_id, #sub_sub_category_id').on('change', function () {
                filterProducts();
            });
        });
    </script>
@endpush


