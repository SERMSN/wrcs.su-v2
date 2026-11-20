<?php

namespace App\Support;

use Carbon\CarbonImmutable;

/**
 * Deterministic contribution-style heatmap (no database, no real events).
 * The seed keeps the picture stable between requests.
 */
final class Heatmap
{
    /**
     * @return array{weeks: array<int, array{cells: array<int, array{level: int, count: int, label: string}>, month: string|null}>, total: int, average: float, peak: int}
     */
    public static function make(int $weeks = 26, int $seed = 2026): array
    {
        $weeks = max(4, $weeks);
        $state = $seed;
        $start = CarbonImmutable::now()->startOfWeek()->subWeeks($weeks - 1);
        $total = 0;
        $result = [];
        $weekTotals = [];
        $lastMonth = null;

        for ($week = 0; $week < $weeks; $week++) {
            $cells = [];
            $monthLabel = null;
            $weekTotal = 0;

            for ($day = 0; $day < 7; $day++) {
                $date = $start->addWeeks($week)->addDays($day);
                $state = (int) ((1103515245 * $state + 12345) % 2147483648);
                $roll = $state / 2147483648;

                $level = match (true) {
                    $roll < 0.16 => 0,
                    $roll < 0.42 => 1,
                    $roll < 0.68 => 2,
                    $roll < 0.87 => 3,
                    default => 4,
                };

                $count = [0, 2, 4, 7, 11][$level] + ($level > 0 ? (int) round($roll * 3) : 0);
                $total += $count;
                $weekTotal += $count;

                $cells[] = [
                    'level' => $level,
                    'count' => $count,
                    'label' => $date->translatedFormat('j M').' · '.(app()->getLocale() === 'ru'
                        ? self::plural($count, 'деплой', 'деплоя', 'деплоев')
                        : self::plural($count, 'deploy', 'deploys', 'deploys')),
                ];

                if ($day === 0 && $monthLabel === null && $date->month !== $lastMonth) {
                    $monthLabel = $date->translatedFormat('M');
                    $lastMonth = $date->month;
                }
            }

            $weekTotals[] = $weekTotal;
            $result[] = ['cells' => $cells, 'month' => $monthLabel];
        }

        return [
            'weeks' => $result,
            'total' => $total,
            'average' => round($total / ($weeks * 7), 1),
            'peak' => max($weekTotals) ?: 0,
        ];
    }

    private static function plural(int $count, string $one, string $few, string $many): string
    {
        $mod10 = $count % 10;
        $mod100 = $count % 100;

        $word = match (true) {
            $mod10 === 1 && $mod100 !== 11 => $one,
            $mod10 >= 2 && $mod10 <= 4 && ($mod100 < 12 || $mod100 > 14) => $few,
            default => $many,
        };

        return $count.' '.$word;
    }
}
