@extends('layouts.app', ['body_class' => 'page-case'])

@php
    $rating = $project['rating'];
    $average = number_format($rating['average'], 1, app()->getLocale() === 'ru' ? ',' : '.', '');
@endphp

@section('content')
    <article class="case">
        <div class="container">
            <a class="backlink" href="{{ url('/') }}#work">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M11.5 5.5 5 12l6.5 6.5" /></svg>
                {{ __('ui.project.back') }}
            </a>

            <header class="case__head" data-reveal>
                <p class="eyebrow eyebrow--live">
                    <span class="status-dot" aria-hidden="true"></span>
                    case / {{ $project['year'] }} — {{ $project['client'] }}
                </p>
                <h1 class="case__title">{{ loc($project['title']) }}</h1>
                <p class="case__summary">{{ loc($project['summary']) }}</p>

                <dl class="case__meta">
                    <div>
                        <dt>{{ __('ui.project.client') }}</dt>
                        <dd>{{ $project['client'] }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('ui.project.year') }}</dt>
                        <dd class="mono">{{ $project['year'] }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('ui.project.role') }}</dt>
                        <dd>{{ loc($project['role']) }}</dd>
                    </div>
                </dl>
            </header>
        </div>

        <div class="container">
            <figure class="case__cover" data-reveal>
                <img src="{{ $project['cover'] }}" alt="{{ loc($project['title']) }}" width="1200" height="800" fetchpriority="high">
            </figure>

            <ul class="case__metrics" data-reveal>
                @foreach ($project['metrics'] as $metric)
                    <li>
                        <span class="case__metric-value">{{ $metric['value'] }}</span>
                        <span class="case__metric-label">{{ loc($metric['label']) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="container case__grid">
            <div class="case__body" data-reveal>
                <h2>{{ __('ui.project.about') }}</h2>
                @foreach (loc_lines($project['description']) as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach

                <h2>{{ __('ui.project.stack') }}</h2>
                <ul class="tags tags--lg">
                    @foreach ($project['stack'] as $tag)
                        <li>{{ $tag }}</li>
                    @endforeach
                </ul>

                <a class="btn btn--primary case__cta" href="{{ url('/') }}#contact">
                    {{ __('ui.project.discuss') }}
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h13M12.5 5.5 19 12l-6.5 6.5" /></svg>
                </a>
            </div>

            <aside class="case__aside">
                @if (session('status'))
                    <p class="flash" role="status">{{ session('status') }}</p>
                @endif

                <div class="rate-panel" data-rate-panel>
                    <p class="eyebrow">{{ __('ui.rating.title') }}</p>

                    <div class="rate-panel__score">
                        <span class="rate-panel__value" data-rate-avg>{{ $average }}</span>
                        <x-stars :average="$rating['average']" :count="0" size="lg" :showCount="false" />
                        <span class="rate-panel__count" data-rate-count>{{ count_label($rating['count']) }}</span>
                    </div>

                    <x-rating :project="$project" />

                    <div class="dist">
                        <p class="dist__title mono">{{ __('ui.rating.distribution') }}</p>
                        @foreach ($rating['histogram'] as $index => $votes)
                            @php $star = 5 - $index; @endphp
                            <div class="dist__row">
                                <span class="dist__label mono">{{ $star }}★</span>
                                <span class="dist__track">
                                    <span class="dist__fill" data-hist-fill="{{ $star }}" style="width: {{ $rating['percent'][$index] }}%"></span>
                                </span>
                                <span class="dist__count mono" data-hist-count="{{ $star }}">{{ $votes }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>

        @if (count($related) > 0)
            <section class="section related">
                <div class="container">
                    <h2 class="related__title">{{ __('ui.project.related') }}</h2>
                    <div class="work__grid">
                        @foreach ($related as $item)
                            <x-project-card :project="$item" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </article>
@endsection
