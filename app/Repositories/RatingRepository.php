<?php

namespace App\Repositories;

use App\Support\JsonFile;

final class RatingRepository
{
    private const STARS = 5;

    public function __construct(private readonly ?string $path = null) {}

    /**
     * Aggregate rating for a project: seeded votes from the JSON file plus live votes.
     *
     * @param  array<int, int>  $seed  histogram: [5★, 4★, 3★, 2★, 1★]
     * @return array{average: float, count: int, sum: int, histogram: array<int, int>, percent: array<int, int>}
     */
    public function stats(string $slug, array $seed = []): array
    {
        $histogram = $this->histogram($slug, $seed);
        $count = array_sum($histogram);
        $sum = 0;

        foreach ($histogram as $index => $votes) {
            $sum += (self::STARS - (int) $index) * $votes;
        }

        return [
            'average' => $count > 0 ? round($sum / $count, 2) : 0.0,
            'count' => $count,
            'sum' => $sum,
            'histogram' => $histogram,
            'percent' => array_map(
                fn (int $votes) => $count > 0 ? (int) round($votes / $count * 100) : 0,
                $histogram,
            ),
        ];
    }

    /**
     * @param  array<int, int>  $seed
     * @return array{average: float, count: int, sum: int, histogram: array<int, int>, percent: array<int, int>}
     */
    public function addVote(string $slug, int $value, string $voterHash, array $seed = []): array
    {
        $value = max(1, min(self::STARS, $value));

        $data = JsonFile::update($this->file(), function (array $data) use ($slug, $value, $voterHash): array {
            $votes = $data['votes'] ?? [];
            $votes[$slug][$voterHash] = $value;

            return [
                'version' => (int) ($data['version'] ?? 1),
                'votes' => $votes,
            ];
        });

        unset($data);

        return $this->stats($slug, $seed);
    }

    public function userVote(string $slug, string $voterHash): ?int
    {
        $value = $this->votes()[$slug][$voterHash] ?? null;

        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * @return array<int, int>
     */
    private function histogram(string $slug, array $seed): array
    {
        $seed = array_values($seed);
        $histogram = [];

        for ($index = 0; $index < self::STARS; $index++) {
            $histogram[$index] = max(0, (int) ($seed[$index] ?? 0));
        }

        foreach ($this->votes()[$slug] ?? [] as $value) {
            $value = (int) $value;

            if ($value >= 1 && $value <= self::STARS) {
                $histogram[self::STARS - $value]++;
            }
        }

        return $histogram;
    }

    /**
     * @return array<string, array<string, int>>
     */
    private function votes(): array
    {
        $file = $this->file();

        if (! is_file($file)) {
            return [];
        }

        return JsonFile::read($file)['votes'] ?? [];
    }

    private function file(): string
    {
        return $this->path ?? rtrim((string) config('portfolio.path'), '/').'/'.config('portfolio.files.ratings');
    }
}
