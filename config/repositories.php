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

    //TODO: implement configurable default sort and pagination
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
