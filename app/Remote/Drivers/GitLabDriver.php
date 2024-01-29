<?php

namespace App\Remote\Drivers;

use App\Data\AuthorData;
use App\Data\RemoteData;
use App\Remote\Helpers;
use Exception;
use GrahamCampbell\GitLab\GitLabManager;
use Spatie\Url\Url;

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
     */
    public function getData(?Url $url = null): ?RemoteData
    {
        if (! $url && $this->repository) {
            $url = $this->repository->url;
        }

        if ($url) {
            [$username, $repository_name] = Helpers::getRepositoryUserAndName($url);
            $repoData = $this->getClient()->projects()->show($username.'/'.$repository_name);
            if (isset($repoData['id'])) {
                $repoData['title'] = $repoData['name'];
                $releases = $this->getClient()->tags()->all($repoData['id']);
                if (! empty($releases)) {
                    $repoData['version'] = $releases[0]['name'];
                    $repoData['version_published_at'] = $releases[0]['commit']['created_at'];
                }
            }

            return RemoteData::fromGitLab($repoData);
        }

        return null;
    }

    public function getAuthorData(?Url $url = null): ?AuthorData
    {
        if (! $url && $this->author && $this->author->remote_id != '') {
            if ($this->author->type == 'user') {
                $userData = $this->getClient()->users()->show($this->author->remote_id);

                return AuthorData::fromGitLabUser($userData);
            } else {
                $userData = $this->getClient()->groups()->show($this->author->remote_id);

                return AuthorData::fromGitLabGroup($userData);
            }
        } elseif ($url || ($this->repository && $this->repository->url)) {
            if (! $url) {
                $url = $this->repository->url;
            }
            [$username, $repository_name] = Helpers::getRepositoryUserAndName($url);
            $repoData = $this->getClient()->projects()->show($username.'/'.$repository_name);
            $ownerType = $repoData['namespace']['kind'] ?? null;
            if ($ownerType) {
                if ($ownerType == 'user') {
                    $userData = $this->getClient()->users()->all(['username' => $username]);
                    if (isset($userData[0]) && isset($userData[0]['id'])) {
                        $userId = $userData[0]['id'];
                        $userData = $this->getClient()->users()->show($userId);
                        if (isset($userData['name']) && isset($userData['id'])) {
                            return AuthorData::fromGitLabUser($userData);
                        }
                    }
                } else {
                    $userData = $this->getClient()->groups()->show($username);
                    if (isset($userData['name']) && isset($userData['id'])) {
                        return AuthorData::fromGitLabGroup($userData);
                    }
                }
            } else {
                throw new Exception('Unable to determine author type.');
            }
        }
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
