<?php

return [
    /**
     * List of emails to send notifcations (new submissions)
     */
    'notification_emails' => [
        'test@test.com',
    ],

    //TODO: status reporting for CRON update
    'support_emails' => [
        'yann.haefliger@sib.swiss',
    ],

    /**
     * Current app code repository for footer link
     */
    'repository_link' => 'https://gitlab.sib.swiss/yhaeflig/training-collection-app',

    /**
     * Max number of tags displayed in table (with show more button)
     */
    'max_tags' => 5,

    /**
     * Number of columns for topics filtering panel (2 or 1, 3 will mostly break the layout...)
     */
    'topics_filter_columns' => 2,

    /**
     * Repositories fontend list configuration
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

];
