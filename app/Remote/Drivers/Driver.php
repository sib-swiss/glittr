<?php

namespace App\Remote\Drivers;

use App\Models\Author;
use App\Models\Repository;
use App\Remote\Contracts\DriverContract;

abstract class Driver implements DriverContract
{
    /**
     * Client class to make the call
     *
     * @var mixed
     */
    protected $client;

    /**
     * API extra config from remote config info
     *
     * @var array
     */
    protected $config;

    /**
     * Current repository to process
     *
     * @var Repository
     */
    protected $repository;

    /**
     * Current author to process
     *
     * @var Author
     */
    protected $author;

    public function __construct($client = null, $config = null)
    {
        $this->client = $client;
        $this->config = $config;
    }

    public function setRepository(Repository $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    public function setAuthor(Author $author): self
    {
        $this->author = $author;

        return $this;
    }
}
