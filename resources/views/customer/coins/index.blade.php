@extends('layouts.customer')
@section('title', 'My Coins')
@section('customer_content')
<h4 class="fw-bold mb-4">My Coins</h4>

<div class="card border-0 shadow-sm mb-4" style="background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:14px;">
    <div class="card-body p-4 d-flex align-items-center justify-content-between text-white">
        <div>
            <div class="text-white-50 small mb-1">Your Coin Balance</div>
            <div class="fw-bold" style="font-size:2.2rem;line-height:1;">
                <i class="bi bi-coin"></i> {{ number_format($user->coins_balance) }}
            </div>
        </div>
        <div class="text-end small text-white-50" style="max-width:200px;">
            Purchase products marked with a coin badge to earn coins. Coins are credited once your order is delivered.
        </div>
    </div>
</div>

<h6 class="fw-bold mb-3">Redeem with Coins</h6>
@if($products->count())
<div class="row g-3 mb-4">
    @foreach($products as $product)
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <a href="{{ route('products.show', $product->slug) }}">
                <img src="{{ $product->thumbnail_url }}" class="card-img-top" style="height:160px;object-fit:cover" alt="{{ $product->name }}">
            </a>
            <div class="card-body p-3">
                <h6 class="mb-1">
                    <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none">
                        {{ Str::limit($product->name, 40) }}
                    </a>
                </h6>
                <div class="fw-bold mb-2" style="color:#d97706;">
                    <i class="bi bi-coin"></i> {{ number_format($product->coin_price) }} coins
                </div>
                <form action="{{ route('customer.coins.redeem', $product) }}" method="POST"
                      onsubmit="return confirm('Redeem {{ $product->coin_price }} coins for {{ $product->name }}?');">
                    @csrf
                    <button class="btn btn-sm w-100 {{ $user->coins_balance >= $product->coin_price ? 'btn-warning' : 'btn-outline-secondary' }}"
                            {{ $user->coins_balance >= $product->coin_price ? '' : 'disabled' }}>
                        @if($user->coins_balance >= $product->coin_price)
                            Redeem Now
                        @else
                            Need {{ number_format($product->coin_price - $user->coins_balance) }} more
                        @endif
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mb-4">{{ $products->links() }}</div>
@else
<div class="text-center py-4 text-muted mb-4">
    <i class="bi bi-coin" style="font-size:2rem"></i>
    <p class="mt-2 mb-0">No coin-redeemable products right now. Check back soon!</p>
</div>
@endif

<h6 class="fw-bold mb-3">Coin History</h6>
@if($transactions->count())
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $tx)
                <tr>
                    <td class="text-muted small">{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $tx->type_label }}</span></td>
                    <td class="small">{{ $tx->description }}</td>
                    <td class="text-end fw-semibold {{ $tx->amount >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $tx->amount >= 0 ? '+' : '' }}{{ number_format($tx->amount) }}
                    </td>
                    <td class="text-end">{{ number_format($tx->balance_after) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $transactions->links() }}</div>
@else
<div class="text-center py-4 text-muted">
    <p class="mb-0">No coin activity yet.</p>
</div>
@endif
@endsection
