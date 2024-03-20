<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class TermsSettings extends Settings
{
    public string $terms;

    public static function group(): string
    {
        return 'general';
    }
}
