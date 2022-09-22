<?php

namespace App\Remote\Drivers;

use App\Data\AuthorData;
use App\Data\RemoteData;

class GitLabDriver extends Driver
{
    public function getData(): ?RemoteData
    {
        return RemoteData::from([]);
    }

    public function getAuthorData(): ?AuthorData
    {
        return null;
    }
}
