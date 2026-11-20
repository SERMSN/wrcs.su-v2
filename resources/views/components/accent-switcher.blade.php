@php
    $accents = [
        [
            'key' => 'green',
            'label' => __('ui.accent.green'),
            'hint' => __('ui.accent.green_hint'),
        ],
        [
            'key' => 'blue',
            'label' => __('ui.accent.blue'),
            'hint' => __('ui.accent.blue_hint'),
        ],
        [
            'key' => 'pink',
            'label' => __('ui.accent.pink'),
            'hint' => __('ui.accent.pink_hint'),
        ],
        [
            'key' => 'purple',
            'label' => __('ui.accent.purple'),
            'hint' => __('ui.accent.purple_hint'),
        ],
        [
            'key' => 'red',
            'label' => __('ui.accent.red'),
            'hint' => __('ui.accent.red_hint'),
        ],
    ];
@endphp

<div class="accent-picker" data-accent-picker>
    <button class="icon-btn accent-picker__btn" type="button" data-accent-toggle
            data-accent-prefix="{{ __('ui.accent.label') }}"
            aria-haspopup="menu" aria-expanded="false" aria-controls="accent-menu"
            aria-label="{{ __('ui.accent.label') }}: {{ __('ui.accent.green') }}"
            title="{{ __('ui.accent.label') }}: {{ __('ui.accent.green') }}">
        <span class="accent-picker__orb" aria-hidden="true"></span>
        <span class="accent-picker__chev" aria-hidden="true">
            <svg viewBox="0 0 8 8"><path d="m1.5 2.5 2.5 2.5 2.5-2.5"/></svg>
        </span>
    </button>

    <div class="accent-picker__menu" id="accent-menu" data-accent-menu role="menu"
         aria-label="{{ __('ui.accent.title') }}" hidden>
        <p class="accent-picker__title mono">{{ __('ui.accent.title') }}</p>
        <p class="accent-picker__hint">{{ __('ui.accent.hint') }}</p>

        <div class="accent-picker__list">
            @foreach ($accents as $accent)
                <button class="accent-option {{ $loop->first ? 'is-active' : '' }}" type="button"
                        role="menuitemradio" aria-checked="{{ $loop->first ? 'true' : 'false' }}"
                        data-accent-option="{{ $accent['key'] }}"
                        data-accent-label="{{ $accent['label'] }}">
                    <span class="accent-option__swatch accent-option__swatch--{{ $accent['key'] }}" aria-hidden="true"></span>
                    <span class="accent-option__copy">
                        <span class="accent-option__name">{{ $accent['label'] }}</span>
                        <span class="accent-option__hint mono">{{ $accent['hint'] }}</span>
                    </span>
                    <svg class="accent-option__check" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="m3 8.5 3.2 3.2L13 4.8"/>
                    </svg>
                </button>
            @endforeach
        </div>

        <p class="accent-picker__foot mono">
            <span class="accent-picker__dots" aria-hidden="true"><i></i><i></i><i></i></span>
            {{ __('ui.accent.foot') }}
        </p>
    </div>
</div>
