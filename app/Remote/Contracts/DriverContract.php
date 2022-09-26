<?php

namespace App\Remote\Contracts;

use App\Data\AuthorData;
use App\Data\RemoteData;
use Spatie\Url\Url;

interface DriverContract
{
    public function getData(?Url $url = null): ?RemoteData;

    public function getAuthorData(?Url $url = null): ?AuthorData;
}
