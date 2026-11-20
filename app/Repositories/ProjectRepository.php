<?php

namespace App\Repositories;

use App\Support\JsonFile;

final class ProjectRepository
{
    public function __construct(private readonly ?string $path = null) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return JsonFile::read($this->file())['projects'] ?? [];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $slug): ?array
    {
        foreach ($this->all() as $project) {
            if (($project['slug'] ?? null) === $slug) {
                return $project;
            }
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function featured(int $limit = 3): array
    {
        return array_slice(
            array_values(array_filter($this->all(), fn (array $project) => (bool) ($project['featured'] ?? false))),
            0,
            $limit,
        );
    }

    /**
     * Stack tags ordered by how often they appear, ready for filter chips.
     *
     * @return array<int, array{name: string, slug: string, count: int}>
     */
    public function stacks(int $limit = 8): array
    {
        $counts = [];

        foreach ($this->all() as $project) {
            foreach (($project['stack'] ?? []) as $stack) {
                $name = (string) $stack;
                $slug = slugify_stack($name);

                if (! isset($counts[$slug])) {
                    $counts[$slug] = ['name' => $name, 'slug' => $slug, 'count' => 0];
                }

                $counts[$slug]['count']++;
            }
        }

        $priority = array_flip(array_map('strval', (array) config('portfolio.filters.stack_priority', [])));

        uasort($counts, function (array $a, array $b) use ($priority): int {
            return [$priority[$a['name']] ?? 999, -$b['count'], $a['name']]
                <=> [$priority[$b['name']] ?? 999, -$a['count'], $b['name']];
        });

        return array_slice(array_values($counts), 0, $limit);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function related(string $slug, int $limit = 3): array
    {
        $current = $this->find($slug);

        if ($current === null) {
            return [];
        }

        $stack = array_map('slugify_stack', $current['stack'] ?? []);

        $scored = [];

        foreach ($this->all() as $project) {
            if (($project['slug'] ?? null) === $slug) {
                continue;
            }

            $overlap = count(array_intersect($stack, array_map('slugify_stack', $project['stack'] ?? [])));

            if ($overlap > 0) {
                $scored[] = ['score' => $overlap, 'project' => $project];
            }
        }

        usort($scored, fn (array $a, array $b) => $b['score'] <=> $a['score']);

        $related = array_map(fn (array $item) => $item['project'], $scored);

        if (count($related) < $limit) {
            $excluded = array_merge([$slug], array_column($related, 'slug'));

            foreach ($this->all() as $project) {
                if (count($related) >= $limit) {
                    break;
                }

                if (! in_array($project['slug'] ?? null, $excluded, true)) {
                    $related[] = $project;
                    $excluded[] = $project['slug'] ?? null;
                }
            }
        }

        return array_slice($related, 0, $limit);
    }

    private function file(): string
    {
        return $this->path ?? rtrim((string) config('portfolio.path'), '/').'/'.config('portfolio.files.projects');
    }
}
