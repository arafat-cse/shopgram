@props([
    'action',
    'message' => 'Do you want to delete this item?',
    'title' => 'Are you sure?',
    'label' => 'Delete',
    'icon' => 'bi-trash',
    'size' => 'sm',
    'variant' => 'outline-danger',
    'formClass' => 'd-inline',
    'buttonClass' => '',
    'buttonStyle' => '',
])

@php
    $slotContent = trim((string) $slot);
    $isIconOnly = $label === '' && $slotContent === '';
@endphp

<form action="{{ $action }}" method="POST" class="{{ $formClass }}" data-confirm-title="{{ $title }}" data-confirm-message="{{ $message }}">
    @csrf
    @method('DELETE')
    <button type="submit"
            class="btn btn-{{ $size }} btn-{{ $variant }} {{ $buttonClass }}"
            @if($buttonStyle) style="{{ $buttonStyle }}" @endif
            @if($isIconOnly) aria-label="{{ $message }}" title="{{ $message }}" @endif>
        @if($slotContent !== '')
            {{ $slot }}
        @else
            @if($icon)
                <i class="bi {{ $icon }}{{ $label ? ' me-1' : '' }}"></i>
            @endif
            {{ $label }}
        @endif
    </button>
</form>
