<?php

namespace App\Repository\Drivers;

use App\Repository\Contracts\DriverContract;;

abstract class Driver implements DriverContract
{
    protected $client;

    protected $settings;

    public function __construct($client = null, $settings = null)
    {
        $this->client = $client;
        $this->settings = $settings;
    }
}
