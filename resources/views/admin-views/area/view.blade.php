@extends('layouts.back-end.app')

@section('title', translate('delivery_center'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-10">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
                {{ translate('delivery_center') }}
            </h2>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.location.areas.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>{{ translate('delivery_center_name') }}</label>
                                <input type="text" name="name" class="form-control" placeholder="{{ translate('enter_delivery_center_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{ translate('governorate') }}</label>
                                <select class="form-control" name="governorate_id" required>
                                    @foreach($governorates as $governorate)
                                        <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>{{ translate('price_per_km') }}</label>
                                <input type="number" name="price_per_kg" class="form-control" placeholder="{{ translate('enter_price_per_km') }}" required>
                            </div>

                            <div class="form-group">
                                <label>{{ translate('max_distance_km') }}</label>
                                <input type="number" name="max_distance_km" class="form-control" placeholder="{{ translate('enter_max_distance') }}" required>
                            </div>

                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn-primary">{{ translate('submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- عرض جدول قائمة مراكز التوصيل -->
        <div class="row mt-20" id="area-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center">
                            <div class="">
                                <h5 class="text-capitalize d-flex gap-1">
                                    {{ translate('delivery_center_list') }}
                                    <span class="badge badge-soft-dark radius-50 fz-12">{{ $areas->total() }}</span>
                                </h5>
                            </div>
                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input type="search" name="searchValue" class="form-control"
                                               placeholder="{{ translate('search_by_delivery_center') }}"
                                               value="{{ request('searchValue') }}">
                                        <button type="submit" class="btn btn--primary">{{ translate('search') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('id') }}</th>
                                <th>{{ translate('delivery_center_name') }}</th>
                                <th>{{ translate('governorate') }}</th>
                                <th>{{ translate('price_per_km') }}</th>
                                <th>{{ translate('max_distance') }}</th>
                                <th class="text-center">{{ translate('action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($areas as $area)
                                <tr>
                                    <td>{{ $area->id }}</td>
                                    <td>{{ $area->name }}</td>
                                    <td>{{ $area->governorate?->name }}</td>
                                    <td>{{ $area->price_per_kg }}</td>
                                    <td>{{ $area->max_distance_km }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.location.areas.edit', $area->id) }}" class="btn btn-outline-info btn-sm">{{ translate('edit') }}</a>
                                        <form action="{{ route('admin.location.areas.destroy', $area->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">{{ translate('delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{ $areas->links() }}
                        </div>
                    </div>

                    @if(count($areas) == 0)
                        @include('layouts.back-end._empty-state',['text'=>'no_area_found'],['image'=>'default'])
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
