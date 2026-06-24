@php
    $money = fn($amount) => ($currencySymbol ?? '৳') . number_format((float) $amount, 0);
    $moneyKeys = ['price', 'sales', 'cost', 'profit', 'loss'];
@endphp

<h2>Summary</h2>
<table>
    <tbody>
    @foreach($cards as $label => $value)
        @php
            $isMoney = is_numeric($value) && collect($moneyKeys)->contains(fn($key) => str_contains($label, $key));
        @endphp
        <tr>
            <th>{{ ucwords(str_replace('_', ' ', $label)) }}</th>
            <td>{{ $isMoney ? $money($value) : $value }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<h2>Top Selling Products</h2>
<table>
    <thead><tr><th>Product</th><th>SKU</th><th>Sold Quantity</th><th>Sales Amount</th></tr></thead>
    <tbody>
    @foreach($topSellingProducts as $product)
        <tr><td>{{ $product->name }}</td><td>{{ $product->sku }}</td><td>{{ $product->sold_quantity }}</td><td>{{ $money($product->sales_amount) }}</td></tr>
    @endforeach
    </tbody>
</table>

<h2>Recent Orders</h2>
<table>
    <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th></tr></thead>
    <tbody>
    @foreach($recentOrders as $order)
        <tr><td>{{ $order->order_number }}</td><td>{{ $order->user->name ?? '-' }}</td><td>{{ $money($order->total) }}</td><td>{{ $order->payment_status }}</td><td>{{ $order->status }}</td><td>{{ $order->created_at->format('d M Y') }}</td></tr>
    @endforeach
    </tbody>
</table>
