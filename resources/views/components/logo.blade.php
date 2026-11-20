@php
    $parts = explode('.', (string) $brand, 2);
    $name = $parts[0];
    $tld = $parts[1] ?? 'ops';
    $uid = 'lg-'.substr(md5($brand), 0, 8);
@endphp

<a class="logo" href="{{ route('home') }}" aria-label="{{ __('ui.logo_home') }} — {{ $brand }}">
    <svg class="logo__mark" viewBox="0 0 40 40" width="38" height="38" aria-hidden="true" focusable="false">
        <defs>
            <linearGradient id="{{ $uid }}" x1="5" y1="14" x2="31" y2="26" gradientUnits="userSpaceOnUse">
                <stop stop-color="var(--accent)" />
                <stop offset="1" stop-color="var(--accent-2)" />
            </linearGradient>
        </defs>
        <rect class="logo__tile" x="0.5" y="0.5" width="39" height="39" rx="11.5" />
        <path class="logo__loop"
              d="M5 20C5 13.8 12.4 13.8 18 20C23.6 26.2 31 26.2 31 20C31 13.8 23.6 13.8 18 20C12.4 26.2 5 26.2 5 20Z"
              stroke="url(#{{ $uid }})" />
        <circle class="logo__node" cx="5" cy="20" r="1.8" />
        <circle class="logo__node" cx="31" cy="20" r="1.8" />
        <circle class="logo__node logo__node--core" cx="18" cy="20" r="1.4" />
    </svg>
    <span class="logo__text">
        <span class="logo__name">{{ $name }}</span><span class="logo__dot">.</span><span class="logo__tld">{{ $tld }}</span>
    </span>
</a>
