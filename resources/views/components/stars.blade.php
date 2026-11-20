@props([
    'average' => 0.0,
    'count' => 0,
    'size' => 'sm',
    'showCount' => true,
])

@php
    $average = max(0, min(5, (float) $average));
    $percent = $average / 5 * 100;
    $formatted = number_format($average, 1, app()->getLocale() === 'ru' ? ',' : '.', '');
@endphp

<span class="stars stars--{{ $size }}" role="img" aria-label="{{ __('ui.rating.aria', ['rating' => $formatted]) }}">
    <span class="stars__base" aria-hidden="true">★★★★★</span>
    <span class="stars__fill" aria-hidden="true" style="width: {{ $percent }}%">★★★★★</span>
</span>
@if ($showCount && $count > 0)
    <span class="stars__count">{{ count_label((int) $count) }}</span>
@endif
