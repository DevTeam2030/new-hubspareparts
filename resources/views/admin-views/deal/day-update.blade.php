@extends('layouts.back-end.app')
@section('title', translate('deal_Update'))
@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{dynamicAsset(path: 'public/assets/back-end/img/deal_of_the_day.png')}}" alt="">
                {{translate('update_Deal_of_The_Day')}}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.deal.day-update',[$deal['id']])}}"
                              class="text-start onsubmit-disable-action-button"
                              method="post">
                            @csrf
                            @php($language = getWebConfig(name:'pnc_language'))
                            @php($defaultLanguage = 'en')
                            @php($defaultLanguage = $language[0])
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach($language as $lang)
                                    <li class="nav-item text-capitalize">
                                        <a class="nav-link lang-link {{$lang == $defaultLanguage? 'active':''}}"
                                           href="javascript:"
                                           id="{{$lang}}-link">{{getLanguageName($lang).'('.strtoupper($lang).')'}}</a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="form-group">
                                @foreach($language as $lang)
                                        <?php
                                            if (count($deal['translations'])) {
                                                $translate = [];
                                                foreach ($deal['translations'] as $t) {
                                                    if ($t->locale == $lang && $t->key == "title") {
                                                        $translate[$lang]['title'] = $t->value;
                                                    }
                                                }
                                            }
                                        ?>
                                    <div class="row {{$lang != $defaultLanguage ? 'd-none':''}} lang-form"
                                         id="{{$lang}}-form">
                                        <div class="col-md-12">
                                            <label for="name" class="title-color">{{ translate('title')}}
                                                ({{strtoupper($lang)}})</label>
                                            <input type="text" name="title[]"
                                                   value="{{$lang==$defaultLanguage?$deal['title']:($translate[$lang]['title']??'')}}"
                                                   class="form-control" id="title"
                                                   placeholder="{{translate('ex')}} : {{translate('LUX')}}">
                                        </div>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                @endforeach

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
                                    <div class="col-md-12 mt-3">
                                        <label for="name" class="title-color">{{ translate('products')}}</label>
                                        <input type="text" class="product_id" name="product_id"
                                               value="{{ $deal['product_id'] }}" hidden>
                                        <div class="dropdown select-product-search w-100">
                                            <button class="form-control text-start dropdown-toggle select-product-button"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" type="button">
                                                {{isset($deal->product) ? $deal->product->name : translate('product_not_found')}}
                                            </button>
                                            <div class="dropdown-menu w-100 px-2">
                                                <div class="search-form mb-3">
                                                    <button type="button" class="btn"><i class="tio-search"></i>
                                                    </button>
                                                    <input type="text"
                                                           class="js-form-search form-control search-bar-input search-product"
                                                           placeholder="{{translate('search menu').'...'}}">
                                                </div>
                                                <div class="d-flex flex-column gap-3 max-h-200 overflow-y-auto overflow-x-hidden search-result-box">
                                                    @include('admin-views.partials._search-product',['products'=>$products])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3">
                                <button type="reset" id="reset"
                                        class="btn btn-secondary reset-button">{{ translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{ translate('update')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{dynamicAsset(path: 'public/assets/back-end/js/search-product.js')}}"></script>
    <script src="{{dynamicAsset(path: 'public/assets/back-end/js/admin/deal.js')}}"></script>
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

