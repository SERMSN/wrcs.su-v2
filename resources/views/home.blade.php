@extends('layouts.app')

@php
    $identity = $profile['identity'];
    $brand = (string) config('portfolio.brand');
    $name = loc($identity['name']);
    $headline = loc($identity['headline'] ?? $identity['name']);
    $contacts = $profile['contacts'] ?? [];
    $email = collect($contacts)->firstWhere('type', 'email')['label'] ?? '';
    $uptime = ($profile['metrics'][0]['value'] ?? '99.98').($profile['metrics'][0]['suffix'] ?? '%');
    $since = $identity['since'] ?? '';
    $handle = $identity['handle'] ?? 'wrcs';
    $projectsCount = count($projects);
@endphp

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $brand,
            'description' => loc($identity['tagline']),
            'url' => url('/'),
            'address' => loc($identity['location']),
            'sameAs' => collect($contacts)->pluck('href')->filter(fn ($href) => str_starts_with((string) $href, 'http'))->values()->all(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('content')
    <section class="hero">
        <div class="hero__glow" aria-hidden="true"></div>

        <div class="container hero__grid">
            <div class="hero__copy">
                <p class="eyebrow eyebrow--live">
                    <span class="status-dot" aria-hidden="true"></span>
                    // {{ $brand }} · {{ __('ui.hero.eyebrow') }}
                </p>

                <h1 class="hero__title">
                    <span class="hero__name {{ mb_strlen($headline) > 16 ? 'hero__name--long' : '' }}">{{ $headline }}</span>
                    <span class="hero__role">{{ loc($identity['role']) }}</span>
                </h1>

                <p class="hero__tagline">{{ loc($identity['tagline']) }}</p>

                <div class="hero__actions">
                    <a class="btn btn--primary" href="#work">
                        {{ __('ui.hero.cta_work') }}
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h13M12.5 5.5 19 12l-6.5 6.5" /></svg>
                    </a>
                    <a class="btn btn--ghost" href="#contact">{{ __('ui.hero.cta_contact') }}</a>
                </div>

                <ul class="hero__meta">
                    <li>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z" /><circle cx="12" cy="10" r="2.6" /></svg>
                        {{ loc($identity['location']) }}
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3.5 2" /></svg>
                        {{ $identity['timezone'] }}
                    </li>
                    <li>
                        <span class="status-dot" aria-hidden="true"></span>
                        {{ loc($identity['status']) }}
                    </li>
                </ul>
            </div>

            <div class="hero__side">
                <div class="terminal" data-reveal>
                    <div class="terminal__bar">
                        <span class="terminal__dots" aria-hidden="true"><i></i><i></i><i></i></span>
                        <span class="terminal__title">{{ $handle }}@ops — ~/profile</span>
                        <span class="terminal__badge">zsh</span>
                    </div>

                    <pre class="terminal__body"><code><span class="t-line"><span class="t-prompt">~ $</span> {{ __('ui.hero.terminal_whoami') }}</span>
<span class="t-line t-out">{{ $identity['nickname'] ?? $handle }} <span class="t-dim">/</span> <span class="t-at">&#64;</span>{{ $handle }}</span>
<span class="t-line"> </span>
<span class="t-line"><span class="t-prompt">~ $</span> {{ __('ui.hero.terminal_kubectl') }}</span>
<span class="t-line t-out">{{ 'NAME'.str_repeat(' ', 6).'ROLE'.str_repeat(' ', 10).'UPTIME'.str_repeat(' ', 4).'SINCE' }}</span>
<span class="t-line t-out">{{ str_pad($handle, 10).str_pad(__('ui.hero.terminal_role'), 14).str_pad($uptime, 10).$since }}</span>
<span class="t-line"> </span>
<span class="t-line"><span class="t-prompt">~ $</span> {{ __('ui.hero.terminal_cat') }}</span>
<span class="t-line t-out t-accent">{{ __('ui.hero.terminal_focus') }}</span>
<span class="t-line"><span class="t-prompt">~ $</span> <span class="t-caret" aria-hidden="true"></span></span></code></pre>

                    <div class="terminal__foot">
                        <span class="status-dot" aria-hidden="true"></span>
                        <span class="mono">sla {{ $uptime }} · {{ collect($profile['skills'])->count() }} skills · {{ $projectsCount }} cases</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="kpis" aria-label="{{ __('ui.about.eyebrow') }}">
        <div class="container kpis__grid">
            @foreach ($profile['metrics'] as $metric)
                @php
                    $spark = array_values($metric['spark'] ?? []);
                    $min = $spark === [] ? 0 : min($spark);
                    $max = $spark === [] ? 1 : max($spark);
                    $range = max(1, $max - $min);
                    $points = [];
                    foreach ($spark as $index => $value) {
                        $x = count($spark) > 1 ? $index / (count($spark) - 1) * 100 : 0;
                        $y = 22 - (($value - $min) / $range) * 18;
                        $points[] = round($x, 2).','.round($y, 2);
                    }
                @endphp
                <article class="kpi" data-reveal>
                    <div class="kpi__top">
                        <span class="kpi__value">{{ $metric['value'] }}<i>{{ is_array($metric['suffix'] ?? null) ? loc($metric['suffix']) : ($metric['suffix'] ?? '') }}</i></span>
                        <svg class="spark" viewBox="0 0 100 24" preserveAspectRatio="none" aria-hidden="true">
                            <polyline points="{{ implode(' ', $points) }}" />
                        </svg>
                    </div>
                    <p class="kpi__label">{{ loc($metric['label']) }}</p>
                    <p class="kpi__hint">{{ loc($metric['hint']) }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section" id="about">
        <div class="container">
            <x-section-head
                :eyebrow="__('ui.about.eyebrow')"
                :title="__('ui.about.title')"
                :lead="__('ui.about.lead')"
            />

            <div class="about__grid">
                <div class="about__text" data-reveal>
                    @foreach (loc_lines($profile['bio']) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach

                    <h3 class="about__sub">{{ __('ui.about.principles') }}</h3>
                    <ul class="principles">
                        @foreach ($profile['principles'] as $index => $principle)
                            <li>
                                <span class="mono">0{{ $index + 1 }}</span>
                                {{ loc($principle) }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <aside class="spec" data-reveal>
                    <div class="spec__head">
                        <img class="spec__avatar" src="{{ asset('assets/img/logo.svg') }}" alt="{{ $brand }}" width="92" height="92">
                        <div class="spec__who">
                            <strong>{{ $name }}</strong>
                            <span>{{ loc($identity['role']) }}</span>
                            <span class="badge badge--live"><i aria-hidden="true"></i>{{ __('ui.hero.availability') }}</span>
                        </div>
                    </div>

                    <dl class="spec__rows">
                        <div>
                            <dt>{{ __('ui.about.facts.location') }}</dt>
                            <dd>{{ loc($identity['location']) }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('ui.about.facts.timezone') }}</dt>
                            <dd>{{ $identity['timezone'] }}</dd>
                        </div>
                        <div>
                            <dt>{{ __('ui.about.facts.experience') }}</dt>
                            <dd>{{ years_phrase($experienceYears) }} {{ __('ui.about.years_suffix') }}</dd>
                        </div>
                    </dl>

                    <div class="spec__certs">
                        <span class="spec__label">{{ __('ui.about.facts.certs') }}</span>
                        <ul>
                            @foreach ($profile['certifications'] as $cert)
                                <li>
                                    <strong>{{ $cert['name'] }}</strong>
                                    <span>{{ $cert['org'] }} · {{ $cert['year'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>
            </div>

            <ol class="timeline" data-reveal>
                @foreach ($profile['experience'] as $job)
                    @php
                        $period = app()->getLocale() === 'en' ? ($job['periodEn'] ?? $job['period']) : $job['period'];
                    @endphp
                    <li class="timeline__item">
                        <span class="timeline__period mono">{{ $period }}</span>
                        <h3 class="timeline__company">
                            {{ $job['company'] }}
                            <span>· {{ loc($job['role']) }}</span>
                        </h3>
                        <p>{{ loc($job['summary']) }}</p>
                        <ul class="tags">
                            @foreach ($job['stack'] as $tag)
                                <li>{{ $tag }}</li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="section" id="process">
        <div class="container">
            <x-section-head
                :eyebrow="__('ui.pipeline.eyebrow')"
                :title="__('ui.pipeline.title')"
                :lead="__('ui.pipeline.lead')"
            />

            <div class="pipe" data-reveal>
                <span class="pipe__line" aria-hidden="true"></span>
                <ol class="pipe__track">
                    @foreach (__('ui.pipeline.steps') as $key => $step)
                        <li class="pipe__step">
                            <span class="pipe__index mono">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="pipe__node" aria-hidden="true"></span>
                            <h3 class="pipe__title">{{ $step['title'] }}</h3>
                            <p class="pipe__tool mono">{{ $step['tool'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    <section class="section" id="stack">
        <div class="container">
            <x-section-head
                :eyebrow="__('ui.skills.eyebrow')"
                :title="__('ui.skills.title')"
                :lead="__('ui.skills.lead')"
            />

            <div class="skills__grid">
                <ul class="skills__bars" data-reveal>
                    @foreach ($profile['skills'] as $skill)
                        <x-skill-bar :skill="$skill" />
                    @endforeach
                </ul>

                <aside class="skills__side" data-reveal>
                    <h3 class="skills__side-title">{{ __('ui.skills.donut_title') }}</h3>
                    <x-donut :items="$profile['timeSplit']" :years="$experienceYears" />
                    <p class="skills__side-note mono">{{ __('ui.skills.donut_note') }}</p>
                </aside>
            </div>
        </div>
    </section>

    <section class="section" id="work">
        <div class="container">
            <x-section-head
                :eyebrow="__('ui.work.eyebrow')"
                :title="__('ui.work.title')"
                :lead="__('ui.work.lead')"
            />

            <div class="work__toolbar" data-work-toolbar>
                <div class="chips" role="group" aria-label="{{ __('ui.work.title') }}">
                    <button class="chip is-active" type="button" data-filter="all" aria-pressed="true">{{ __('ui.work.all') }}</button>
                    @foreach ($stacks as $stack)
                        <button class="chip" type="button" data-filter="{{ $stack['slug'] }}" aria-pressed="false">{{ $stack['name'] }}</button>
                    @endforeach
                </div>

                <div class="work__controls">
                    <label class="switch">
                        <input type="checkbox" data-min-rating>
                        <span class="switch__ui" aria-hidden="true"></span>
                        <span class="switch__label">{{ __('ui.work.min_rating') }}</span>
                    </label>

                    <label class="select">
                        <span>{{ __('ui.work.sort') }}</span>
                        <select data-sort>
                            <option value="year">{{ __('ui.work.sort_year') }}</option>
                            <option value="rating">{{ __('ui.work.sort_rating') }}</option>
                        </select>
                    </label>
                </div>
            </div>

            <p class="work__count mono" data-work-count aria-live="polite"
               data-template="{{ __('ui.work.found', ['count' => ':count']) }}">
                {{ __('ui.work.found', ['count' => $projectsCount]) }}
            </p>

            <div class="work__grid" data-grid>
                @foreach ($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>

            <div class="work__empty" data-work-empty hidden>
                <p>{{ __('ui.work.empty') }}</p>
                <button class="btn btn--ghost btn--sm" type="button" data-work-reset>{{ __('ui.work.reset') }}</button>
            </div>
        </div>
    </section>

    <section class="section" id="activity">
        <div class="container">
            <x-section-head
                :eyebrow="__('ui.activity.eyebrow')"
                :title="__('ui.activity.title')"
                :lead="__('ui.activity.lead')"
            />

            <div class="activity__grid">
                <x-heatmap :heatmap="$heatmap" />

                <aside class="activity__side" data-reveal>
                    <div class="activity__stat">
                        <span class="activity__stat-value mono">{{ number_format($heatmap['average'], 1, app()->getLocale() === 'ru' ? ',' : '.', '') }}</span>
                        <span class="activity__stat-label">{{ __('ui.activity.per_day') }}</span>
                    </div>
                    <div class="activity__stat">
                        <span class="activity__stat-value mono">{{ $heatmap['peak'] }}</span>
                        <span class="activity__stat-label">{{ __('ui.activity.peak') }}</span>
                    </div>
                    <p class="activity__note">{{ __('ui.activity.note') }}</p>
                </aside>
            </div>
        </div>
    </section>

    <section class="section section--contact" id="contact">
        <div class="container">
            <div class="contact" data-reveal>
                <div class="contact__glow" aria-hidden="true"></div>

                <p class="eyebrow">{{ __('ui.contact.eyebrow') }}</p>
                <h2 class="contact__title">{{ __('ui.contact.title') }}</h2>
                <p class="contact__lead">{{ __('ui.contact.lead') }}</p>

                <div class="contact__actions">
                    <a class="btn btn--primary btn--lg" href="mailto:{{ $email }}">{{ __('ui.contact.cta') }}</a>
                    <button class="btn btn--ghost btn--lg" type="button" data-copy="{{ $email }}"
                        data-copied-label="{{ __('ui.contact.copied') }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="9" y="9" width="11" height="11" rx="2.5" /><path d="M15 5.5A2.5 2.5 0 0 0 12.5 3h-6A3.5 3.5 0 0 0 3 6.5v6A2.5 2.5 0 0 0 5.5 15" /></svg>
                        <span data-copy-label>{{ __('ui.contact.copy') }}</span>
                    </button>
                </div>

                <ul class="contact__links">
                    @foreach ($contacts as $contact)
                        @if ($contact['type'] !== 'email')
                            <li>
                                <a href="{{ $contact['href'] }}" target="_blank" rel="noopener noreferrer">
                                    <span class="mono">{{ strtoupper($contact['type']) }}</span>
                                    {{ $contact['label'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                    <li>
                        <a href="mailto:{{ $email }}">
                            <span class="mono">EMAIL</span>
                            {{ $email }}
                        </a>
                    </li>
                </ul>

                <p class="contact__status">
                    <span class="status-dot" aria-hidden="true"></span>
                    {{ __('ui.contact.availability') }}
                </p>
            </div>
        </div>
    </section>
@endsection
