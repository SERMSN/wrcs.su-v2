<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Portfolio data (the "database")
    |--------------------------------------------------------------------------
    |
    | Everything the site shows lives in JSON files inside this directory.
    | Edit profile.json to change the person, projects.json for the cases.
    |
    */
    'path' => env('PORTFOLIO_DATA_PATH', base_path('data')),

    'files' => [
        'profile' => 'profile.json',
        'projects' => 'projects.json',
        'ratings' => 'ratings.json',
    ],

    'locales' => ['ru', 'en'],

    'brand' => env('PORTFOLIO_BRAND', 'wrcs.ops'),

    'ratings' => [
        'min' => 1,
        'max' => 5,
        'throttle' => 30,
        'voter_secret' => env('PORTFOLIO_VOTER_SECRET', env('APP_KEY', 'wrcs-ops')),
    ],

    'filters' => [
        /* Order of the portfolio filter chips; anything else is appended by frequency. */
        'stack_priority' => ['Kubernetes', 'Terraform', 'Argo CD', 'Prometheus', 'AWS', 'GitLab CI', 'Backstage'],
    ],

    'heatmap' => [
        'weeks' => 26,
        'seed' => 2026,
    ],
];
