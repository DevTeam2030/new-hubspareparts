@extends('layouts.front-end.app')

@section('title', translate('my_Order_List'))

@section('content')
    <div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
        <div class="row">
            @include('web-views.partials._profile-aside')

            <section class="col-lg-9 __customer-profile customer-profile-wishlist px-0">

                {{-- desktop --}}
                <div class="card __card d-none d-lg-flex web-direction customer-profile-orders h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="font-bold fs-16 mb-0">{{ translate('my_Order') }}</h5>
                        </div>

                        @if($orders->count())
                            <div class="table-responsive">
                                <table class="table __table __table-2 text-center">
                                    <thead class="thead-light">
                                    <tr>
                                        <td class="tdBorder text-start">{{ translate('order_list') }}</td>
                                        <td class="tdBorder">{{ translate('status') }}</td>
                                        <td class="tdBorder">{{ translate('total') }}</td>
                                        <td class="tdBorder">{{ translate('action') }}</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $lastGroup  = null;
                                        $groupIndex = 0;
                                        $itemIndex  = 0;
                                    @endphp

                                    @foreach($orders as $order)
                                        @if($lastGroup !== $order->order_group_id)
                                            @php
                                                $groupIndex++;
                                                $itemIndex = 0;
                                            @endphp

                                            @if($lastGroup !== null)
                                                <tr>
                                                    <td colspan="4">
                                                        <div style="
                                                            height:4px;
                                                            margin:24px 0;
                                                            background:linear-gradient(90deg,rgba(0,123,255,0) 0%,rgba(0,123,255,0.8) 50%,rgba(0,123,255,0) 100%);
                                                            border-radius:2px
                                                        "></div>
                                                    </td>
                                                </tr>
                                            @endif

                                            <tr>
                                                <td colspan="4" class="text-start d-flex justify-content-between align-items-center">
                                                    <span class="fs-16 font-semibold">{{ translate('order') }} {{ $groupIndex }}</span>
                                                    <a href="{{ route('generate-invoice', [$order->order_group_id]) }}"
                                                       class="btn btn-sm btn-outline-success">
                                                        <i class="tio-download-to"></i> {{ translate('Download Invoice') }}
                                                    </a>
                                                </td>
                                            </tr>

                                            @php $lastGroup = $order->order_group_id; @endphp
                                        @endif

                                        @php $itemIndex++; @endphp
                                        <tr>
                                            <td class="bodytr text-start">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="order-seq font-bold">#{{ $itemIndex }}</span>
                                                    <div class="media-order d-flex align-items-center">
                                                        <a href="{{ route('account-order-details', $order->id) }}" class="d-block">
                                                            <img alt="{{ translate('shop') }}"
                                                                 src="{{ $order->seller_is==='seller'
                                                                     ? getStorageImages(path:$order->seller->shop->image_full_url, type:'shop')
                                                                     : getStorageImages(path:$web_config['fav_icon'], type:'shop') }}">
                                                        </a>
                                                        <div class="cont text-start ms-2">
                                                            <h6 class="fs-14 font-semibold mb-1">
                                                                <a href="{{ route('account-order-details', $order->id) }}">
                                                                    {{ translate('order') }} #{{ $order->id }}
                                                                </a>
                                                            </h6>
                                                            <div class="fs-12 text-secondary-50">
                                                                {{ $order->order_details_sum_qty }} {{ translate('items') }} •
                                                                {{ date('d M, Y h:i A', strtotime($order->created_at)) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                @php $status = $order->order_status; @endphp
                                                @if(in_array($status, ['failed','canceled']))
                                                    <span class="status-badge badge-soft-danger">{{ translate($status=='failed'?'failed_to_deliver':$status) }}</span>
                                                @elseif(in_array($status, ['confirmed','processing','delivered']))
                                                    <span class="status-badge badge-soft-success">{{ translate($status=='processing'?'packaging':$status) }}</span>
                                                @else
                                                    <span class="status-badge badge-soft-primary">{{ translate($status) }}</span>
                                                @endif
                                            </td>
                                            <td class="bodytr">
                                                <div class="font-bold fs-13 text-dark">
                                                    @php
                                                        $sum = \App\Utils\OrderManager::getOrderTotalPriceSummary(order:$order);
                                                    @endphp
                                                    {{ webCurrencyConverter(amount:$sum['totalAmount']) }}
                                                </div>
                                            </td>
                                            <td class="bodytr">
                                                <a href="{{ route('account-order-details', $order->id) }}"
                                                   class="btn-outline--info __action-btn btn-shadow rounded-full">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="d-flex justify-content-center align-items-center h-100">
                                <div class="text-center">
                                    <img src="{{ theme_asset(path:'public/assets/front-end/img/empty-icons/empty-orders.svg') }}" width="100">
                                    <h5 class="text-muted fs-14">{{ translate('You_have_not_any_order_yet') }}!</h5>
                                </div>
                            </div>
                        @endif

                        <div class="card-footer border-0">
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>

                {{-- mobile --}}
                <div class="bg-white d-lg-none web-direction">
                    <div class="card-body py-0 customer-profile-orders d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="font-bold fs-16 mb-0">{{ translate('my_Order') }}</h5>
                            <button class="profile-aside-btn btn btn--primary rounded py-1 px-2">
                                <i class="tio-menu-hamburger"></i>
                            </button>
                        </div>

                        @php
                            $lastGroup  = null;
                            $groupIndex = 0;
                            $itemIndex  = 0;
                        @endphp

                        @foreach($orders as $order)
                            @if($lastGroup !== $order->order_group_id)
                                @php
                                    $groupIndex++;
                                    $itemIndex = 0;
                                @endphp

                                @if($lastGroup !== null)
                                    <div style="
                                    height:4px;
                                    margin:24px 0;
                                    background:linear-gradient(90deg,rgba(0,123,255,0) 0%,rgba(0,123,255,0.8) 50%,rgba(0,123,255,0) 100%);
                                    border-radius:2px
                                "></div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-16 font-semibold">{{ translate('Batch') }} {{ $groupIndex }}</span>
                                    <a href="{{ route('generate-invoice', [$order->order_group_id]) }}"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="tio-download-to"></i>
                                    </a>
                                </div>

                                @php $lastGroup = $order->order_group_id; @endphp
                            @endif

                            @php $itemIndex++; @endphp
                            <div class="d-flex justify-content-between align-items-center border-lighter rounded p-2">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="order-seq font-bold">#{{ $itemIndex }}</span>
                                    <a href="{{ route('account-order-details', $order->id) }}">
                                        <img width="40" class="rounded"
                                             src="{{ $order->seller_is==='seller'
                                             ? getStorageImages(path:$order->seller->shop->image_full_url, type:'shop')
                                             : getStorageImages(path:$web_config['fav_icon'], type:'shop') }}"
                                             alt="">
                                    </a>
                                    <div>
                                        <h6 class="fs-14 font-semibold mb-1">
                                            <a href="{{ route('account-order-details', $order->id) }}">
                                                {{ translate('order') }} #{{ $order->id }}
                                            </a>
                                        </h6>
                                        <div class="fs-12 text-secondary-50">
                                            {{ $order->order_details_sum_qty }} {{ translate('items') }} •
                                            {{ date('d M, Y h:i A', strtotime($order->created_at)) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="font-bold fs-13">{{ webCurrencyConverter(amount:$order->order_amount) }}</div>
                                    <a href="{{ route('account-order-details', $order->id) }}"
                                       class="btn-outline--info __action-btn btn-shadow rounded-full">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach

                        @if(!$orders->count())
                            <div class="d-flex justify-content-center align-items-center h-100 pt-5">
                                <div class="text-center">
                                    <img src="{{ theme_asset(path:'public/assets/front-end/img/empty-icons/empty-orders.svg') }}" width="100">
                                    <h5 class="text-muted fs-14">{{ translate('You_have_not_any_order_yet') }}!</h5>
                                </div>
                            </div>
                        @endif

                        <div class="card-footer border-0">
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>

            </section>
        </div>
    </div>
@endsection
