<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { font-size: 13px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="p-4">
    <div class="no-print mb-3">
        <button class="btn btn-sm btn-primary" onclick="window.print()">Print</button>
        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-secondary ms-2">Back</a>
    </div>
    <div class="row mb-4">
        <div class="col-6"><h4 class="fw-bold">ShopGram</h4><small class="text-muted">Invoice</small></div>
        <div class="col-6 text-end">
            <strong>Invoice #{{ $order->order_number }}</strong><br>
            <small>{{ $order->created_at->format('d M Y') }}</small>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-6">
            <strong>Billed To:</strong><br>
            @php $addr = $order->billing_address @endphp
            {{ $addr['name'] ?? '' }}<br>{{ $addr['phone'] ?? '' }}<br>{{ $addr['address_line'] ?? '' }}<br>{{ ($addr['city'] ?? '') . ', ' . ($addr['district'] ?? '') }}
        </div>
    </div>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>৳{{ number_format($item->unit_price, 0) }}</td>
                <td>৳{{ number_format($item->total_price, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="3" class="text-end">Subtotal</td><td>৳{{ number_format($order->subtotal, 0) }}</td></tr>
            @if($order->discount_amount > 0)<tr><td colspan="3" class="text-end">Discount</td><td>-৳{{ number_format($order->discount_amount, 0) }}</td></tr>@endif
            <tr><td colspan="3" class="text-end">Shipping</td><td>৳{{ number_format($order->shipping_charge, 0) }}</td></tr>
            <tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td>৳{{ number_format($order->total, 0) }}</td></tr>
        </tfoot>
    </table>
    <div class="mt-4 text-muted small text-center">Thank you for shopping with ShopGram!</div>
</body>
</html>
