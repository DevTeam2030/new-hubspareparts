<div class="category-create-form">
    <div class="card shadow-sm">
        <div class="card-header shadow-none">
            <h4 class="m-0">{{ translate('Add_New_Category') }}</h4>
        </div>
        <div class="card-body">



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

            <form action="{{ route('admin.blog.category.add') }}" method="POST" class="category-form-submit" id="blog-category-add-form">
                @csrf
                <div class="mb-4">
                    <div class="category-section">
{{--                        @foreach($languages as $lang)--}}
{{--                        <div class="{{$lang != $defaultLanguage ? 'd-none':''}} category-lang-tab category-lang-{{ $lang }}-tab" data-lang="{{ $lang }}">--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="form-label category-label">{{ translate('Category_Name') }} ({{strtoupper($lang)}})</label>--}}
{{--                                <input type="text" name="name[{{$lang}}]" class="form-control category_name" id="{{$lang}}_category_name" placeholder="{{translate('ex').':'.translate('LUX')}}">--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <input type="hidden" name="lang[{{$lang}}]" value="{{$lang}}" id="lang-{{$lang}}">--}}
{{--                        @endforeach--}}

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
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-3 justify-content-end">
                    <button type="reset" id="reset" class="btn btn-secondary">
                        {{ translate('Reset') }}
                    </button>
                    <button class="btn btn-primary category-form-submit-btn"
                            data-type="add"
                            data-form="#blog-category-add-form" data-route="{{ route('admin.blog.category.add') }}"
                    >
                        {{ translate('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
