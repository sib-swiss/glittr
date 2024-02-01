<?php

namespace App\Remote;

use App\Models\Author;
use App\Models\Repository;
use App\Remote\Contracts\DriverContract;
use App\Remote\Drivers\GithubDriver;
use App\Remote\Drivers\GitLabDriver;
use GrahamCampbell\GitHub\GitHubManager;
use GrahamCampbell\GitLab\GitLabManager;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Spatie\Url\Url;

class RemoteManager extends Manager
{
    /**
     * Get Driver instance for given repository or author
     */
    public function for(Repository|Author $model): ?DriverContract
    {
        if ($model->api != '') {
            $driver = $this->driver($model->api);
        }

        if (($model instanceof Repository) && method_exists($driver, 'setRepository')) {
            $driver->setRepository($model);
        } elseif (($model instanceof Author) && method_exists($driver, 'setAuthor')) {
            $driver->setAuthor($model);
        }

        return $driver;
    }

    /**
     * Resolve driver name based on repository url
     */
    public function resolveAPI(?Repository $repository = null, ?string $url = null): ?string
    {
        $apis = $this->getConfiguredAPIs();

        if ($repository) {
            $host = $repository->url->getHost();
        } else {
            $host = Url::fromString($url)->getHost();
        }

        foreach ($apis as $api => $config) {
            if (isset($config['hosts']) && ! empty($config['hosts'])) {
                if (in_array($host, $config['hosts'])) {
                    return $api;
                }
            }
        }

        return null;
    }

    public function createGithubDriver(array $config): DriverContract
    {
        return new GithubDriver($this->container->make(GithubManager::class), $config);
    }

    public function createGitlabDriver(array $config): DriverContract
    {
        return new GitLabDriver($this->container->make(GitLabManager::class), $config);
    }

    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No driver was specified.');
    }

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        // First, we will determine if a custom driver creator exists for the given driver and
        // if it does not we will check for a creator method for the driver. Custom creator
        // callbacks allow developers to build their own "drivers" easily using Closures.
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        } else {
            // Look in our config file for driver configurations
            $apis = $this->getConfiguredAPIs();

            if (isset($apis[$driver]) && isset($apis[$driver]['driver'])) {
                $config = $apis[$driver];
                $driverClass = $config['driver'];
                $method = 'create'.Str::studly($driverClass).'Driver';

                if (method_exists($this, $method)) {
                    return $this->$method($config);
                }
            }
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }

    protected function getConfiguredAPIs()
    {
        return $this->config->get('remote.apis', []);
    }
}
