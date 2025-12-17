@extends('layouts.back-end.app')

@section('title', translate('edit_delivery_time'))

@section('content')
    <div class="content container-fluid">
        <h2 class="h1">{{ translate('edit_delivery_time') }}</h2>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.location.delivery-times.update', $deliveryTime->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>{{ translate('governorate') }}</label>
                                <select class="form-control" name="governorate_id" required>
                                    @foreach($governorates as $governorate)
                                        <option value="{{ $governorate->id }}" {{ $deliveryTime->governorate_id == $governorate->id ? 'selected' : '' }}>
                                            {{ $governorate->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>{{ translate('start_time') }}</label>
                                <input type="time" name="start_time" value="{{ $deliveryTime->start_time }}" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>{{ translate('end_time') }}</label>
                                <input type="time" name="end_time" value="{{ $deliveryTime->end_time }}" class="form-control" required>
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
