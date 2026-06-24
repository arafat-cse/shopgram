@extends('layouts.admin')
@section('title', 'Edit Shipping Zone')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Shipping Zone</h4>
    <a href="{{ route('admin.shipping-zones.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('admin.shipping-zones.update', $shippingZone) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Zone Name *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $shippingZone->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Charge ({{ $currencySymbol ?? 'Tk' }}) *</label>
                    <input type="number" name="charge" class="form-control @error('charge') is-invalid @enderror" value="{{ old('charge', $shippingZone->charge) }}" min="0" step="0.01" required>
                    @error('charge')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Free Above ({{ $currencySymbol ?? 'Tk' }})</label>
                    <input type="number" name="free_above" class="form-control @error('free_above') is-invalid @enderror" value="{{ old('free_above', $shippingZone->free_above) }}" min="0" step="0.01" placeholder="Leave blank to disable">
                    @error('free_above')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="active" {{ old('status', $shippingZone->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $shippingZone->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Zone</button>
        </form>
    </div>
</div>
@endsection
