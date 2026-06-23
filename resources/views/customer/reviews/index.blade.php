@extends('layouts.customer')
@section('title', 'My Reviews')
@section('customer_content')
<h4 class="fw-bold mb-4">My Reviews</h4>
@if($reviews->count())
<div class="row g-3">
    @foreach($reviews as $review)
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex gap-3">
                        <img src="{{ $review->product->thumbnail ? asset('storage/'.$review->product->thumbnail) : asset('images/no-image.png') }}"
                             width="60" height="60" style="object-fit:cover;border-radius:6px" alt="">
                        <div>
                            <a href="{{ route('products.show', $review->product->slug) }}" class="fw-semibold text-dark text-decoration-none">{{ $review->product->name }}</a>
                            <div class="my-1">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : ' text-muted' }}"></i>
                                @endfor
                            </div>
                            <p class="mb-0 text-muted small">{{ $review->comment }}</p>
                        </div>
                    </div>
                    <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-4">{{ $reviews->links() }}</div>
@else
<div class="text-center py-5">
    <i class="bi bi-chat-square-text text-muted" style="font-size:3rem"></i>
    <p class="text-muted mt-2">No reviews yet. Buy products and leave reviews!</p>
    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">Shop Now</a>
</div>
@endif
@endsection
