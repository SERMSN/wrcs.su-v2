@props(['project'])

@php
    $stack = $project['stack'] ?? [];
    $rating = $project['rating'] ?? ['average' => 0, 'count' => 0];
@endphp

<article class="card" data-reveal data-card
         data-stack="{{ implode(' ', $project['stack_slugs'] ?? []) }}"
         data-rating="{{ $rating['average'] }}"
         data-year="{{ $project['year'] }}">
    <a class="card__media" href="{{ $project['url'] }}" tabindex="-1" aria-hidden="true">
        <img src="{{ $project['cover'] }}" alt="" width="1200" height="800" loading="lazy" decoding="async">
        <span class="card__year">{{ $project['year'] }}</span>
        @if (! empty($project['featured']))
            <span class="card__badge">{{ __('ui.work.featured') }}</span>
        @endif
    </a>

    <div class="card__body">
        <h3 class="card__title">
            <a href="{{ $project['url'] }}">{{ loc($project['title']) }}</a>
        </h3>
        <p class="card__summary">{{ loc($project['summary']) }}</p>

        <ul class="tags">
            @foreach (array_slice($stack, 0, 3) as $tag)
                <li>{{ $tag }}</li>
            @endforeach
            @if (count($stack) > 3)
                <li class="tags__more">+{{ count($stack) - 3 }}</li>
            @endif
        </ul>

        <div class="card__foot">
            <x-stars :average="$rating['average']" :count="$rating['count']" />
            <span class="card__arrow" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M5 12h13M12.5 5.5 19 12l-6.5 6.5" /></svg>
            </span>
        </div>
    </div>
</article>
