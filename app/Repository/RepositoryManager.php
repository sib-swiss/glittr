<?php

namespace App\Manager;

use App\Drivers\GithubDriver;
use App\Models\Repository;
use App\Repository\Contracts\DriverContract;
use Illuminate\Support\Manager;
use InvalidArgumentException;
use GrahamCampbell\GitHub\GitHubManager;
use GrahamCampbell\GitLab\GitLabManager;

class RepositoryManager extends Manager
{

    public function for(Repository $repository)
    {
    }

    public function createGithubDriver(): DriverContract
    {
        return new GithubDriver($this->container->make(GithubManager::class));
    }

    public function createGitlabDriver(): DriverContract
    {
        return new GitlabDriver($this->container->make(GitLabManager::class))
    }

    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No driver was specified.');
    }
}
