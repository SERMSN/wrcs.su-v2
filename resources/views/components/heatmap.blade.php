@props(['heatmap'])

@php
    $total = number_format((int) $heatmap['total'], 0, '', app()->getLocale() === 'ru' ? ' ' : ',');
@endphp

<div class="heatmap" data-reveal>
    <div class="heatmap__months" aria-hidden="true">
        @foreach ($heatmap['weeks'] as $index => $week)
            @if ($week['month'])
                <span style="grid-column: {{ $index + 1 }}">{{ $week['month'] }}</span>
            @endif
        @endforeach
    </div>

    <div class="heatmap__scroll">
        <div class="heatmap__grid" role="img" aria-label="{{ __('ui.activity.lead') }}">
            @foreach ($heatmap['weeks'] as $week)
                <div class="heatmap__week">
                    @foreach ($week['cells'] as $cell)
                        <span class="heatmap__cell" data-level="{{ $cell['level'] }}" title="{{ $cell['label'] }}"></span>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="heatmap__legend">
        <span>{{ __('ui.activity.less') }}</span>
        @for ($level = 0; $level <= 4; $level++)
            <i data-level="{{ $level }}" aria-hidden="true"></i>
        @endfor
        <span>{{ __('ui.activity.more') }}</span>
        <b class="heatmap__total"><span class="mono">{{ $total }}</span> {{ __('ui.activity.total') }}</b>
    </div>
</div>
