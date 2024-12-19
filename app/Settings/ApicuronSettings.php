<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ApicuronSettings extends Settings
{
    public bool $apicuron_enabled;

    public string $apicuron_submission_activity_term;

    public string $apicuron_title;

    public string $apicuron_introduction;

    public string $apicuron_login_btn;

    public string $apicuron_logged_warning;

    public string $apicuron_logout_btn;

    public static function group(): string
    {
        return 'general';
    }
}
