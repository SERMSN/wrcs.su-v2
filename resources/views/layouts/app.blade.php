<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $meta['title'] ?? $brand }}</title>
    <meta name="description" content="{{ $meta['description'] ?? '' }}">
    <meta name="author" content="{{ $meta['author'] ?? $brand }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $brand }}">
    <meta property="og:title" content="{{ $meta['title'] ?? $brand }}">
    <meta property="og:description" content="{{ $meta['description'] ?? '' }}">
    <meta property="og:image" content="{{ url($meta['image'] ?? '/assets/img/og-cover.svg') }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="{{ asset('assets/img/favicon.svg') }}" type="image/svg+xml">

    <meta name="theme-color" content="#0a0d12" media="(prefers-color-scheme: dark)">
    <meta name="theme-color" content="#f5f6f2" media="(prefers-color-scheme: light)">

    <script>
        (function () {
            try {
                var stored = localStorage.getItem('wrcs-theme');
                var accent = localStorage.getItem('wrcs-accent');
                document.documentElement.classList.add('js');
                var system = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
                document.documentElement.dataset.theme = (stored === 'light' || stored === 'dark') ? stored : system;
                document.documentElement.dataset.accent = ['blue', 'pink', 'purple', 'red'].includes(accent) ? accent : 'green';
            } catch (e) {
                document.documentElement.dataset.theme = 'dark';
                document.documentElement.dataset.accent = 'green';
            }
        })();
    </script>

    <link rel="preload" href="{{ asset('assets/fonts/manrope-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/fonts/manrope-cyrillic.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}">

    @stack('head')
</head>
<body class="@yield('body_class')">
    <a class="skip-link" href="#main">{{ __('ui.skip') }}</a>

    @include('partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.footer')

    <button class="to-top" type="button" data-to-top aria-label="{{ __('ui.footer.top') }}">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 19V5M5.5 11.5 12 5l6.5 6.5"/></svg>
    </button>

    <script src="{{ asset('assets/js/app.js') }}?v={{ filemtime(public_path('assets/js/app.js')) }}" defer></script>
</body>
</html>
