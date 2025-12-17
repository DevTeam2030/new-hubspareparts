@extends('layouts.back-end.app')
@section('title', translate('order_List'))

@section('content')
    <div class="content container-fluid">
        {{-- Header --}}
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img src="{{ dynamicAsset('public/assets/back-end/img/all-orders.png') }}" width="24" alt="">
                <span class="page-header-title">
                    @if($status == 'processing')
                        {{ translate('packaging') }}
                    @elseif($status == 'failed')
                        {{ translate('failed_to_Deliver') }}
                    @elseif($status == 'all')
                        {{ translate('all') }}
                    @else
                        {{ translate(str_replace('_',' ',$status)) }}
                    @endif
                </span>
                {{ translate('orders') }}
            </h2>
            <span class="badge badge-soft-dark radius-50">{{ $orders->total() }}</span>
        </div>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('admin.orders.list',['status'=>request('status')]) }}" method="GET" id="form-data">
                    {{-- … كل حقول الفلترة اللي عندك نفسها بلا تغيير … --}}
                </form>
            </div>
        </div>

        {{-- Orders Table (grouped by order_group_id) --}}
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">{{ translate('order_list') }}
                    <span class="badge badge-secondary">{{ $orders->total() }}</span>
                </h5>

                <div class="table-responsive">
                    <table class="table table-hover table-borderless">
                        <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>{{ translate('order_ID') }}</th>
                            <th>{{ translate('total') }}</th>
                            <th>{{ translate('order_Status') }}</th>
                            <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $row = $orders->firstItem() - 1; @endphp

                        @forelse($groupedOrders as $groupId => $groupOrders)
                            {{-- Group Header --}}
                            <tr class="table-secondary">
                                <td colspan="5">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>
                                            {{ translate('batch') }} {{ $loop->iteration }}:
                                            {{ $groupOrders->count() }} {{ translate('orders') }}
                                            ({{ $groupId }})
                                        </strong>
                                        <a href="{{ route('admin.orders.generate-invoice', [$groupId]) }}"
                                           class="btn btn-sm btn-outline-info">
                                            <i class="tio-download"></i> {{ translate('invoice') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            {{-- Individual orders in this group --}}
                            @foreach($groupOrders as $order)
                                @php $row++; @endphp
                                <tr>
                                    <td>{{ $row }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.details', ['id' => $order->id]) }}">
                                            {{ $order->id }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ setCurrencySymbol( usdToDefaultCurrency($order->order_amount) ) }}
                                        @if($order->payment_status=='paid')
                                            <span class="badge badge-success">{{ translate('paid') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ translate('unpaid') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                'pending'          => 'info',
                                                'processing'       => 'warning',
                                                'out_for_delivery' => 'warning',
                                                'confirmed'        => 'success',
                                                'delivered'        => 'success',
                                                'failed'           => 'danger',
                                                'canceled'         => 'danger',
                                                'returned'         => 'secondary',
                                            ];
                                            $badge = $statusMap[$order->order_status] ?? 'primary';
                                        @endphp
                                        <span class="badge badge-{{ $badge }}">
                                                {{ translate(str_replace('_',' ',$order->order_status)) }}
                                            </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.orders.details', ['id' => $order->id]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="tio-invisible"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                        @empty
                            <tr>
                                <td colspan="5" class="text-center">{{ translate('no_order_found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-end mt-3">
                    {!! $orders->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset('public/assets/back-end/js/owl.min.js') }}"></script>
    <script>
        'use strict';
        $('.address-slider').owlCarousel({
            margin: 16,
            loop: false,
            autoWidth: true,
        });
    </script>
@endpush
