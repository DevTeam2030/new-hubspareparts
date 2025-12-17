{{-- resources/views/web-views/order/invoice.blade.php --}}
    <!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ملخص طلبك من Hubspareparts</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>

        /* force-print-colors */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        /* Reset & Base */
        html, body { margin: 0; padding: 0; }
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Tajawal', sans-serif;
            background: #f8f9fa;
            padding: 20px;
            color: #333;
        }

        /* Container */
        .invoice-box {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(90deg, #00d1bc 0%, #00a38f 100%);
            color: #fff;
            padding: 1rem 2rem;
        }
        .header .logo {
            max-height: 60px;
            width: auto;
        }
        .header .header-text {
            flex: 1;
            text-align: center;
            margin: 0 1rem;
        }
        .header h1 {
            font-size: 2.2rem;
            margin-bottom: .3rem;
        }
        .header p {
            font-size: 1.7rem;
            margin: 0;
        }

        /* Order Info */
        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px,1fr));
            gap: 1rem;
            padding: 2rem;
            border-bottom: 2px solid #eee;
        }
        .info-item { text-align: center; }
        .info-label {
            font-size: .9rem;
            color: #666;
            margin-bottom: .3rem;
        }
        .info-value {
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Products */
        .products-list {
            padding: 2rem;
        }
        .order-number {
            font-weight: 700;
            color: #007F65;
            margin-bottom: .5rem;
            font-size: 1rem;
            text-align: right;
        }
        .product-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        .product-item:last-child {
            border-bottom: none;
        }
        .product-info {
            text-align: right;
        }
        .seller-name {
            font-size: .9rem;
            color: #666;
        }

        /* Quantity Badge */
        .qty-badge {
            display: inline-flex;
            align-items: center;
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: .9rem;
        }
        .qty-badge::before {
            content: "✕";
            margin-left: 6px;
            opacity: .7;
            font-size: .8em;
        }

        /* Price */
        .price {
            font-weight: 700;
            color: #2e7d32;
            font-size: 1.1rem;
        }

        /* Fees */
        .fees-section {
            padding: 0 2rem 2rem;
            text-align: right;
        }
        .fees-section .fee-item {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            font-size: .95rem;
            color: #333;
        }

        /* Grand Total */
        .total-section {
            padding: 2rem;
            background: #f8f9fa;
            border-top: 2px solid #eee;
            text-align: right;
        }
        .grand-total {
            font-size: 1.5rem;
            color: #c62828;
            font-weight: 700;
        }

        /* Footer Note */
        .footer-note {
            padding: 2rem;
            text-align: center;
            background: #fff9c4;
            color: #6d4c41;
            font-size: .9rem;
        }
    </style>
</head>
<body>
<div class="invoice-box">
    {{-- Header --}}
    <div class="header">
        <img src="{{  asset('new-logo.png') }}" alt="Logo" class="logo">
        <div class="header-text">
            <h1>ملخص طلبك من Hubspareparts</h1>
            <p>🎉 شكرًا لثقتك بنا</p>
        </div>
    </div>

    {{-- Order Info --}}
    <div class="order-info">
        <div class="info-item">
            <div class="info-label">تاريخ الطلب</div>
            <div class="info-value">{{ date(' d/m/Y', strtotime($order->created_at)) }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">رقم الطلب</div>
            <div class="info-value">#{{ $order->order_group_id }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">طريقة الدفع</div>
            <div class="info-value">
                {{ $order->payment_method == 'cash_on_delivery'
                    ? 'الدفع عند الاستلام'
                    : ucfirst($order->payment_method) }}
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">اسم العميل</div>
            <div class="info-value">{{ $order->customer->name }}</div>
        </div>
    </div>

    {{-- Products List --}}
    <div class="products-list">
        @foreach($orders as $o)
            @php
                $detail  = $o->orderDetails->first();
                $product = $detail->product;
            @endphp

            <div class="order-number">
                المنتج رقم {{ $loop->iteration }}
            </div>
            <div class="product-item">
                <div class="product-info">
                    <div style="font-weight:600;">{{ $product?->name }}</div>
                    <div class="seller-name">البائع:  {{ $o->seller?->shop?->name ?? 'غير محدد' }}</div>
                </div>
                <div>
                    <span class="qty-badge">الكمية: {{ $detail?->qty }}</span>
                </div>
                <div>
                    <span class="price">{{ webCurrencyConverter(amount: $o->order_amount) }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Fees --}}
    <div class="fees-section">
        <div class="fee-item">
            <span>مصاريف الشحن:</span>
            <span>{{ webCurrencyConverter(amount: $totalShippingCost) }}</span>
        </div>
        <div class="fee-item">
            <span>رسوم الخدمة:</span>
            <span>{{ webCurrencyConverter(amount: $totalServiceFee) }}</span>
        </div>
    </div>

    {{-- Grand Total --}}
    <div class="total-section">
        <div class="grand-total">
            الإجمالي النهائي: {{ webCurrencyConverter(amount: $grandTotal) }}
        </div>
    </div>

    {{-- Footer Note --}}
    <div class="footer-note">
        ⚠️ هذه ليست فاتورة ، يتعين على المحل بصفته التاجر اصدار فاتورة ومشاركتها مع العميل
    </div>
</div>


<script>
    // بعد انتهاء تحميل كل الموارد، شغّل نافذة الطباعة
    window.addEventListener('load', () => {
        window.print();
    });
    // لو حابب يقفل النافذة بعد الطباعة:
    // window.onafterprint = () => window.close();
</script>
</body>
</html>
