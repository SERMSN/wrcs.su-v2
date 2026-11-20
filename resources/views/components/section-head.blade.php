@props([
    'eyebrow' => '',
    'title' => '',
    'lead' => '',
])

<header class="section__head" data-reveal>
    <p class="eyebrow">{{ $eyebrow }}</p>
    <h2 class="section__title">{{ $title }}</h2>
    @if ($lead !== '')
        <p class="section__lead">{{ $lead }}</p>
    @endif
</header>
