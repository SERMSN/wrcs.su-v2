<?php

namespace App\Http\Controllers;

use App\Repositories\ProjectRepository;
use App\Repositories\RatingRepository;
use App\Support\ProjectPresenter;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProjectController extends Controller
{
    public function show(
        Request $request,
        string $slug,
        ProjectRepository $projectRepository,
        RatingRepository $ratingRepository,
    ): View {
        $project = $projectRepository->find($slug);

        abort_if($project === null, 404);

        $presenter = ProjectPresenter::forRequest($request, $ratingRepository);
        $project = $presenter->present($project);

        return view('project', [
            'project' => $project,
            'related' => $presenter->presentMany($projectRepository->related($slug, 3)),
            'meta' => [
                'title' => loc($project['title']).' — '.config('portfolio.brand'),
                'description' => loc($project['summary']),
                'image' => $project['cover'] ?? '/assets/img/og-cover.svg',
            ],
        ]);
    }
}
