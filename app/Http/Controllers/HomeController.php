<?php

namespace App\Http\Controllers;

use App\Repositories\ProfileRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\RatingRepository;
use App\Support\Heatmap;
use App\Support\ProjectPresenter;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __invoke(
        Request $request,
        ProfileRepository $profileRepository,
        ProjectRepository $projectRepository,
        RatingRepository $ratingRepository,
    ): View {
        $presenter = ProjectPresenter::forRequest($request, $ratingRepository);
        $profile = $profileRepository->get();

        $projects = $presenter->presentMany($projectRepository->all());
        usort($projects, fn (array $a, array $b) => [$b['year'], $b['rating']['average']] <=> [$a['year'], $a['rating']['average']]);

        return view('home', [
            'profile' => $profile,
            'projects' => $projects,
            'stacks' => $projectRepository->stacks(7),
            'heatmap' => Heatmap::make(
                (int) config('portfolio.heatmap.weeks'),
                (int) config('portfolio.heatmap.seed'),
            ),
            'experienceYears' => $profileRepository->experienceYears(),
            'meta' => [
                'title' => loc($profile['identity']['name'] ?? config('portfolio.brand')).' — '.loc($profile['identity']['role'] ?? ''),
                'description' => loc($profile['identity']['tagline'] ?? ''),
                'image' => '/assets/img/og-cover.svg',
            ],
        ]);
    }
}
