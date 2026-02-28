<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ 'wishlist Collection - '.$data['collection']->name }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1e293b;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        .container { padding: 15px; }
        .table-layout { width: 100%; border-collapse: collapse; margin-bottom: 15px; }

        /* Header */
        .logo { max-height: 40px; width: auto; }
        .h3 { font-size: 18px; font-weight: bold; color: #0f2b4c; }

        /* Meta Panel */
        .meta-table { width: 100%; border-collapse: collapse; background: #f9fbfd; border: 1px solid #e9eef3; margin-bottom: 15px; }
        .meta-table td { padding: 10px; border: 1px solid #e9eef3; vertical-align: top; }
        .meta-label { font-size: 9px; color: #5d6e85; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 3px; }
        .meta-value { font-size: 12px; font-weight: bold; color: #1d2b3f; }

        /* Badges */
        .badge { background: #eef2ff; color: #1e3a8a; padding: 3px 8px; border-radius: 10px; font-size: 10px; border: 1px solid #cfddf5; margin-right: 5px; }

        /* Product Table */
        .product-table { width: 100%; border-collapse: collapse; }
        .product-table th { background: #f2f6fc; color: #1e3d62; text-align: left; padding: 10px; border-bottom: 2px solid #cbd5e1; }
        .product-table td { padding: 10px; border-bottom: 1px solid #e9edf3; }

        /* Footer */
        .footer { text-align: center; background: #f8fafc; padding: 20px; border-top: 1px solid #e2e8f0; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
<div class="container">
    <table class="table-layout">
        <tr>
            <td class="h3">{{ 'Wishlist Collection - '.$data['collection']->name }}  </td>
            <td align="right">
                <img class="logo" src="{{ getStorageImages(path:$data['company_web_logo'], type:'backend-logo') }}" alt="Logo">
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td width="33%">
                <span class="meta-label">Due date</span>
                <span class="meta-value">{{ $data['collection']->due_date }}</span>
            </td>
            <td width="33%">
                <span class="meta-label">Created by</span>
                <span class="meta-value">{{ $data['collection']->user?->name ?? 'Jonior user' }}</span>
            </td>
            <td width="33%">
                <span class="meta-label">Created at</span>
                <span class="meta-value">{{ $data['collection']->created_at->format('d/m/Y') }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="meta-label">Priority</span>
                <span class="meta-value">{{ $data['collection']->priority }}</span>
            </td>
            <td colspan="2">
                <span class="meta-label">Approvals</span>
                <div class="meta-value">
                    @if($data['collection']->eng_approve)
                        <span class="badge">Eng. Approve</span>
                    @endif
                    @if($data['collection']->eng_proc)
                        <span class="badge">Proc. Approve</span>
                    @endif
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span class="meta-label">Notes</span>
                <span class="meta-value">{{ $data['collection']->notes ?? '—' }}</span>
            </td>
        </tr>
    </table>
    @php
        $totalPrice = 0;
    @endphp

    <table class="product-table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Image</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data['wishlist'] as $item)
            @php

                    $product = App\Models\Product::find($item->product_id);
                    $totalPrice += $item->quantity * $product->unit_price;
            @endphp
            <tr>
                <td><strong>{{ $product['name'] }}</strong></td>
                <td>
                    <img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'product') }}" alt="{{ $product['name'] }}" style="max-width: 50px;">
                </td>
                <td>{{ $item->quantity }}</td>
                <td>{{ webCurrencyConverter(amount: $product->unit_price) }}</td>
                <td>
                    @if($product && $product->current_stock >= $item->quantity)
                        <span style="color: green;">In Stock</span>
                    @else
                        <span style="color: red;">Out of Stock</span>
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="3" style="text-align: right; font-weight: bold;">
                Total Cost: {{ webCurrencyConverter(amount: $totalPrice) }}
            </td>
        </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>
            {{ translate('phone') }}: {{ $data['company_phone'] }} |
            {{ translate('email') }}: {{ $data['company_email'] }}
        </p>
        <p>{{ url('/') }}</p>
        <p>{{ translate('all_copy_right_reserved_©_'.date('Y').'_'). $data['company_name'] }}</p>
    </div>
</div>
</body>
</html>
