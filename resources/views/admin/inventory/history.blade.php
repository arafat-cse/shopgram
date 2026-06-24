@extends('layouts.admin')
@section('title', 'Stock History')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Stock History</h4>
        <div class="text-muted small">{{ $product->name }}</div>
    </div>
    <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Variant</th>
                    <th>Quantity Change</th>
                    <th>Note</th>
                    <th>Updated By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $history)
                    <tr>
                        <td class="small text-muted">{{ $history->created_at->format('d M Y h:i A') }}</td>
                        <td><span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $history->type)) }}</span></td>
                        <td class="small">{{ $history->variant?->sku ?? '-' }}</td>
                        <td class="small fw-semibold {{ $history->quantity < 0 ? 'text-danger' : 'text-success' }}">
                            {{ $history->quantity > 0 ? '+' : '' }}{{ $history->quantity }}
                        </td>
                        <td class="small">{{ $history->note ?: '-' }}</td>
                        <td class="small">{{ $history->creator?->name ?? 'System' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No stock history found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $histories->links() }}</div>
</div>
@endsection
