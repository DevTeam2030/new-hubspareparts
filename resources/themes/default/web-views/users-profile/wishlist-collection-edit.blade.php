@extends('layouts.front-end.app')

@section('title', __('Add Collection'))

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

                        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                            <h5 class="font-bold m-0 fs-16">{{ __('Add Collection') }}</h5>
                            <button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1 d-lg-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z"
                                          fill="white"/>
                                </svg>
                            </button>

                        </div>

                        <div class="card-inner">
                            <form class="mt-3 px-sm-2 pb-2" action="{{route('update-wishlist-collection')}}" method="post"
                                  id="profile_form"
                                  enctype="multipart/form-data">
                                @csrf

                                <div class="row pb-1">
                                    <div class="col-md-6">
                                        <input type="hidden" name="id" value="{{$collection->id}}">

                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="form-group col-md-6 mb-0">
                                        <label for="firstName"
                                               class="mb-2 text-capitalize">{{translate('name')}} </label>
                                        <input type="text" class="form-control" id="name" name="name"  value="{{$collection->name}}" required>
                                    </div>
                                    <div class="form-group col-md-6 mb-0">
                                        <label for="lastName"
                                               class="mb-2 text-capitalize"> {{translate('arabic_name')}} </label>
                                        <input type="text" class="form-control"  value="{{$collection->name_ar}}" id="name_ar" name="name_ar">
                                    </div>
                                    <div class="form-group col-md-6 mb-0">
                                        <label for="due_date"
                                               class="mb-2 text-capitalize"> {{translate('due_date')}} </label>
                                        <input type="date" class="form-control" id="due_date" value="{{$collection->due_date}}" name="due_date">
                                    </div>
                                    <div class="form-group col-md-6 mb-0">
                                        <label for="due_date" class="mb-2 text-capitalize"> {{translate('priority')}} </label>
                                        <select class="form-control" name="priority" id="priority">
                                            <option value="">{{ __('Select priority') }}</option>
                                            <option value="normal" @if($collection->priority=='normal') selected @endif> {{ __('Normal') }}</option>
                                            <option value="medium" @if($collection->priority=='medium') selected @endif> {{ __('Medium') }}</option>
                                            <option value="urgent" @if($collection->priority=='urgent') selected @endif> {{ __('Urgent') }}</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6 mb-0">
                                        <label for="due_date"
                                               class="mb-2 text-capitalize"> {{translate('notes')}} </label>
                                        <textarea  type="text" name="notes" class="form-control" placeholder="{{ __('Notes') }}">{{ old("notes") ?? $collection->notes }}</textarea>
                                    </div>


                                    <div class="col-12 text-end d-none d-md-block">
                                        <button type="submit" class="btn btn--primary px-4 fs-14 font-semi-bold py-2">
                                            {{ translate('save') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
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
