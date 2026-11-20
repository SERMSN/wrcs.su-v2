<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RatingController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
Route::post('/projects/{slug}/rate', RatingController::class)
    ->middleware('throttle:'.config('portfolio.ratings.throttle').',1')
    ->name('projects.rate');
Route::get('/lang/{locale}', LocaleController::class)->name('locale');
