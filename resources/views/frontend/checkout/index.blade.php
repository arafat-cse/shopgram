@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">Checkout</h2>
    <x-alert />

    <form action="{{ route('checkout.place-order') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Billing Info --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Billing Information</h5>
                        @if($addresses->count())
                        <div class="mb-3">
                            <label class="form-label">Select Saved Address</label>
                            <select class="form-select" id="savedAddress">
                                <option value="">-- New Address --</option>
                                @foreach($addresses as $addr)
                                <option value="{{ $addr->id }}"
                                    data-name="{{ $addr->name }}" data-phone="{{ $addr->phone }}"
                                    data-address="{{ $addr->address_line }}" data-city="{{ $addr->city }}"
                                    data-district="{{ $addr->district }}" data-zip="{{ $addr->zip ?? '' }}"
                                    {{ $addr->is_default ? 'selected' : '' }}>
                                    {{ $addr->label }} - {{ $addr->address_line }}, {{ $addr->city }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="billing_name" class="form-control @error('billing_name') is-invalid @enderror" value="{{ old('billing_name') }}" required>
                                @error('billing_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone *</label>
                                <input type="text" name="billing_phone" class="form-control @error('billing_phone') is-invalid @enderror" value="{{ old('billing_phone', auth()->user()->phone) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address *</label>
                                <input type="text" name="billing_address" class="form-control" value="{{ old('billing_address') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City *</label>
                                <input type="text" name="billing_city" class="form-control" value="{{ old('billing_city') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">District *</label>
                                <input type="text" name="billing_district" class="form-control" value="{{ old('billing_district') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ZIP</label>
                                <input type="text" name="billing_zip" class="form-control" value="{{ old('billing_zip') }}">
                            </div>
                        </div>
                        <div class="form-check mt-3">
                            <input type="checkbox" class="form-check-input" name="same_as_billing" id="sameAsBilling" value="1" checked>
                            <label class="form-check-label" for="sameAsBilling">Shipping same as billing</label>
                        </div>
                    </div>
                </div>

                {{-- Shipping Zone --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Delivery Area</h5>
                        @foreach($zones as $zone)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="shipping_zone_id" value="{{ $zone->id }}" id="zone{{ $zone->id }}" {{ $loop->first ? 'checked' : '' }}>
                            <label class="form-check-label" for="zone{{ $zone->id }}">
                                {{ $zone->name }} — ৳{{ number_format($zone->charge, 0) }}
                                @if($zone->free_above) <small class="text-success">(Free over ৳{{ number_format($zone->free_above, 0) }})</small>@endif
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Payment --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Payment Method</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="cod" id="payCod" checked>
                            <label class="form-check-label" for="payCod"><i class="bi bi-cash-coin me-1"></i>Cash on Delivery</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="bank" id="payBank">
                            <label class="form-check-label" for="payBank"><i class="bi bi-bank me-1"></i>Bank Transfer</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="mobile" id="payMobile">
                            <label class="form-check-label" for="payMobile"><i class="bi bi-phone me-1"></i>Mobile Banking</label>
                        </div>
                        <div id="paymentScreenshotSection" style="display:none">
                            <label class="form-label mt-2">Payment Screenshot</label>
                            <input type="file" name="payment_screenshot" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                {{-- Order Note --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <label class="form-label">Order Note <span class="text-muted">(optional)</span></label>
                        <textarea name="order_note" class="form-control" rows="2" placeholder="Any special instructions..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-3 sticky-top" style="top:80px">
                    <h5 class="fw-bold mb-3">Order Summary</h5>
                    @foreach($items as $item)
                    <div class="d-flex justify-content-between small mb-2">
                        <span>{{ Str::limit($item->product->name, 25) }} x{{ $item->quantity }}</span>
                        <span>৳{{ number_format($item->price * $item->quantity, 0) }}</span>
                    </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between small"><span>Subtotal</span><span>৳{{ number_format($subtotal, 0) }}</span></div>
                    @if($discount > 0)
                    <div class="d-flex justify-content-between small text-success"><span>Discount</span><span>-৳{{ number_format($discount, 0) }}</span></div>
                    @endif
                    <div class="d-flex justify-content-between small"><span>Shipping</span><span>Calculated at checkout</span></div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold"><span>Total</span><span class="text-danger fs-5">৳{{ number_format($subtotal - $discount, 0) }}+</span></div>
                    <button type="submit" class="btn btn-primary w-100 mt-3 btn-lg">Place Order</button>
                </div>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>
document.querySelectorAll('input[name="payment_method"]').forEach(r => {
    r.addEventListener('change', function() {
        document.getElementById('paymentScreenshotSection').style.display = (this.value === 'bank' || this.value === 'mobile') ? 'block' : 'none';
    });
});

const savedAddr = document.getElementById('savedAddress');
if (savedAddr) {
    savedAddr.addEventListener('change', function() {
        const o = this.options[this.selectedIndex];
        if (o.value) {
            document.querySelector('[name=billing_name]').value = o.dataset.name || '';
            document.querySelector('[name=billing_phone]').value = o.dataset.phone || '';
            document.querySelector('[name=billing_address]').value = o.dataset.address || '';
            document.querySelector('[name=billing_city]').value = o.dataset.city || '';
            document.querySelector('[name=billing_district]').value = o.dataset.district || '';
            document.querySelector('[name=billing_zip]').value = o.dataset.zip || '';
        }
    });
    if (savedAddr.value) savedAddr.dispatchEvent(new Event('change'));
}
</script>
@endpush
@endsection
