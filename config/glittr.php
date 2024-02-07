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

    /**
     * Tags filtering default operator
     */
    'tags_default_and_operator' => true,

    /**
     * Email signature
     */
    'mail_signature' => 'Best wishes
The Glittr team',

    /**
     * List of licences urls
     */
    'licences_url' => [
        'agpl-3.0' => 'https://spdx.org/licenses/AGPL-3.0-only.html',
        'apache-2.0' => 'https://spdx.org/licenses/Apache-2.0.html',
        'artistic-2.0' => 'https://spdx.org/licenses/Artistic-2.0.html',
        'bsd-2-clause' => 'https://spdx.org/licenses/BSD-2-Clause.html',
        'bsd-3-clause' => 'https://spdx.org/licenses/BSD-3-Clause.html',
        'bsl-1.0' => 'https://spdx.org/licenses/BSL-1.0.html',
        'cc-by-4.0' => 'https://spdx.org/licenses/CC-BY-4.0.html',
        'cc-by-sa-4.0' => 'https://spdx.org/licenses/CC-BY-SA-4.0.html',
        'cc0-1.0' => 'https://spdx.org/licenses/CC0-1.0.html',
        'epl-2.0' => 'https://spdx.org/licenses/EPL-2.0.html',
        'gpl-2.0' => 'https://spdx.org/licenses/GPL-2.0-only.html',
        'gpl-3.0' => 'https://spdx.org/licenses/GPL-3.0-only.html',
        'lgpl-2.1' => 'https://spdx.org/licenses/LGPL-2.1-only.html',
        'mit' => 'https://spdx.org/licenses/MIT.html',
        'mpl-2.0' => 'https://spdx.org/licenses/MPL-2.0.html',
        'unlicense' => 'https://spdx.org/licenses/Unlicense.html',
        'wtfpl' => 'https://spdx.org/licenses/WTFPL.html',
    ],
];
