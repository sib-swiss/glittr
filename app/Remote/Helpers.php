<?php

namespace App\Remote;

use Spatie\Url\Url;

class Helpers
{
    /**
     * Extract username and repository name from repository url
     *
     * @return array [?string author, ?string repository_name]
     */
    public static function getRepositoryUserAndName(Url $url)
    {
        // general /username/repository-name structure
        return [$url->getSegment(1), $url->getSegment(2)];
    }
}
