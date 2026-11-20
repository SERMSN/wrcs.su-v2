@props(['project'])

@php
    $userVote = (int) ($project['user_vote'] ?? 0);
@endphp

<form class="rate" method="post" action="{{ route('projects.rate', $project['slug']) }}"
      data-rate
      data-saving-label="{{ __('ui.rating.saving') }}"
      data-error-label="{{ __('ui.rating.error') }}">
    @csrf
    <fieldset class="rate__fieldset">
        <legend class="rate__legend">{{ __('ui.rating.your') }}</legend>

        <div class="rate__stars" data-rate-stars>
            @for ($value = 1; $value <= 5; $value++)
                <button class="rate__star {{ $userVote >= $value ? 'is-on' : '' }}" type="submit"
                        name="value" value="{{ $value }}" data-value="{{ $value }}"
                        aria-label="{{ __('ui.rating.aria', ['rating' => $value]) }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2.6l2.9 5.9 6.5.95-4.7 4.6 1.1 6.45L12 17.45 6.2 20.5l1.1-6.45-4.7-4.6 6.5-.95z" />
                    </svg>
                </button>
            @endfor
        </div>
    </fieldset>

    <p class="rate__status" data-rate-status aria-live="polite">
        {{ $userVote > 0 ? __('ui.rating.your_value', ['value' => $userVote]) : __('ui.rating.hint') }}
    </p>
</form>
