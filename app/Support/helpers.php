<?php

use Illuminate\Support\Str;

if (! function_exists('loc')) {
    /**
     * Resolve a bilingual value stored as ["ru" => "...", "en" => "..."].
     */
    function loc(mixed $value, ?string $locale = null): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (! is_array($value) || $value === []) {
            return '';
        }

        $locale ??= app()->getLocale();
        $fallback = (string) config('app.fallback_locale', 'en');

        if (isset($value[$locale]) && is_scalar($value[$locale])) {
            return (string) $value[$locale];
        }

        if (isset($value[$fallback]) && is_scalar($value[$fallback])) {
            return (string) $value[$fallback];
        }

        $first = reset($value);

        return is_scalar($first) ? (string) $first : '';
    }
}

if (! function_exists('loc_lines')) {
    /**
     * @return array<int, string>
     */
    function loc_lines(mixed $value, ?string $locale = null): array
    {
        if (is_string($value)) {
            return [$value];
        }

        if (! is_array($value)) {
            return [];
        }

        if (array_is_list($value)) {
            return array_values(array_map(
                static fn ($item) => is_string($item) ? $item : (string) loc($item, $locale),
                $value,
            ));
        }

        $locale ??= app()->getLocale();
        $localized = $value[$locale] ?? $value[(string) config('app.fallback_locale', 'en')] ?? null;

        if (is_array($localized)) {
            return array_values(array_map(
                static fn ($item) => is_string($item) ? $item : (string) loc($item, $locale),
                $localized,
            ));
        }

        return [(string) loc($value, $locale)];
    }
}

if (! function_exists('slugify_stack')) {
    function slugify_stack(string $stack): string
    {
        return Str::slug($stack, '-');
    }
}

if (! function_exists('count_label')) {
    /**
     * Localised "{count} ratings" with Russian plural rules.
     */
    function count_label(int $count, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if ($locale === 'ru') {
            $mod10 = $count % 10;
            $mod100 = $count % 100;

            $word = match (true) {
                $mod10 === 1 && $mod100 !== 11 => 'оценка',
                $mod10 >= 2 && $mod10 <= 4 && ($mod100 < 12 || $mod100 > 14) => 'оценки',
                default => 'оценок',
            };

            return $count.' '.$word;
        }

        return $count.' '.($count === 1 ? 'rating' : 'ratings');
    }
}

if (! function_exists('rating_label')) {
    function rating_label(float $average, int $count): string
    {
        return number_format($average, 1, app()->getLocale() === 'ru' ? ',' : '.', '')
            .' · '.count_label($count);
    }
}

if (! function_exists('years_word')) {
    /**
     * Russian plural form for "year" (3 года, 5 лет, 21 год).
     */
    function years_word(int $years, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if ($locale === 'ru') {
            $mod10 = $years % 10;
            $mod100 = $years % 100;

            return match (true) {
                $mod10 === 1 && $mod100 !== 11 => 'год',
                $mod10 >= 2 && $mod10 <= 4 && ($mod100 < 12 || $mod100 > 14) => 'года',
                default => 'лет',
            };
        }

        return $years === 1 ? 'year' : 'years';
    }
}

if (! function_exists('years_phrase')) {
    function years_phrase(int $years, ?string $locale = null): string
    {
        return $years.' '.years_word($years, $locale);
    }
}
