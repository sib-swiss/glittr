<?php

namespace App\Facades;

use App\Manager\RepositoryManager;
use Illuminate\Support\Facades\Facade;

class Repository extends Facade
{
    protected static function getFacadeAccessor()
    {
        return RepositoryManager::class;
    }
}
