<?php

return [
    /**
     * List of emails to send notifcations (new submissions)
     */
    'notification_emails' => explode(',', env('GLITTR_NOTIFICATION_EMAILS', '')),

    /**
     * List of emails to send cron repositories update notifications
     */
    'support_emails' => explode(',', env('GLITTR_SUPPORT_EMAILS', '')),

    /**
     * Current app code repository for footer link
     */
    'repository_link' => 'https://github.com/sib-swiss/glittr',

    /**
     * Max number of tags displayed in table (with show more button)
     */
    'max_tags' => 5,

    /**
     * Split tags filter display in two cols
     */
    'split_tags_filter' => true,

    /**
     * Repositories frontend list configuration
     */
    'default_sort_by' => 'stargazers',
    'default_sort_direction' => 'desc',
    'default_per_page' => 25,
    'paginations' => [
        10,
        25,
        50,
        100,
    ],

    /**
     * Categories colors for import
     */
    'colors' => [
        '#FF006E', // pink
        '#212121', // black
        '#FFBE0B', // yellow
        '#3A86FF', // blue
        '#8338EC', // purple
        '#FB5607', // orange
    ],

    /**
     * SEO tags
     */
    'seo' => [
        'title' => 'Glittr.org | Git repositories with bioinformatics training material',
        'description' => 'Glittr is a curated list of bioinformatics training material.',
    ],

    /**
     * Open Graph tags for social media
     */
    'og' => [
        'title' => 'Glittr.org',
        'description' => 'Git repositories with bioinformatics training material',
    ],

    /**
     * List of developers/maintainers
     */
    'contributors' => [
        [
            'name' => 'Geert van Geest',
            'links' => [
                'twitter' => 'https://twitter.com/geertvangeest',
                'orcid' => 'https://orcid.org/0000-0002-1561-078X',
                'linkedin' => 'https://www.linkedin.com/in/geert-van-geest-47938822/',
            ],
        ],
        [
            'name' => 'Patricia Palagi',
            'links' => [
                'twitter' => 'https://twitter.com/P_Palagi',
                'orcid' => 'https://orcid.org/0000-0001-9062-6303',
                'linkedin' => 'https://www.linkedin.com/in/patriciapalagi/',
            ],
        ],
        [
            'name' => 'Yann Haefliger',
            'links' => [
                'twitter' => 'https://twitter.com/choup',
                'linkedin' => 'https://www.linkedin.com/in/yhaefliger/',
            ],
        ],
    ],

    /**
     * Analytics systems
     */
    'google_analytics' => env('GOOGLE_ANALYTICS', false),
    'matomo' => [
        'url' => env('MATOMO_URL', false),
        'site_id' => env('MATOMO_SITE_ID', false),
    ],
];
