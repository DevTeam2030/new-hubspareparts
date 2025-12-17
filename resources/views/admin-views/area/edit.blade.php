@extends('layouts.back-end.app')

@section('title', translate('edit_delivery_center'))

@section('content')
    <div class="content container-fluid">
        <h2 class="h1">{{ translate('edit_delivery_center') }}</h2>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.location.areas.update', [$area->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>{{ translate('delivery_center_name') }}</label>
                                <input type="text" name="name" value="{{ $area->name }}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>{{ translate('governorate') }}</label>
                                <select class="form-control" name="governorate_id" required>
                                    <option value="{{ $area->governorate?->id }}" selected>{{ $area->governorate?->name }}</option>
                                    @foreach($governorates as $governorate)
                                        <option value="{{ $governorate?->id }}">{{ $governorate?->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ translate('price_per_km') }}</label>
                                <input type="number" name="price_per_km" value="{{ $area->price_per_kg }}" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>{{ translate('max_distance_km') }}</label>
                                <input type="number" name="max_distance_km" value="{{ $area->max_distance_km }}" class="form-control" required>
                            </div>

                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn-primary">{{ translate('update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
