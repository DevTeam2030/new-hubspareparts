@extends('layouts.back-end.app')

@section('title', translate('edit_blog'))

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
            <a href="{{ route('frontend.blog.details', ['slug' => $blog['slug'], 'source' => 'edit']) }}" target="_blank" class="btn btn-outline-primary ms-auto"  data-bs-toggle="tooltip" data-placement="bottom" title=""
            >
                {{ translate('view_preview') }}
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15" fill="none">
                    <path
                        d="M1.75 4.14583C1.75 3.64303 1.94974 3.16081 2.30528 2.80528C2.66081 2.44974 3.14303 2.25 3.64583 2.25H10.3542C10.857 2.25 11.3392 2.44974 11.6947 2.80528C12.0503 3.16081 12.25 3.64303 12.25 4.14583V10.8542C12.25 11.357 12.0503 11.8392 11.6947 12.1947C11.3392 12.5503 10.857 12.75 10.3542 12.75H3.64583C3.14303 12.75 2.66081 12.5503 2.30528 12.1947C1.94974 11.8392 1.75 11.357 1.75 10.8542V4.14583ZM3.64583 3.125C3.37509 3.125 3.11544 3.23255 2.924 3.424C2.73255 3.61544 2.625 3.87509 2.625 4.14583V10.8542C2.625 11.4177 3.08233 11.875 3.64583 11.875H10.3542C10.6249 11.875 10.8846 11.7674 11.076 11.576C11.2674 11.3846 11.375 11.1249 11.375 10.8542V4.14583C11.375 3.87509 11.2674 3.61544 11.076 3.424C10.8846 3.23255 10.6249 3.125 10.3542 3.125H3.64583ZM3.5 5.3125C3.5 4.749 3.95733 4.29167 4.52083 4.29167H9.47917C10.0427 4.29167 10.5 4.749 10.5 5.3125V6.1875C10.5 6.45824 10.3924 6.7179 10.201 6.90934C10.0096 7.10078 9.74991 7.20833 9.47917 7.20833H4.52083C4.25009 7.2083 3.99044 7.10078 3.799 6.90934C3.60755 6.7179 3.5 6.45824 3.5 6.1875V5.3125Z"
                        fill="currentColor" fill-opacity="0.85"/>
                </svg>
            </a>
        </div>

        <div class="row g-3">
            <div class="col-md-12">
                <form action="{{ route('admin.blog.update') }}" method="POST" enctype="multipart/form-data" class="blog-setup-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $blog->id }}">

                    {{-- Card 1: Main Info --}}
                    <div class="card mb-3">
                        <div class="card-body text-start">

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
                                                   value="{{ $blog?->translations->where('locale', $lang)->where('key', 'title')->first()?->value ?? $blog?->title ?? old('title.' . $lang) }}"
                                                   placeholder="{{ translate('ex') }} : {{ translate('My_Blog_Title') }}"
                                                {{ $lang == $defaultLanguage ? 'required' : '' }}>
                                        </div>
                                        <input type="hidden" name="lang[{{ $lang }}]" value="{{ $lang }}">
                                    @endforeach

                                        {{-- Writer --}}
                                        <div class="form-group">
                                            <label class="title-color">{{ translate('Writer') }}</label>
                                            <input type="text" name="writer" class="form-control"
                                                   value="{{ $blog?->writer ?? old('writer') }}"
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
                                        </div>
                                        <select class="form-control" name="blog_category" id="blog-category-select"
                                                data-text="{{ translate('select') }}"
                                                data-route="">
                                            <option value="" selected disabled>{{ translate('select') }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $blog->category_id == $category->id ? 'selected' : '' }}>
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
                                               value="{{ $blog?->publish_date?->format('Y-m-d') ?? date('Y-m-d') }}" placeholder="{{ translate('Select_Date') }}" autocomplete="off">
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
                                          {{ $lang == $defaultLanguage ? 'required' : '' }}>{{ $blog?->translations->where('locale', $lang)->where('key', 'description')->first()?->value ?? $blog?->description ?? old('description.' . $lang) }}</textarea>
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
                                        <img id="pre_img_viewer" class="h-auto aspect-1 bg-white {{ $blog?->image ? '' : 'd-none' }}" 
                                             src="{{ $blog?->image ? getStorageImages(path: $blog->image_full_url, type:'backend-product') : 'dummy' }}" alt="">
                                    </div>

                                    <div class="placeholder-image {{ $blog?->image ? 'd-none' : '' }}">
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
                    @include('admin-views.blog.partials._edit-seo-section')

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
@endsection

@push('script')
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/plugins/summernote/summernote.min.js') }}"></script>
    <script src="{{ dynamicAsset(path: 'public/assets/back-end/libs/quill-editor/quill-editor-init.js') }}"></script>

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
