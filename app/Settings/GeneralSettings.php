<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    public string $site_description;

    public string $homepage_page_title;

    public string $header_text;

    public string $about_text;

    public string $contribute_text;

    public string $footer_text;

    public bool $show_repository_link;

    public string $repository_link;

    public string $mail_signature;

    public static function group(): string
    {
        return 'general';
    }
}
