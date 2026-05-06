<?php

namespace App\Remote\Drivers;

use App\Data\AuthorData;
use App\Data\ContributorData;
use App\Data\RemoteData;
use App\Remote\Helpers;
use Github\HttpClient\Message\ResponseMediator;
use GrahamCampbell\GitHub\GitHubManager;
use Spatie\Url\Url;

class GithubDriver extends Driver
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
            $repoData = $this->getClient()->repo()->show($username, $repository_name);
            try {
                $release = $this->getClient()->repo()->releases()->latest($username, $repository_name);
                if (isset($release['name'])) {
                    $repoData['revision_comments'] = $release['body'];
                    $repoData['version'] = $release['name'];
                    $repoData['version_published_at'] = $release['published_at'];
                }
            } catch (\Exception $e) {
                // No release found
            }

            try {
                $readmeData = $this->getClient()->repo()->contents()->readme($username, $repository_name);
                if (isset($readmeData['content'])) {
                    $repoData['readme'] = base64_decode(str_replace("\n", '', $readmeData['content']));
                }
            } catch (\Exception $e) {
                // No README found
            }

            return RemoteData::fromGithub($repoData);
        }

        return null;
    }

    public function getAuthorData(?Url $url = null): ?AuthorData
    {
        $userData = null;

        if (! $url && $this->author && $this->author->name != '') {
            $userData = $this->getClient()->user()->show($this->author->name);
        } elseif ($url || ($this->repository && $this->repository->url)) {
            if (! $url) {
                $url = $this->repository->url;
            }
            [$username, $repository_name] = Helpers::getRepositoryUserAndName($url);
            $userData = $this->getClient()->user()->show($username);
        }

        if (isset($userData['login'])) {
            return AuthorData::fromGithub($userData);
        }

        return null;
    }

    /**
     * Retrieve all contributors for the repository, handling pagination.
     *
     * @return array<ContributorData>
     */
    public function getContributors(): array
    {
        $contributors = [];
        $page = 1;

        do {
            $pageContributors = $this->getContributorsPage($page);
            $contributors = array_merge($contributors, $pageContributors);
            $page++;
        } while (! empty($pageContributors));

        return $contributors;
    }

    /**
     * Retrieve a single page of contributors.
     *
     * @return array<ContributorData>
     */
    public function getContributorsPage(int $page): array
    {
        [$username, $repositoryName] = Helpers::getRepositoryUserAndName($this->repository->url);

        $path = '/repos/' . rawurlencode($username) . '/' . rawurlencode($repositoryName) . '/contributors?'
            . http_build_query(['per_page' => 100, 'page' => $page], '', '&', PHP_QUERY_RFC3986);

        $response = $this->getClient()->getHttpClient()->get($path);
        $pageData = ResponseMediator::getContent($response);

        if (empty($pageData) || ! is_array($pageData)) {
            return [];
        }
        $contributors = [];

        foreach ($pageData as $item) {
            if (empty($item['id']) || empty($item['login']) || ($item['type'] ?? '') === 'Bot') {
                continue;
            }
            $contributors[] = ContributorData::fromGithub($item);
        }

        return $contributors;
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
