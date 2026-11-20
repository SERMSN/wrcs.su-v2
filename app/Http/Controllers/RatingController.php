<?php

namespace App\Http\Controllers;

use App\Repositories\ProjectRepository;
use App\Repositories\RatingRepository;
use App\Support\Voter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class RatingController extends Controller
{
    public function __invoke(
        Request $request,
        string $slug,
        ProjectRepository $projectRepository,
        RatingRepository $ratingRepository,
    ): JsonResponse|RedirectResponse {
        $project = $projectRepository->find($slug);

        abort_if($project === null, 404);

        $validated = $request->validate([
            'value' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $stats = $ratingRepository->addVote(
            $slug,
            (int) $validated['value'],
            Voter::hash($request),
            $project['rating']['histogram'] ?? [],
        );

        $request->session()->put("rated.{$slug}", (int) $validated['value']);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'slug' => $slug,
                'value' => (int) $validated['value'],
                'stats' => $stats,
                'count_label' => count_label($stats['count']),
                'average_label' => number_format($stats['average'], 1, app()->getLocale() === 'ru' ? ',' : '.', ''),
                'message' => __('ui.rating.thanks'),
            ]);
        }

        return back()->with('status', __('ui.rating.thanks'));
    }
}
