@extends('layouts.customer')
@section('title', 'New Support Ticket')
@section('customer_content')
<h4 class="fw-bold mb-4">Create Support Ticket</h4>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('customer.tickets.store') }}" method="POST">
            @csrf
            <x-alert />
            <div class="mb-3">
                <label class="form-label">Subject *</label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-select">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Message *</label>
                <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Ticket</button>
            <a href="{{ route('customer.tickets.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </form>
    </div>
</div>
@endsection
