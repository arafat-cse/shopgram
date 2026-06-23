@props(['items' => []])
<nav aria-label="breadcrumb" class="bg-light py-2 px-3 rounded mb-3">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        @foreach($items as $label => $url)
            @if($loop->last)
                <li class="breadcrumb-item active">{{ $label }}</li>
            @else
                <li class="breadcrumb-item"><a href="{{ $url }}">{{ $label }}</a></li>
            @endif
        @endforeach
    </ol>
</nav>
