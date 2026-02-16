<div class="modal fade" id="product-add-modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="border-bottom">
                    <h4>{{ translate('Add_Product') }}</h4>
                    <p>
                        {{ translate('search_product') }} & {{ translate('add_to_your_clearance_list') }}
                    </p>
                </div>
                <form action="{{route('admin.deal.clearance-sale.add-product')}}" method="post" class="clearance-add-product">
                    @csrf
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
                    <div class="mt-3">
                        <label class="form-label">{{ translate('Products') }}</label>
                        <div class="dropdown select-clearance-product-search w-100">
                            <div class="search-form" data-toggle="dropdown" aria-expanded="false">
                                <input type="text" class="form-control pl-5 search-product-for-clearance-sale" placeholder="{{ translate('Search_Product') }}" multiple>
                                <span
                                    class="tio-search position-absolute left-0 top-0 h-42px d-flex align-items-center pl-2"></span>
                            </div>
                            <div class="dropdown-menu w-100 px-2">
                                <div class="d-flex flex-column max-h-300 overflow-y-auto overflow-x-hidden search-result-box">
                                    @include('admin-views.deal.clearance-sale.partials._search-product', ['products' => $products])
                                </div>
                            </div>
                        </div>
                        <div class="selected-products d-flex flex-wrap g-3 mt-3 clearance-selected-products" id="selected-products">
                            @include('admin-views.partials._select-product')
                        </div>
                    </div>
                    <div class="p-4 bg-chat rounded text-center mt-3 search-and-add-product">
                        <img src="{{ dynamicAsset('public/assets/back-end/img/empty-product.png') }}" width="64"
                             alt="">
                        <div class="mx-auto my-3 max-w-353px">
                            {{ translate('search') }} & {{ translate('and_add_product_from_the_list') }}
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-3">
                        <button class="btn btn-secondary font-weight-semibold"
                                type="reset" data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button class="btn btn--primary font-weight-semibold clearance-product-add-submit" id="add-products-btn"
                                type="button">{{ translate('Add_Products') }}</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@push('script')
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
                url: '{{ route("admin.ajax-get-products-clearance") }}',
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
