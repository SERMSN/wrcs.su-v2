<?php

return [
    'skip' => 'Skip to content',
    'menu' => 'Menu',
    'theme' => 'Toggle theme',
    'lang' => 'Language',
    'logo_home' => 'Back to home',

    'nav' => [
        'about' => 'About',
        'work' => 'Work',
        'stack' => 'Stack',
        'contact' => 'Contact',
    ],

    'hero' => [
        'eyebrow' => 'platform engineering',
        'cta_work' => 'See the work',
        'cta_contact' => "Let's talk",
        'terminal_whoami' => 'whoami',
        'terminal_kubectl' => 'kubectl get profile -o wide',
        'terminal_cat' => 'cat focus.txt',
        'terminal_role' => 'lead sre',
        'terminal_focus' => 'kubernetes · terraform · slo · finops',
        'availability' => 'Open to offers',
    ],

    'about' => [
        'eyebrow' => '// 01 — profile',
        'title' => 'Infrastructure nobody notices',
        'lead' => 'Three years turning manual deploys into boring automation — my favourite metric.',
        'facts' => [
            'location' => 'Location',
            'timezone' => 'Timezone',
            'experience' => 'Experience',
            'certs' => 'Certifications',
        ],
        'years_suffix' => 'in infra',
        'principles' => 'Principles',
        'timeline' => 'Experience',
        'now' => 'now',
    ],

    'pipeline' => [
        'eyebrow' => '// 02 — process',
        'title' => 'From commit to production',
        'lead' => 'One path for every service. No manual windows, no "let\'s skip deploys today".',
        'steps' => [
            'plan' => ['title' => 'Plan', 'tool' => 'RFC · SLO · budget'],
            'code' => ['title' => 'Code', 'tool' => 'trunk-based · review'],
            'build' => ['title' => 'Build', 'tool' => 'Docker · layer cache'],
            'test' => ['title' => 'Test', 'tool' => 'unit · e2e · load'],
            'release' => ['title' => 'Release', 'tool' => 'semver · changelog'],
            'deploy' => ['title' => 'Deploy', 'tool' => 'Argo CD · canary'],
            'operate' => ['title' => 'Operate', 'tool' => 'SLO · alerts'],
        ],
    ],

    'skills' => [
        'eyebrow' => '// 03 — stack',
        'title' => 'Tools and depth',
        'lead' => 'Not everything I have touched, but what I build with every day.',
        'tiers' => [
            'expert' => 'Expert',
            'advanced' => 'Advanced',
            'solid' => 'Solid',
        ],
        'donut_title' => 'Where time goes',
        'donut_note' => 'Average over the last 12 months',
        'donut_center' => 'in infra',
    ],

    'work' => [
        'eyebrow' => '// 04 — portfolio',
        'title' => 'Selected work',
        'lead' => 'Nine projects where numbers beat adjectives. Ratings come from visitors — no moderation, no inflation.',
        'all' => 'All',
        'sort' => 'Sort',
        'sort_year' => 'By year',
        'sort_rating' => 'By rating',
        'min_rating' => '4.5+ ★',
        'found' => ':count found',
        'empty' => 'Nothing matches these filters.',
        'reset' => 'Reset',
        'featured' => 'featured',
        'open' => 'Open case',
    ],

    'activity' => [
        'eyebrow' => '// 05 — activity',
        'title' => 'Deploy rhythm',
        'lead' => 'Half a year of shipping: one cell per day, colour shows the number of deploys.',
        'less' => 'less',
        'more' => 'more',
        'total' => 'deploys in 26 weeks',
        'per_day' => 'deploys per day on average',
        'peak' => 'deploys in the best week',
        'note' => '26 weeks straight · zero manual windows',
    ],

    'contact' => [
        'eyebrow' => '// 06 — contact',
        'title' => 'Need a platform that lets you sleep?',
        'lead' => 'One project per quarter: platform work, a Kubernetes migration or cleaning up CI/CD.',
        'cta' => 'Start a conversation',
        'copy' => 'Copy',
        'copied' => 'Copied',
        'availability' => 'Open to offers · I reply within a day',
    ],

    'footer' => [
        'note' => 'A DevOps engineer\'s personal site. Data lives in JSON files, no database.',
        'built' => 'Laravel 13 · JSON instead of a DB',
        'top' => 'Back to top',
        'rights' => '© :year :brand',
    ],

    'rating' => [
        'aria' => ':rating out of 5',
        'your' => 'Your rating',
        'hint' => 'Click a star — your rating is saved instantly',
        'your_value' => 'You rated :value out of 5',
        'thanks' => 'Thanks! Your rating is counted.',
        'error' => 'Could not save the rating. Please try again.',
        'title' => 'Case rating',
        'average' => 'Average rating',
        'distribution' => 'Distribution',
        'saving' => 'Saving…',
    ],

    'project' => [
        'back' => 'All cases',
        'client' => 'Client',
        'year' => 'Year',
        'role' => 'Role',
        'stack' => 'Stack',
        'metrics' => 'Outcome',
        'about' => 'Problem and approach',
        'related' => 'Related cases',
        'discuss' => 'Discuss a similar project',
        'rate' => 'Rate this case',
    ],

    'notfound' => [
        'title' => '404 — route not found',
        'lead' => 'This page does not exist. Maybe it was deployed to another branch.',
        'cta' => 'Back to home',
    ],
];
