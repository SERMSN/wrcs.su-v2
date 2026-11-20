<footer class="footer">
    <div class="container footer__inner">
        <div class="footer__brand">
            <x-logo />
            <p>{{ __('ui.footer.note') }}</p>
        </div>

        <nav class="footer__nav" aria-label="{{ __('ui.menu') }}">
            <a href="{{ url('/') }}#about">{{ __('ui.nav.about') }}</a>
            <a href="{{ url('/') }}#work">{{ __('ui.nav.work') }}</a>
            <a href="{{ url('/') }}#stack">{{ __('ui.nav.stack') }}</a>
            <a href="{{ url('/') }}#contact">{{ __('ui.nav.contact') }}</a>
        </nav>

        <div class="footer__meta">
            <span class="mono">{{ __('ui.footer.built') }}</span>
            <span>{{ __('ui.footer.rights', ['year' => now()->year, 'brand' => $brand]) }}</span>
        </div>
    </div>
</footer>
