<?php

namespace App\Console\Commands;

use App\Actions\Repository\AttachAuthorAction;
use App\Jobs\UpdateRepositoryData;
use App\Models\Repository;
use Illuminate\Console\Command;

class UpdateRepositoriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repo:update {repository?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update repositories with remote data';

    /**
     * Execute the console command.
     *
     * @param  UpdateRepositoryDataAction  $updateRepositoryDataAction
     * @param  AttachAuthorAction  $attachAuthorAction
     * @return int
     */
    public function handle()
    {
        $this->comment('Start updating repositories job queuing.');

        if ($this->hasArgument('repository') && $this->argument('repository')) {
            $repository = Repository::find($this->argument('repository'));
            if ($repository) {
                $this->comment("Updating repository {$repository->url}");
                UpdateRepositoryData::dispatch($repository);
            } else {
                $this->error("Repository not found {$this->argument('repository')}");
            }
        } else {
            Repository::chunk(100, function ($repositories) {
                foreach ($repositories as $repository) {
                    $this->comment("Updating repository {$repository->url}");
                    UpdateRepositoryData::dispatch($repository);
                }
            });

            $this->comment('Finished queuing jobs.');
        }

        return 0;
    }
}
