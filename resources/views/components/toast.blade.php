<div class="toast-container">
    @foreach(['success' => 'success', 'error' => 'danger', 'info' => 'info', 'warning' => 'warning'] as $key => $class)
        @if(session($key))
        <div class="toast align-items-center text-bg-{{ $class }} border-0 show" role="alert" data-bs-autohide="true" data-bs-delay="4000">
            <div class="d-flex">
                <div class="toast-body">{{ session($key) }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        @endif
    @endforeach
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toast').forEach(el => new bootstrap.Toast(el).show());
});
</script>
