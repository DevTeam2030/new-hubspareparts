@extends('layouts.back-end.app')

@section('title', translate('edit_governorate'))

@section('content')
    <div class="content container-fluid">
        <h2 class="h1">{{ translate('edit_governorate') }}</h2>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-start">
                        <form action="{{ route('admin.location.governorates.update', [$governorate->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>{{ translate('governorate_name') }}</label>
                                <input type="text" name="name" value="{{ $governorate->name }}" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>{{ translate('note') }}</label>
                                <input type="text" name="note" value="{{ $governorate->note }}" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>{{ translate('min_shipping_cost') }}</label>
                                <input type="number" step="0.01" name="min_shipping_cost" value="{{ $governorate->min_shipping_cost }}" class="form-control" required>
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
