<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locales = (array) config('portfolio.locales', ['ru', 'en']);
        $locale = $request->session()->get('locale');

        if (! is_string($locale) || ! in_array($locale, $locales, true)) {
            $locale = $this->negotiate($request, $locales);
        }

        app()->setLocale($locale);

        return $next($request);
    }

    /**
     * @param  array<int, string>  $locales
     */
    private function negotiate(Request $request, array $locales): string
    {
        $preferred = $request->getPreferredLanguage($locales);

        if (is_string($preferred) && in_array($preferred, $locales, true)) {
            return $preferred;
        }

        $fallback = (string) config('app.locale');

        return in_array($fallback, $locales, true) ? $fallback : $locales[0];
    }
}
