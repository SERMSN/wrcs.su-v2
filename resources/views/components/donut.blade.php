@props(['items' => [], 'years' => 0])

@php
    $palette = ['var(--accent)', 'var(--accent-2)', 'var(--star)', '#a78bfa'];
    $stops = [];
    $cursor = 0.0;

    foreach ($items as $index => $item) {
        $value = (float) ($item['value'] ?? 0);
        $stops[] = $palette[$index % count($palette)].' '.round($cursor, 2).'% '.round($cursor + $value, 2).'%';
        $cursor += $value;
    }
@endphp

<div class="donut-wrap">
    <div class="donut" style="--donut: {{ 'conic-gradient(from -90deg, '.implode(', ', $stops).')' }}"
         role="img" aria-label="{{ __('ui.skills.donut_title') }}">
        <span class="donut__hole">
            <strong>{{ $years }}</strong>
            <span>{{ years_word((int) $years) }} {{ __('ui.skills.donut_center') }}</span>
        </span>
    </div>

    <ul class="donut__legend">
        @foreach ($items as $index => $item)
            <li style="--dot: {{ $palette[$index % count($palette)] }}">
                <i aria-hidden="true"></i>
                <span>{{ loc($item['label']) }}</span>
                <b>{{ $item['value'] }}%</b>
            </li>
        @endforeach
    </ul>
</div>
