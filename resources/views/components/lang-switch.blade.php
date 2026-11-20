<div class="lang" role="group" aria-label="{{ __('ui.lang') }}">
    @foreach ($locales as $locale)
        <a class="lang__item {{ app()->getLocale() === $locale ? 'is-active' : '' }}"
           href="{{ route('locale', $locale) }}"
           hreflang="{{ $locale }}"
           @if (app()->getLocale() === $locale) aria-current="true" @endif>{{ strtoupper($locale) }}</a>
    @endforeach
</div>
