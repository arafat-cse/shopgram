<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shipping Label #{{ $order->order_number }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f1f5f9; }
        @media print {
            .no-print { display: none; }
            body { background: #fff; }
            .label-box { box-shadow: none !important; margin: 0 !important; }
            @page { size: 4in 6in; margin: 6mm; }
        }
        .label-box {
            width: 4in;
            min-height: 6in;
            margin: 24px auto;
            background: #fff;
            border: 2px solid #0f172a;
            border-radius: 6px;
            padding: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,.08);
        }
        .label-section { border: 1.5px solid #0f172a; border-radius: 4px; padding: 10px 12px; margin-bottom: 10px; }
        .label-caption { font-size: 10px; letter-spacing: .06em; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px; }
        .label-name { font-size: 18px; font-weight: 800; }
        .label-line { font-size: 14px; line-height: 1.5; }
        .order-no-box { background: #0f172a; color: #fff; text-align: center; border-radius: 4px; padding: 10px; font-size: 20px; font-weight: 800; letter-spacing: .04em; margin-bottom: 10px; }
        .cod-box { background: #fef3c7; border: 1.5px dashed #b45309; color: #78350f; text-align: center; border-radius: 4px; padding: 8px; font-size: 16px; font-weight: 800; margin-bottom: 10px; }
        .paid-box { background: #dcfce7; border: 1.5px solid #166534; color: #14532d; text-align: center; border-radius: 4px; padding: 8px; font-size: 16px; font-weight: 800; margin-bottom: 10px; }
    </style>
</head>
<body class="p-3">
    <div class="no-print mb-3 text-center">
        <button class="btn btn-sm btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Print Label</button>
        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-secondary ms-2">Back</a>
    </div>

    <div class="label-box">
        <div class="order-no-box">{{ $order->order_number }}</div>

        @if($order->payment_status === 'paid')
            <div class="paid-box">✓ PAID — No Collection Needed</div>
        @else
            <div class="cod-box">COD: ৳{{ number_format($order->total, 0) }} — Collect on Delivery</div>
        @endif

        <div class="label-section">
            <div class="label-caption">From</div>
            <div class="label-name">{{ $sender['name'] }}</div>
            @if($sender['phone'])<div class="label-line">{{ $sender['phone'] }}</div>@endif
            @if($sender['address'])<div class="label-line">{{ $sender['address'] }}</div>@endif
        </div>

        <div class="label-section">
            <div class="label-caption">To</div>
            @php $addr = $order->shipping_address ?? [] @endphp
            <div class="label-name">{{ $addr['name'] ?? '' }}</div>
            <div class="label-line fw-bold">{{ $addr['phone'] ?? '' }}</div>
            <div class="label-line">{{ $addr['address_line'] ?? '' }}</div>
            <div class="label-line">{{ ($addr['city'] ?? '') . ($addr['district'] ?? '' ? ', ' . $addr['district'] : '') }}</div>
        </div>

        <div class="label-section d-flex justify-content-between">
            <div>
                <div class="label-caption">Courier</div>
                <div class="label-line">{{ $order->courier->name ?? '-' }}</div>
            </div>
            <div class="text-end">
                <div class="label-caption">Items</div>
                <div class="label-line">{{ $order->items->sum('quantity') }} pcs</div>
            </div>
        </div>

        @if($order->courier_tracking_number)
            <div class="label-section text-center">
                <div class="label-caption">Tracking No.</div>
                <div class="label-line fw-bold">{{ $order->courier_tracking_number }}</div>
            </div>
        @endif
    </div>
</body>
</html>
