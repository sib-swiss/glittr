<?php

return [
    /**
     * List of emails to send notifcations (new submissions)
     */
    'notification_emails' => [
        'test@test.com',
    ],

    //TODO: status reporting for CRON update
    'support_emails' => [],

    /**
     * Current app code repository for footer link
     */
    'repository_link' => 'https://gitlab.sib.swiss/yhaeflig/training-collection-app',

    /**
     * Repositories fontend list configuration
     */
    'default_sort_by' => 'stargazers',
    'default_sort_direction' => 'desc',
    'default_per_page' => 20,
    'paginations' => [
        10,
        20,
        50,
        100,
    ],

];
