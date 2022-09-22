<?php

namespace App\Remote\Drivers;

use App\Data\AuthorData;
use App\Data\RemoteData;
use App\Remote\Helpers;
use Exception;
use GrahamCampbell\GitHub\GitHubManager;

class GithubDriver extends Driver
{

    protected $connection;

    /**
     * Undocumented function
     *
     * @param GitHubManager $client
     * @param array $config
     */
    public function __construct($client = null, $config = null)
    {
        parent::__construct($client, $config);

        $this->connection = $config['connection'] ?? 'main';
    }

    /**
     * Retrieve and transmit to data
     *
     * @return RemoteData|null
     */
    public function getData(): ?RemoteData
    {

        if ($this->repository && $this->repository->url) {
            list ($username, $repository_name) = Helpers::getRepositoryUserAndName($this->repository->url);
            $repoData = $this->getClient()->repo()->show($username, $repository_name);
            return RemoteData::fromGithub($repoData);
        }

        return null;

    }

    public function getAuthorData(): ?AuthorData
    {
        if ($this->repository && $this->repository->url) {
            list($username, $repository_name) = Helpers::getRepositoryUserAndName($this->repository->url);
            $userData = $this->getClient()->user()->show($username);

            if (isset($userData['name']) && isset($userData['login'])) {
                return AuthorData::fromGithub($userData);
            }
        }
        return null;
    }

    /**
     * Ensure the client always use the configured connection
     *
     * @return GitHubManager
     */
    protected function getClient()
    {

        return $this->connection ? $this->client->connection($this->connection) : $this->client;
    }
}
