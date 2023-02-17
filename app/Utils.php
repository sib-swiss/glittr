<?php

namespace App;

use Illuminate\Support\Str;

class Utils
{
    /**
     * Ensure url start with https or http
     */
    public static function ensureUrl(string $url): string
    {
        if ($url != '') {
            if (! Str::startsWith($url, 'https://') && ! Str::startsWith($url, 'http://')) {
                return 'https://'.$url;
            }
        }

        return $url;
    }
}
