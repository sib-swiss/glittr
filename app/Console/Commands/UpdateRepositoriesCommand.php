<?php

namespace App\Console\Commands;

use App\Actions\Repository\AttachAuthorAction;
use App\Actions\Repository\UpdateRemoteAction;
use App\Facades\Remote;
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
    public function handle(UpdateRemoteAction $updateRemoteAction, AttachAuthorAction $attachAuthorAction)
    {
        $this->comment('Start updating repositories data.');

        if ($this->hasArgument('repository') && $this->argument('repository')) {
            $repository = Repository::find($this->argument('repository'));
            if ($repository) {
                $this->comment("Updating repository {$repository->url}");
                $this->updateRepository($repository, $updateRemoteAction, $attachAuthorAction);
            } else {
                $this->error("Repository not found {$this->argument('repository')}");
            }
        } else {
            $updated = 0;
            Repository::chunk(100, function ($repositories) use ($updateRemoteAction, $attachAuthorAction) {
                foreach ($repositories as $repository) {
                    $this->comment("Updating repository {$repository->url}");
                    $this->updateRepository($repository, $updateRemoteAction, $attachAuthorAction);
                }
            });

            $this->comment("Finished procesing repositories ({$updated} updated).");
        }

        return 0;
    }

    protected function updateRepository(Repository $repository, UpdateRemoteAction $updateRemoteAction, AttachAuthorAction $attachAuthorAction)
    {
        if ($repository->api) {
            $data = Remote::for($repository)->getData();
            if ($data) {
                $updateRemoteAction->execute($repository, $data);
            }
            // Attach author if not linked.
            if (! $repository->author) {
                $authorData = Remote::for($repository)->getAuthorData();
                if ($authorData) {
                    $attachAuthorAction->execute($repository, $authorData);
                }
            }
        }
    }
}
