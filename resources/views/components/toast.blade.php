@php
    $toastMap = [
        'success' => ['class' => 'success', 'icon' => 'bi-check-circle-fill', 'title' => 'Success'],
        'error' => ['class' => 'danger', 'icon' => 'bi-x-circle-fill', 'title' => 'Error'],
        'info' => ['class' => 'info', 'icon' => 'bi-info-circle-fill', 'title' => 'Info'],
        'warning' => ['class' => 'warning', 'icon' => 'bi-exclamation-triangle-fill', 'title' => 'Warning'],
    ];

    $operationTitle = function (string $message, string $fallback) {
        $text = strtolower($message);

        return match (true) {
            str_contains($text, 'created') || str_contains($text, 'added') => 'Created',
            str_contains($text, 'updated') || str_contains($text, 'saved') => 'Updated',
            str_contains($text, 'deleted') || str_contains($text, 'removed') => 'Deleted',
            default => $fallback,
        };
    };
@endphp

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">
    @foreach($toastMap as $key => $meta)
        @if(session($key))
            @php
                $message = session($key);
                $title = $key === 'success' ? $operationTitle($message, $meta['title']) : $meta['title'];
            @endphp
            <div class="toast border-0 shadow-sm mb-2" role="alert" data-bs-autohide="true" data-bs-delay="4200">
                <div class="toast-header text-bg-{{ $meta['class'] }} border-0">
                    <i class="bi {{ $meta['icon'] }} me-2"></i>
                    <strong class="me-auto">{{ $title }}</strong>
                    <button type="button" class="btn-close {{ $meta['class'] === 'warning' || $meta['class'] === 'info' ? '' : 'btn-close-white' }}" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body bg-white text-dark">
                    {{ $message }}
                </div>
            </div>
        @endif
    @endforeach
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toast').forEach((toast) => {
        new bootstrap.Toast(toast).show();
    });
});
</script>
