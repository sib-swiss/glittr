<?php

return [
    /**
     * List of emails to send notifcations (new submissions)
     */
    'notification_emails' => [
        'test@test.com',
    ],

    /**
     * List of emails to send cron repositories update notifications
     */
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
     * Split tags filter display in two cols
     */
    'split_tags_filter' => true,

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