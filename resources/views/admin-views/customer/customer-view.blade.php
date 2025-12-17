@extends('layouts.back-end.app')

@section('title', translate('customer_Details'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ dynamicAsset(path:'public/assets/back-end/css/owl.min.css') }}">
@endpush

@section('content')
    <div class="content container-fluid">
        {{-- Breadcrumb / Header --}}
        <div class="d-print-none pb-2">
            <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
                <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/add-new-seller.png') }}" alt="">
                {{ translate('customer_details') }}
            </h2>
        </div>

        {{-- Customer Card --}}
        <div class="row g-2 mb-4">
            <div class="col-xl-6 col-xxl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h4 class="mb-4 d-flex align-items-center gap-2">
                            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/vendor-information.png') }}" alt="">
                            {{ translate('customer').' #'.$customer->id }}
                        </h4>
                        <div class="customer-details-new-card d-flex gap-3">
                            <img src="{{ getStorageImages(path: $customer->image_full_url, type: 'backend-profile') }}"
                                 alt="avatar" class="rounded-circle" width="80" height="80">
                            <div>
                                <h6 class="name">{{ $customer->f_name.' '.$customer->l_name }}</h6>
                                <ul class="list-unstyled mb-0">
                                    <li><strong>{{ translate('contact') }}:</strong> {{ $customer->phone ?: '-' }}</li>
                                    <li><strong>{{ translate('email') }}:</strong> {{ $customer->email ?: '-' }}</li>
                                    <li><strong>{{ translate('joined_date') }}:</strong> {{ date('d M Y', strtotime($customer->created_at)) }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Saved Addresses --}}
            @if($customer->addresses->isNotEmpty())
                <div class="col-xl-6 col-xxl-8">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="mb-4">{{ translate('saved_address') }}</h4>
                            <div class="owl-carousel address-slider">
                                @foreach($customer->addresses as $address)
                                    <div class="card p-3">
                                        <h6>{{ $address->address_type .' ('. ($address->is_billing? translate('billing_address'): translate('shipping_address')) .')' }}</h6>
                                        <p class="mb-1"><strong>{{ translate('name') }}:</strong> {{ $address->contact_person_name }}</p>
                                        <p class="mb-1"><strong>{{ translate('phone') }}:</strong> {{ $address->phone }}</p>
                                        <p class="mb-0"><strong>{{ translate('address') }}:</strong> {{ $address->address }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Order Stats --}}
        <div class="row g-2 mb-4">
            @foreach([
                'total_order' => 'total-order.png',
                'ongoing'     => 'ongoing.png',
                'completed'   => 'completed.png',
                'canceled'    => 'canceled.png',
                'returned'    => 'returned.png',
                'failed'      => 'failed.png',
                'refunded'    => 'refunded.png',
            ] as $statusKey => $icon)
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card text-center p-3">
                        <img width="24" src="{{ dynamicAsset("public/assets/back-end/img/customer/{$icon}") }}" alt="">
                        <h6 class="mt-2">{{ translate(str_replace('_',' ',$statusKey)) }}</h6>
                        <span class="h4">{{ $orderStatusArray[$statusKey] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Orders Table (grouped by order_group_id) --}}
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ translate('orders') }}
                    <span class="badge badge-secondary">{{ $orders->total() }}</span>
                </h5>

                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('order_ID') }}</th>
                        <th>{{ translate('total') }}</th>
                        <th>{{ translate('order_Status') }}</th>
                        <th class="text-center">{{ translate('action') }}</th>
                    </tr>
                    </thead>
                    <tbody>

                    {{-- Iterate groups --}}
                    @php $rowCount = $orders->firstItem() - 1; @endphp
                    @foreach($groupedOrders as $groupId => $groupOrders)
                        {{-- Group Header --}}
                        @php $rowCount += 1; @endphp
                        <tr class="table-light">
                            <td colspan="5">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>
                                        {{ translate('batch') }} {{ $loop->iteration }}
                                        : {{ $groupOrders->count() }} {{ translate('orders') }}
                                        ({{ $groupId }})
                                    </strong>
                                    <a href="{{ route('admin.orders.generate-invoice', [$groupId]) }}"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="tio-download"></i> {{ translate('invoice') }}
                                    </a>
                                </div>
                            </td>
                        </tr>

                        {{-- Individual Orders in this group --}}
                        @foreach($groupOrders as $order)
                            @php $rowCount += 1; @endphp
                            <tr>
                                <td>{{ $rowCount }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.details', ['id'=>$order->id]) }}">
                                        {{ $order->id }}
                                    </a>
                                </td>
                                <td>
                                    {{ setCurrencySymbol(amount: usdToDefaultCurrency(amount: $order->order_amount)) }}
                                    @if($order->payment_status=='paid')
                                        <span class="badge badge-success">{{ translate('paid') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ translate('unpaid') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $st = $order->order_status;
                                        $map = [
                                            'pending' => 'info',
                                            'processing' => 'warning',
                                            'out_for_delivery' => 'warning',
                                            'confirmed' => 'success',
                                            'delivered' => 'success',
                                            'failed' => 'danger',
                                            'canceled' => 'danger',
                                            'returned' => 'secondary',
                                        ];
                                    @endphp
                                    <span class="badge badge-{{ $map[$st] ?? 'primary' }}">
                                    {{ translate(str_replace('_',' ',$st)) }}
                                </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.orders.details', ['id'=>$order->id]) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="tio-invisible"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach

                    @if($orders->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">{{ translate('no_order_found') }}</td>
                        </tr>
                    @endif

                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-end">
                    {!! $orders->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ dynamicAsset(path:'public/assets/back-end/js/owl.min.js') }}"></script>
    <script>
        'use strict';
        $('.address-slider').owlCarousel({
            margin: 16,
            loop: false,
            autoWidth: true,
        });
    </script>
@endpush
