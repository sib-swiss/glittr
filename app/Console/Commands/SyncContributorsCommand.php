<?php

namespace App\Console\Commands;

use App\Jobs\SyncRepositoryContributors;
use App\Models\Repository;
use Illuminate\Console\Command;

class SyncContributorsCommand extends Command
{
    protected $signature = 'repo:sync-contributors {repository?}';

    protected $description = 'Sync contributors for GitHub repositories';

    public function handle(): int
    {
        if ($this->hasArgument('repository') && $this->argument('repository')) {
            $repository = Repository::find($this->argument('repository'));

            if (! $repository) {
                $this->error("Repository not found {$this->argument('repository')}");

                return 1;
            }

            $this->comment("Dispatching contributor sync for {$repository->url}");
            SyncRepositoryContributors::dispatch($repository);
        } else {
            $this->comment('Dispatching contributor sync for all GitHub repositories.');

            Repository::query()
                ->where('api', 'github')
                ->where('enabled', true)
                ->chunk(500, function ($repositories) {
                    foreach ($repositories as $repository) {
                        $this->comment("Dispatching sync for {$repository->url}");
                        SyncRepositoryContributors::dispatch($repository);
                    }
                });
        }

        $this->comment('Done dispatching.');

        return 0;
    }
}
