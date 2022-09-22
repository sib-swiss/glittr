<?php

namespace App\Remote\Drivers;

use App\Data\AuthorData;
use App\Data\RemoteData;
use App\Remote\Helpers;
use GrahamCampbell\GitLab\GitLabManager;

class GitLabDriver extends Driver
{
    protected $connection;

    /**
     * Undocumented function
     *
     * @param  GitHubManager  $client
     * @param  array  $config
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
            [$username, $repository_name] = Helpers::getRepositoryUserAndName($this->repository->url);
            $repoData = $this->getClient()->projects()->show($username.'/'.$repository_name);

            return RemoteData::fromGitLab($repoData);
        }

        return null;
    }

    public function getAuthorData(): ?AuthorData
    {
        if ($this->author && $this->author->remote_id != '') {
            $userData = $this->getClient()->users()->show($this->author->remote_id);
        } elseif ($this->repository && $this->repository->url) {
            [$username, $repository_name] = Helpers::getRepositoryUserAndName($this->repository->url);
            $userData = $this->getClient()->users()->all(['username' => $username]);
            if (isset($userData[0]) && isset($userData[0]['id'])) {
                $userId = $userData[0]['id'];
                $userData = $this->getClient()->users()->show($userId);
            }
        }

        if (isset($userData['name']) && isset($userData['id'])) {
            return AuthorData::fromGitLab($userData);
        }

        return null;
    }

    /**
     * Ensure the client always use the configured connection
     *
     * @return GitLabManager
     */
    protected function getClient()
    {
        return $this->connection ? $this->client->connection($this->connection) : $this->client;
    }
}
