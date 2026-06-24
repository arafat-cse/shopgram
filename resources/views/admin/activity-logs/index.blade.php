@extends('layouts.admin')
@section('title', 'Activity Log')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Activity Log</h4>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Search description..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="action" class="form-select form-select-sm">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $action)) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="model_type" class="form-select form-select-sm">
                    <option value="">All Models</option>
                    @foreach($modelTypes as $type)
                    <option value="{{ $type }}" {{ request('model_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:13px;">
            <thead class="table-light">
                <tr>
                    <th style="width:140px">Time</th>
                    <th style="width:130px">Admin</th>
                    <th style="width:100px">Action</th>
                    <th style="width:90px">Model</th>
                    <th>Description</th>
                    <th style="width:100px">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-muted" style="white-space:nowrap">
                        {{ $log->created_at->format('d M y, H:i') }}
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $log->user->name ?? 'Deleted' }}</div>
                        <div class="text-muted" style="font-size:11px">{{ $log->user->email ?? '' }}</div>
                    </td>
                    <td>
                        <span class="badge bg-{{ $log->action_color }} bg-opacity-10 text-{{ $log->action_color }}" style="border:1px solid currentColor;padding:3px 8px">
                            <i class="bi {{ $log->action_icon }} me-1"></i>{{ ucfirst(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </td>
                    <td class="text-muted">
                        @if($log->model_type)
                            {{ $log->model_type }}
                            @if($log->model_id)
                                <span class="text-muted" style="font-size:11px">#{{ $log->model_id }}</span>
                            @endif
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $log->description }}</td>
                    <td class="text-muted" style="font-size:11px">{{ $log->ip_address ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">No activity logs yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $logs->withQueryString()->links() }}</div>
</div>
@endsection
