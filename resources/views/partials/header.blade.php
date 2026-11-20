<header class="header" data-header>
    <div class="container header__inner">
        <x-logo />

        <nav class="nav" id="site-nav" data-nav aria-label="{{ __('ui.menu') }}">
            <a class="nav__link" href="{{ url('/') }}#about">{{ __('ui.nav.about') }}</a>
            <a class="nav__link" href="{{ url('/') }}#work">{{ __('ui.nav.work') }}</a>
            <a class="nav__link" href="{{ url('/') }}#stack">{{ __('ui.nav.stack') }}</a>
            <a class="nav__link" href="{{ url('/') }}#contact">{{ __('ui.nav.contact') }}</a>
            <a class="btn btn--primary btn--sm nav__cta" href="{{ url('/') }}#contact">{{ __('ui.hero.cta_contact') }}</a>
        </nav>

        <div class="header__tools">
            <x-lang-switch />
            <x-accent-switcher />
            <x-theme-toggle />

            <button class="icon-btn menu-btn" type="button" data-menu-toggle aria-expanded="false"
                    aria-controls="site-nav" aria-label="{{ __('ui.menu') }}">
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>
