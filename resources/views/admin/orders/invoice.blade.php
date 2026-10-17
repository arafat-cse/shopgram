<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #1e293b; background: #f1f5f9; }
        @media print { .no-print { display: none; } body { background: #fff; } .invoice-box { box-shadow: none !important; } }
        .invoice-box { max-width: 820px; margin: 24px auto; background: #fff; padding: 36px 40px; border: 1px solid #e2e8f0; }
        table.layout { width: 100%; border-collapse: collapse; }
        table.layout td { vertical-align: top; }
        .brand-logo { height: 48px; width: auto; }
        .brand-badge { display: inline-block; width: 44px; height: 44px; line-height: 44px; text-align: center; background: #6d28d9; color: #fff; border-radius: 10px; font-weight: bold; font-size: 18px; }
        .brand-name { font-size: 22px; font-weight: 800; color: #1e293b; margin-top: 6px; }
        .brand-meta { font-size: 11.5px; color: #64748b; line-height: 1.6; margin-top: 4px; }
        .invoice-title { font-size: 26px; font-weight: 800; color: #6d28d9; text-align: right; letter-spacing: 1px; }
        .invoice-meta { text-align: right; font-size: 12px; color: #475569; line-height: 1.7; margin-top: 6px; }
        .status-pill { display: inline-block; padding: 3px 12px; border-radius: 999px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-unpaid { background: #fef3c7; color: #92400e; }
        .divider { border-top: 2px solid #1e293b; margin: 22px 0 18px; }
        .addr-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; width: 48%; }
        .addr-label { font-size: 10px; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; font-weight: 700; margin-bottom: 4px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 24px; }
        table.items th { background: #1e293b; color: #fff; text-align: left; padding: 9px 10px; font-size: 11.5px; text-transform: uppercase; letter-spacing: .03em; }
        table.items td { padding: 9px 10px; border-bottom: 1px solid #e2e8f0; font-size: 12.5px; }
        table.items th.num, table.items td.num { text-align: right; }
        table.totals { width: 280px; margin-left: auto; margin-top: 16px; border-collapse: collapse; }
        table.totals td { padding: 5px 4px; font-size: 12.5px; }
        table.totals td.num { text-align: right; }
        table.totals tr.grand td { border-top: 2px solid #1e293b; font-size: 15px; font-weight: 800; padding-top: 8px; }
        .footer-note { text-align: center; color: #94a3b8; font-size: 11.5px; margin-top: 36px; border-top: 1px solid #e2e8f0; padding-top: 14px; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center;padding:14px;">
        <button onclick="window.print()" style="background:#6d28d9;color:#fff;border:0;border-radius:6px;padding:8px 18px;font-weight:600;cursor:pointer;">Print</button>
        <a href="{{ route('admin.orders.show', $order) }}" style="background:#64748b;color:#fff;border:0;border-radius:6px;padding:8px 18px;font-weight:600;text-decoration:none;margin-left:8px;">Back</a>
    </div>

    <div class="invoice-box">
        <table class="layout">
            <tr>
                <td style="width:55%;">
                    @if(!empty($sender['logo']))
                        <img src="{{ $sender['logo'] }}" class="brand-logo">
                    @else
                        <span class="brand-badge">{{ strtoupper(substr($sender['name'] ?? 'T', 0, 1)) }}</span>
                    @endif
                    <div class="brand-name">{{ $sender['name'] ?? 'Tech' }}</div>
                    <div class="brand-meta">
                        @if(!empty($sender['address'])){{ $sender['address'] }}<br>@endif
                        @if(!empty($sender['phone'])){{ $sender['phone'] }}@endif
                        @if(!empty($sender['phone']) && !empty($sender['email'])) &nbsp;|&nbsp; @endif
                        @if(!empty($sender['email'])){{ $sender['email'] }}@endif
                    </div>
                </td>
                <td style="width:45%;">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-meta">
                        <strong>#{{ $order->order_number }}</strong><br>
                        {{ $order->created_at->format('d M Y, h:i A') }}<br>
                        <span class="status-pill {{ $order->payment_status === 'paid' ? 'status-paid' : 'status-unpaid' }}">
                            {{ $order->payment_status === 'paid' ? 'Paid' : 'Unpaid' }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <table class="layout">
            <tr>
                <td style="width:50%;">
                    <div class="addr-box">
                        <div class="addr-label">Billed To</div>
                        @php $addr = $order->billing_address ?? [] @endphp
                        <strong>{{ $addr['name'] ?? '' }}</strong><br>
                        {{ $addr['phone'] ?? '' }}<br>
                        {{ $addr['address_line'] ?? '' }}<br>
                        {{ ($addr['city'] ?? '') . ($addr['district'] ?? '' ? ', ' . $addr['district'] : '') }}
                    </div>
                </td>
                <td style="width:50%;text-align:right;">
                    <div class="addr-box" style="margin-left:auto;text-align:left;">
                        <div class="addr-label">Payment Method</div>
                        <strong>{{ strtoupper($order->payment_method) }}</strong>
                        @if($order->payment->transaction_id ?? null)
                            <br><span style="color:#64748b;font-size:11.5px;">TrxID: {{ $order->payment->transaction_id }}</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr><th>Product</th><th class="num">Qty</th><th class="num">Unit Price</th><th class="num">Total</th></tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="num">{{ $item->quantity }}</td>
                    <td class="num">৳{{ number_format($item->unit_price, 0) }}</td>
                    <td class="num">৳{{ number_format($item->total_price, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr><td>Subtotal</td><td class="num">৳{{ number_format($order->subtotal, 0) }}</td></tr>
            @if($order->discount_amount > 0)
            <tr><td>Discount</td><td class="num">-৳{{ number_format($order->discount_amount, 0) }}</td></tr>
            @endif
            <tr><td>Shipping</td><td class="num">৳{{ number_format($order->shipping_charge, 0) }}</td></tr>
            <tr class="grand"><td>Total</td><td class="num">৳{{ number_format($order->total, 0) }}</td></tr>
        </table>

        <div class="footer-note">
            Thank you for shopping with {{ $sender['name'] ?? 'Tech' }}!<br>
            This is a computer-generated invoice.
        </div>
    </div>
</body>
</html>
