<?php

namespace App\Facades;

use App\Remote\RemoteManager;
use Illuminate\Support\Facades\Facade;

class Remote extends Facade
{
    protected static function getFacadeAccessor()
    {
        return RemoteManager::class;
    }
}
