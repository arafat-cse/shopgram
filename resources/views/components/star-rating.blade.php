@props(['rating' => 0, 'max' => 5])
@for($i = 1; $i <= $max; $i++)
    <i class="bi bi-star{{ $i <= $rating ? '-fill text-warning' : ' text-muted' }}"></i>
@endfor
