<?php

namespace App\Remote\Contracts;

use App\Data\AuthorData;
use App\Data\RemoteData;

interface DriverContract
{
    public function getData(): ?RemoteData;

    public function getAuthorData(): ?AuthorData;
}
