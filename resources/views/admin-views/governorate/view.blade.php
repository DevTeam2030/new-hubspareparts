@extends('layouts.back-end.app')

@section('title', translate('governorate_setup'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-10">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
                {{ translate('governorate_setup') }}
            </h2>
        </div>

        <!-- Governorate Create Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.location.governorates.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>{{ translate('governorate_name') }}</label>
                                <input type="text" name="name" class="form-control" placeholder="{{ translate('enter_governorate_name') }}" required>
                            </div>
                            <div class="form-group">
                                <label>{{ translate('note') }}</label>
                                <input type="text" name="note" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>{{ translate('min_shipping_cost') }}</label>
                                <input type="number" step="0.01" name="min_shipping_cost" class="form-control" placeholder="{{ translate('enter_min_shipping_cost') }}" required>
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

        <!-- Governorate Table Display -->
        <div class="row mt-20" id="governorate-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center">
                            <div class="">
                                <h5 class="text-capitalize d-flex gap-1">
                                    {{ translate('governorate_list') }}
                                    <span class="badge badge-soft-dark radius-50 fz-12">{{ $governorates->count() }}</span>
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
                                               placeholder="{{ translate('search_by_governorate_name') }}"
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
                                <th>{{ translate('governorate_name') }}</th>
                                <th>{{ translate('note') }}</th>
                                <th>{{ translate('min_shipping_cost') }}</th>
                                <th>{{ translate('action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($governorates as $governorate)
                                <tr>
                                    <td>{{ $governorate->id }}</td>
                                    <td>{{ $governorate->name }}</td>
                                    <td>{{ $governorate->note }}</td>
                                    <td>{{ $governorate->min_shipping_cost }}</td>
                                    <td>
                                        <a class="btn btn-outline-info btn-sm square-btn"
                                           title="{{ translate('edit') }}"
                                           href="{{ route('admin.location.governorates.edit', [$governorate->id]) }}">
                                            <i class="tio-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.location.governorates.destroy', [$governorate->id]) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm square-btn" title="{{ translate('delete') }}">
                                                <i class="tio-delete"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            {{ $governorates->links() }}
                        </div>
                    </div>
                    @if(count($governorates) == 0)
                        @include('layouts.back-end._empty-state', ['text' => 'no_governorate_found'], ['image' => 'default'])
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
