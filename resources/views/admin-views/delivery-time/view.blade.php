@extends('layouts.back-end.app')

@section('title', translate('delivery_time_setup'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 mb-0 d-flex gap-10">
                <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/brand-setup.png') }}" alt="">
                {{ translate('delivery_time_setup') }}
            </h2>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.location.delivery-times.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>{{ translate('governorate') }}</label>
                                <select class="form-control" name="governorate_id" required>
                                    @foreach($governorates = \App\Models\Governorate::all() as $governorate)
                                        <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ translate('start_time') }}</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>{{ translate('end_time') }}</label>
                                <input type="time" name="end_time" class="form-control" required>
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

        <!-- Times Table -->
        <div class="row mt-20" id="delivery-time-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="px-3 py-4">
                        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center">
                            <div>
                                <h5 class="text-capitalize d-flex gap-1">
                                    {{ translate('delivery_times_list') }}
                                    <span class="badge badge-soft-dark radius-50 fz-12">{{ $deliveryTimes->count() }}</span>
                                </h5>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{ translate('id') }}</th>
                                <th>{{ translate('governorate') }}</th>
                                <th>{{ translate('start_time') }}</th>
                                <th>{{ translate('end_time') }}</th>
                                <th class="text-center">{{ translate('action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($deliveryTimes as $deliveryTime)
                                <tr>
                                    <td>{{ $deliveryTime->id }}</td>
                                    <td>{{ $deliveryTime->governorate->name ?? '' }}</td>
                                    <td>
                                        @php
                                            $start = \Carbon\Carbon::parse($deliveryTime->start_time);
                                            $startFormatted = $start->format('g:i') . ' ' . ($start->format('A') === 'AM' ? 'صباحاً' : 'مساءً');
                                        @endphp
                                        {{ $startFormatted }}
                                    </td>
                                    <td>
                                        @php
                                            $end = \Carbon\Carbon::parse($deliveryTime->end_time);
                                            $endFormatted = $end->format('g:i') . ' ' . ($end->format('A') === 'AM' ? 'صباحاً' : 'مساءً');
                                        @endphp
                                        {{ $endFormatted }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.location.delivery-times.edit', $deliveryTime->id) }}" class="btn btn-outline-info btn-sm">
                                            {{ translate('edit') }}
                                        </a>
                                        <form action="{{ route('admin.location.delivery-times.destroy', $deliveryTime->id) }}" method="POST" style="display:inline;">
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
                            {{ $deliveryTimes->links() }}
                        </div>
                    </div>

                    @if(count($deliveryTimes) == 0)
                        @include('layouts.back-end._empty-state',['text'=>'no_delivery_times_found'],['image'=>'default'])
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
