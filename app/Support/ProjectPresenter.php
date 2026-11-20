<?php

namespace App\Support;

use App\Repositories\RatingRepository;
use Illuminate\Http\Request;

final class ProjectPresenter
{
    public function __construct(
        private readonly RatingRepository $ratings,
        private readonly string $voterHash,
    ) {}

    public static function forRequest(Request $request, RatingRepository $ratings): self
    {
        return new self($ratings, Voter::hash($request));
    }

    /**
     * @param  array<string, mixed>  $project
     * @return array<string, mixed>
     */
    public function present(array $project): array
    {
        $slug = (string) ($project['slug'] ?? '');

        $project['rating'] = $this->ratings->stats($slug, $project['rating']['histogram'] ?? []);
        $project['user_vote'] = $this->ratings->userVote($slug, $this->voterHash);
        $project['url'] = route('projects.show', $slug);
        $project['stack_slugs'] = array_values(array_map(
            fn (string $stack) => slugify_stack($stack),
            $project['stack'] ?? [],
        ));

        return $project;
    }

    /**
     * @param  array<int, array<string, mixed>>  $projects
     * @return array<int, array<string, mixed>>
     */
    public function presentMany(array $projects): array
    {
        return array_map(fn (array $project) => $this->present($project), $projects);
    }
}
