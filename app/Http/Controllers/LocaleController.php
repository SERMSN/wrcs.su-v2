<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

final class LocaleController extends Controller
{
    public function __invoke(string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, (array) config('portfolio.locales'), true), 404);

        session(['locale' => $locale]);

        return redirect()->back(fallback: route('home'));
    }
}
