@php
    $waNumber = $whatsappNumber ?? null;
    $waLink = $waNumber
        ? (str_starts_with($waNumber, 'http') ? $waNumber : 'https://wa.me/' . preg_replace('/\D+/', '', $waNumber))
        : null;
@endphp
@if($waLink)
<style>
#wa-btn{position:fixed;bottom:24px;right:24px;z-index:9999;width:56px;height:56px;border-radius:50%;background:#25D366;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(37,211,102,.45);transition:transform .2s,box-shadow .2s}
#wa-btn:hover{transform:scale(1.08);box-shadow:0 6px 20px rgba(37,211,102,.6)}
#wa-btn i{font-size:30px;color:#fff}
@media(max-width:991.98px){#wa-btn{bottom:80px}}
</style>
<a id="wa-btn" href="{{ $waLink }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp" title="Chat on WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>
@endif
