@props(['status'])
@php
$colors = [
    'pending'          => 'secondary',
    'confirmed'        => 'info',
    'processing'       => 'primary',
    'packed'           => 'warning',
    'shipped'          => 'purple',
    'out_for_delivery' => 'warning',
    'delivered'        => 'success',
    'cancelled'        => 'danger',
    'returned'         => 'dark',
    'refunded'         => 'info',
];
$color = $colors[$status] ?? 'secondary';
$label = ucwords(str_replace('_', ' ', $status));
@endphp
<span class="badge bg-{{ $color }}">{{ $label }}</span>
