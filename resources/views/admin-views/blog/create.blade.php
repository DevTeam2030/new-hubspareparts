@extends('layouts.back-end.app')

@section('title', translate('Create_New_Blog'))

@push('css_or_js')
    <link href="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">

        {{-- Page Header --}}
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/blog.png') }}" alt="">
                {{ translate('Blog_Setup') }}
            </h2>
        </div>

        <div class="row g-3">
            <div class="col-md-12">
                <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data" class="blog-setup-form">
                    @csrf

                    {{-- Card 1: Main Info --}}
                    <div class="card mb-3">
                        <div class="card-body text-start">

                            {{-- Debug Info --}}
                            {{-- Languages: {{ json_encode($languages) }} --}}
                            {{-- Default: {{ $defaultLanguage }} --}}

                            {{-- Language Tabs --}}
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach($languages as $lang)
                                    <li class="nav-item">
                                        <span class="nav-link form-system-language-tab cursor-pointer {{ $lang == $defaultLanguage ? 'active' : '' }}"
                                              id="{{ $lang }}-link">
                                            {{ ucfirst(getLanguageName($lang)) . '(' . strtoupper($lang) . ')' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="row">
                                {{-- Left: Translatable Title --}}
                                <div class="col-md-6">
                                    @foreach($languages as $lang)
                                        <div class="form-group {{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-language-form" id="{{ $lang }}-form">
                                            <label for="title" class="title-color">
                                                {{ translate('title') }}
                                                <span class="text-danger">*</span>
                                                ({{ strtoupper($lang) }})
                                            </label>
                                            <input type="text" name="title[{{ $lang }}]" class="form-control"
                                                   value="{{ old('title.' . $lang) }}"
                                                   placeholder="{{ translate('ex') }} : {{ translate('My_Blog_Title') }}"
                                                {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                        </div>
                                        <input type="hidden" name="lang[{{ $lang }}]" value="{{ $lang }}">
                                    @endforeach

                                        {{-- Writer --}}
                                        <div class="form-group">
                                            <label class="title-color">{{ translate('Writer') }}</label>
                                            <input type="text" name="writer" class="form-control"
                                                   value="{{ old('writer') }}"
                                                   placeholder="{{ translate('Ex') }}: {{ 'Jhon Milar' }}">
                                        </div>
                                </div>

                                {{-- Right: Category, Writer, Publish Date --}}
                                <div class="col-md-6">

                                    {{-- Category --}}
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="title-color mb-0">
                                                {{ translate('Category') }}
                                            </label>
{{--                                            <a data-bs-toggle="offcanvas" href="#offcanvasCategory" class="user-select-none small">--}}
{{--                                                {{ translate('Manage_Category') }}--}}
{{--                                            </a>--}}
                                        </div>
                                        <select class="form-control" name="blog_category" id="blog-category-select"
                                                data-text="{{ translate('select') }}"
                                                data-route="">
                                            <option value="" selected disabled>{{ translate('select') }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    @if(getDefaultLanguage() == 'en')
                                                        {{ $category->name }}
                                                    @else
                                                        {{ $category?->translations()->where('key', 'name')->where('locale', getDefaultLanguage())->first()?->value ?? $category?->name }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    {{-- Publish Date --}}
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('Publish_Date') }}</label>
                                        <input type="date" name="publish_date" class="form-control cursor-pointer"
                                               value="{{ date('Y-m-d') }}" placeholder="{{ translate('Select_Date') }}" autocomplete="off">
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Card 2: Description (per language) --}}
                    <div class="card mb-3">
                        <div class="card-body text-start">
                            <ul class="nav nav-tabs w-fit-content mb-4">
                                @foreach($languages as $lang)
                                    <li class="nav-item">
                                        <span class="nav-link form-system-language-tab cursor-pointer {{ $lang == $defaultLanguage ? 'active' : '' }}"
                                              id="{{ $lang }}-link">
                                            {{ ucfirst(getLanguageName($lang)) . '(' . strtoupper($lang) . ')' }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            @foreach($languages as $lang)
                                <input type="hidden" name="lang[{{ $lang }}]" value="{{ $lang }}" id="lang-{{ $lang }}">
                                <div class="form-group mb-0 {{ $lang != $defaultLanguage ? 'd-none' : '' }} form-system-description-language-form"
                                     id="{{ $lang }}-description-form">
                                    <label class="title-color">
                                        {{ translate('Description') }} ({{ strtoupper($lang) }})
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="description[{{ $lang }}]" id="description-{{ $lang }}"
                                          class="summernote-editor"
                                          {{ $lang == $defaultLanguage ? 'required' : '' }}></textarea>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Card 3: Thumbnail Image --}}
                    <div class="card mb-3">
                        <div class="card-body py-5">
                            <div class="mx-auto text-center max-w-170px">
                                <label class="d-block text-center font-weight-bold">
                                    {{ translate('Thumbnail') }}
                                    <small class="text-danger">{{ '(' . translate('size') . ': 325x100)' }}</small>
                                </label>

                                <label class="custom_upload_input d-block mx-2 cursor-pointer">
                                    <input type="file" name="image" id="blog-image"
                                           class="image-input image-preview-before-upload d-none"
                                           data-preview="#pre_img_viewer" accept="image/*">

                                    <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d--none">
                                        <i class="tio-delete"></i>
                                    </span>

                                    <div class="img_area_with_preview position-absolute z-index-2 p-0">
                                        <img id="pre_img_viewer" class="h-auto aspect-1 bg-white d-none" src="dummy" alt="">
                                    </div>

                                    <div class="placeholder-image">
                                        <div class="d-flex flex-column justify-content-center align-items-center aspect-1">
                                            <img alt="" width="33" src="{{ dynamicAsset(path: 'public/assets/back-end/img/icons/product-upload-icon.svg') }}">
                                            <h3 class="text-muted fz-12">{{ translate('Upload_Image') }}</h3>
                                        </div>
                                    </div>
                                </label>

                                <p class="text-muted mt-2 fz-12 m-0">
                                    {{ translate('image_format') }} : {{ "jpg, png, jpeg" }}
                                    <br>
                                    {{ translate('image_size') }} : {{ translate('max') }} {{ "2 MB" }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- SEO Section --}}
                    @include('admin-views.blog.partials._seo-section')

                    {{-- Hidden Fields --}}
                    <input type="hidden" name="status" id="status" value="1">
                    <input type="hidden" name="is_draft" id="is_draft" value="0">

                    {{-- Form Actions --}}
                    <div class="d-flex gap-3 justify-content-end mt-3">
                        <button type="reset" id="reset" class="btn btn-secondary px-4">
                            {{ translate('reset') }}
                        </button>
                        <a class="btn btn-outline-primary px-4 save-draft">
                            {{ translate('Save_to_Draft') }}
                        </a>
                        <button type="submit" class="btn btn--primary px-4 publish">
                            {{ translate('Publish') }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- Modals & Offcanvas --}}
    @include('admin-views.blog.partials._publish-modal')
{{--    @include('admin-views.blog.category.index')--}}
{{--    @include('admin-views.blog.partials.ai-sidebar')--}}

@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>

    @include('admin-views.blog.partials._blog-script')
    @include('admin-views.blog.category.partials._script')

    <script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>

    <script>
        $('.blog-setup-form').on('reset', function () {
            $(this).find('#pre_img_viewer').addClass('d-none');
            $(this).find('.placeholder-image').css('opacity', '1');
        });

        // Initialize Summernote editors
        $(document).ready(function() {
            $('.summernote-editor').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Extend existing language switching to handle description forms
            $(".form-system-language-tab").off("click").on("click", function (e) {
                e.preventDefault();
                $(".form-system-language-tab").removeClass("active");
                $(".form-system-language-form").addClass("d-none");
                $(".form-system-description-language-form").addClass("d-none");
                $(this).addClass("active");
                let form_id = this.id;
                let lang = form_id.split("-")[0];

                // Show title form for this language
                $("#" + lang + "-form").removeClass("d-none");
                $("." + lang + "-form").removeClass("d-none");

                // Show description form for this language
                $("#" + lang + "-description-form").removeClass("d-none");
                console.log("Showing description form for language:", lang);
            });
        });
    </script>
@endpush
