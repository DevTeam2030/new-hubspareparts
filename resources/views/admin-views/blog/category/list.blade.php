@extends('layouts.back-end.app')

@section('title', translate('blog_category_List'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand.png') }}" alt="">
                {{ translate('blog_category_List') }}
                <span class="badge badge-soft-dark radius-50 fz-14">{{ $categories->total() }}</span>
            </h2>
        </div>
        <div class="row mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                            placeholder="{{ translate('search_by_category_name') }}" aria-label="{{ translate('search_by_category_name') }}" value="{{ request('searchValue') }}" required>
                                        <button type="submit" class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">
                                <a href="{{ route('admin.blog.category.add-view') }}" class="btn btn--primary text-nowrap">
                                    <i class="tio-add"></i>
                                    <span class="ps-2">{{ translate('add_new_category') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                                <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
{{--                                    <th>{{ translate('category_Image') }}</th>--}}
                                    <th class="max-width-100px">{{ translate('name') }}</th>
                                    <th class="text-center">{{ translate('total_Blogs') }}</th>
                                    <th class="text-center">{{ translate('status') }}</th>
                                    <th class="text-center"> {{ translate('action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($categories as $key => $category)
                                    <tr>
                                        <td>{{ $categories->firstItem()+$key }}</td>
{{--                                        <td>--}}
{{--                                            <div class="avatar-60 d-flex align-items-center rounded">--}}
{{--                                                @if($category->image)--}}
{{--                                                    <img class="img-fluid" alt="{{ $category->image_alt_text ?? 'Category Image' }}"--}}
{{--                                                         src="{{ getStorageImages(path:$category->image, type: 'blog-category') }}">--}}
{{--                                                @else--}}
{{--                                                    <div class="bg-gray-200 d-flex align-items-center justify-content-center h-100 w-100">--}}
{{--                                                        <i class="tio-category text-muted"></i>--}}
{{--                                                    </div>--}}
{{--                                                @endif--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
                                        <td class="overflow-hidden max-width-100px">
                                            <span data-toggle="tooltip" data-placement="right" title="{{$category->name}}">
                                                 {{ Str::limit($category->name,20) }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $category->blogs()->count() }}</td>
                                        <td>
                                            <form action="{{route('admin.blog.category.status-update') }}" method="post" id="category-status{{$category->id}}-form">
                                                @csrf
                                                <input type="hidden" name="category_id" value="{{$category->id}}">
                                                <label class="switcher mx-auto">
                                                    <input type="checkbox" class="switcher_input toggle-switch-message" name="status"
                                                           id="category-status{{ $category->id }}" value="1" {{ $category->status == 1 ? 'checked' : '' }}
                                                           data-modal-id = "toggle-status-modal"
                                                           data-toggle-id = "category-status{{ $category->id }}"
                                                           data-on-image = "category-status-on.png"
                                                           data-off-image = "category-status-off.png"
                                                           data-on-title = "{{ translate('Want_to_Turn_ON').' '.$category->name.' '. translate('status') }}"
                                                           data-off-title = "{{ translate('Want_to_Turn_OFF').' '.$category->name.' '.translate('status') }}"
                                                           data-on-message = "<p>{{ translate('if_enabled_this_category_will_be_available_on_the_website_and_customer_app') }}</p>"
                                                           data-off-message = "<p>{{ translate('if_disabled_this_category_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a class="btn btn-outline-info btn-sm square-btn" title="{{ translate('edit') }}"
                                                    href="{{ route('admin.blog.category.edit', [$category->id]) }}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                <a class="btn btn-outline-danger btn-sm delete-category square-btn" title="{{ translate('delete') }}"
                                                   data-blog-count = "{{ $category->blogs()->count() }}"
                                                   data-text="{{translate('there_were_').$category->blogs()->count().translate('_blogs_under_this_category').'.'.translate('please_update_their_category_from_the_below_list_before_deleting_this_one').'.'}}"
                                                   id="{{ $category->id }}">
                                                    <i class="tio-delete"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{ $categories->links() }}
                        </div>
                    </div>
                    @if(count($categories)==0)
                        @include('layouts.back-end._empty-state',['text'=>'no_category_found'],['image'=>'default'])
                    @endif
                </div>
            </div>
        </div>
    </div>
    <span id="route-admin-blog-category-delete" data-url="{{ route('admin.blog.category.delete') }}"></span>
    <span id="route-admin-blog-category-status-update" data-url="{{ route('admin.blog.category.status-update') }}"></span>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
    <script>
        // Handle delete category
        $('.delete-category').on('click', function() {
            const categoryId = $(this).attr('id');
            const blogCount = $(this).data('blog-count');
            const message = $(this).data('text');

            if (blogCount > 0) {
                // Show warning modal if there are blogs
                alert(message);
                return;
            }

            if (confirm('{{ translate("Are_you_sure_to_delete_this_category") }}?')) {
                $.ajax({
                    url: $('#route-admin-blog-category-delete').data('url'),
                    type: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'category_id': categoryId
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }
        });

        // Handle status toggle
        $('.toggle-switch-message').on('change', function() {
            const formId = $(this).closest('form').attr('id');
            const form = $('#' + formId);
            const status = $(this).is(':checked') ? 1 : 0;
            const categoryId = form.find('input[name="category_id"]').val();

            $.ajax({
                url: $('#route-admin-blog-category-status-update').data('url'),
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'category_id': categoryId,
                    'status': status
                },
                success: function(response) {
                    console.log(response.message);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
    </script>
@endpush
