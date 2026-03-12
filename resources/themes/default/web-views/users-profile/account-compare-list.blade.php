@extends('layouts.front-end.app')

@section('title', translate('my_Compare_List'))

@push('css_or_js')
<style>
    /* ===== Compare List Page Styles ===== */
    .compare-page-wrapper {
        background: #f8f9fc;
        min-height: 60vh;
        padding: 40px 0 60px;
    }

    /* Page Header */
    .compare-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 32px;
        padding-bottom: 18px;
        border-bottom: 2px solid #e9ecf3;
    }

    .compare-page-header h3 {
        font-size: 1.65rem;
        font-weight: 700;
        color: #1a1f36;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .compare-page-header h3::before {
        content: '';
        display: inline-block;
        width: 6px;
        height: 28px;
        background: linear-gradient(180deg, #0d6efd, #5b9cf6);
        border-radius: 4px;
    }

    .compare-count-badge {
        background: #e8f0fe;
        color: #0d6efd;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }

    /* Product Card */
    .compare-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .compare-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    /* Image Area */
    .compare-card__image-wrapper {
        position: relative;
        background: #f1f4f9;
        padding: 20px;
        text-align: center;
        overflow: hidden;
    }

    .compare-card__image-wrapper::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 40px;
        background: linear-gradient(to top, #ffffff, transparent);
    }

    .compare-card__img {
        width: 140px;
        height: 140px;
        object-fit: contain;
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .compare-card:hover .compare-card__img {
        transform: scale(1.06);
    }

    /* Card Body */
    .compare-card__body {
        padding: 18px 18px 14px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .compare-card__name {
        font-size: 0.98rem;
        font-weight: 600;
        color: #1a1f36;
        margin-bottom: 8px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .compare-card__price {
        font-size: 1.18rem;
        font-weight: 700;
        color: #0d6efd;
        margin-bottom: 12px;
    }

    /* Stars */
    .compare-card__rating {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 16px;
    }

    .stars-group {
        display: flex;
        gap: 2px;
    }

    .stars-group i {
        font-size: 0.85rem;
    }

    .star-filled { color: #f5a623; }
    .star-empty  { color: #d0d5e8; }

    .rating-text {
        font-size: 0.78rem;
        color: #8892a4;
        font-weight: 500;
    }

    /* Remove Button */
    .compare-card__footer {
        padding: 0 18px 18px;
    }

    .btn-remove {
        width: 100%;
        background: transparent;
        border: 1.5px solid #ff4d6d;
        color: #ff4d6d;
        border-radius: 10px;
        padding: 9px 0;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .btn-remove:hover {
        background: #ff4d6d;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(255, 77, 109, 0.35);
    }

    .btn-remove i {
        font-size: 1rem;
    }

    /* Empty State */
    .compare-empty {
        text-align: center;
        padding: 80px 20px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }

    .compare-empty__icon {
        width: 90px;
        height: 90px;
        background: #e8f0fe;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
    }

    .compare-empty__icon i {
        font-size: 2.5rem;
        color: #0d6efd;
    }

    .compare-empty h5 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a1f36;
        margin-bottom: 10px;
    }

    .compare-empty p {
        font-size: 0.95rem;
        color: #8892a4;
        margin-bottom: 28px;
        max-width: 360px;
        margin-left: auto;
        margin-right: auto;
    }

    .btn-shop-now {
        background: linear-gradient(135deg, #0d6efd, #5b9cf6);
        color: #fff;
        padding: 12px 32px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
        box-shadow: 0 4px 16px rgba(13, 110, 253, 0.35);
    }

    .btn-shop-now:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(13, 110, 253, 0.45);
        color: #fff;
        text-decoration: none;
    }

    /* Divider row spacing */
    .compare-grid > [class*="col-"] {
        margin-bottom: 24px;
    }
</style>
@endpush

@section('content')
<div class="compare-page-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">

                <div class="compare-page-header">
                    <h3>{{ translate('my_Compare_List') }}</h3>
                    @if(count($compareLists) > 0)
                        <span class="compare-count-badge">{{ count($compareLists) }} {{ translate('items') }}</span>
                    @endif
                </div>

                @if(count($compareLists) > 0)
                    <div class="row compare-grid">
                        @foreach($compareLists as $compareList)
                            @if($compareList->product)
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12" id="row_id{{ $compareList->product->id }}">
                                    <div class="compare-card">

                                        {{-- Product Image --}}
                                        <div class="compare-card__image-wrapper">
                                            <img class="compare-card__img"
                                                 src="{{ getStorageImages(path: $compareList->product->thumbnail_full_url, type: 'product') }}"
                                                 alt="{{ $compareList->product->name }}">
                                        </div>

                                        {{-- Card Body --}}
                                        <div class="compare-card__body">
                                            <div class="compare-card__name">
                                                <a href="{{route('product', $compareList->product?->slug)}}" target="_blank"> {{ $compareList->product->name }} </a>
                                            </div>
                                            <div class="compare-card__price">
                                                {{ webCurrencyConverter(amount: $compareList->product->unit_price) }}
                                            </div>

                                            {{-- Rating --}}
                                            @php
                                                $reviewCount = $compareList->product->reviews ? $compareList->product->reviews->count() : 0;
                                                $avgRating   = $reviewCount > 0 ? round($compareList->product->reviews->avg('rating')) : 0;
                                            @endphp
                                            <div class="compare-card__rating">
                                                <div class="stars-group">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $avgRating)
                                                            <i class="tio-star star-filled"></i>
                                                        @else
                                                            <i class="tio-star-outlined star-empty"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="rating-text">({{ $reviewCount }} {{ translate('reviews') }})</span>
                                            </div>
                                        </div>

                                        {{-- Remove Button --}}
                                        <div class="compare-card__footer">
                                            <form action="{{ route('product-compare.delete') }}" method="POST">
                                                @csrf
                                                @if(auth('customer')->check())
                                                    <input type="hidden" name="id" value="{{ $compareList->id }}">
                                                @else
                                                    <input type="hidden" name="product_id" value="{{ $compareList->product->id }}">
                                                @endif
                                                <button type="submit" class="btn-remove">
                                                    <i class="tio-delete-outlined"></i>
                                                    {{ translate('remove') }}
                                                </button>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                @else
                    {{-- Empty State --}}
                    <div class="compare-empty">
                        <div class="compare-empty__icon">
                            <i class="tio-compare-arrows"></i>
                        </div>
                        <h5>{{ translate('no_products_in_compare_list') }}</h5>
                        <p>{{ translate('add_products_to_compare_and_find_the_best_deal') }}</p>
                        <a href="{{ route('home') }}" class="btn-shop-now">
                            <i class="tio-shopping-cart-outlined"></i>
                            {{ translate('continue_shopping') }}
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
