<?php

namespace App\Console\Commands;

use App\Actions\Repository\AttachAuthorAction;
use App\Actions\Repository\UpdateRemoteAction;
use App\Data\RemoteData;
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
    protected $signature = 'repo:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update repositories with remote data';

    /**
     * Execute the console command.
     *
     * @param UpdateRepositoryDataAction $updateRepositoryDataAction
     * @param AttachAuthorAction $attachAuthorAction
     * @return int
     */
    public function handle(UpdateRemoteAction $updateRemoteAction, AttachAuthorAction $attachAuthorAction)
    {
        $this->comment('Start updating repositories data.');

        $updated = 0;
        Repository::chunk(100, function ($repositories) use ($updated, $updateRemoteAction, $attachAuthorAction) {
            foreach ($repositories as $repository) {
                $data = Remote::for($repository)->getData();
                if ($data) {
                    $updateRemoteAction->execute($repository, $data);
                }
                if (!$repository->author) {
                    $authorData = Remote::for($repository)->getAuthorData();
                    if ($authorData){
                        $attachAuthorAction->execute($repository, $authorData);
                    }
                }
            }
        });

        $this->comment("Finished procesing repositories ({$updated} updated).");
        return 0;
    }
}
