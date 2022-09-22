<?php

namespace App\Jobs;

use App\Actions\Repository\AttachAuthorAction;
use App\Actions\Repository\UpdateRemoteAction;
use App\Facades\Remote;
use App\Models\Repository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateRepositoryData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The repository to udpate
     *
     * @var Repository
     */
    public $repository;

    /**
     * Create a new job instance.
     *
     * @param  Repository  $repository
     * @return void
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(UpdateRemoteAction $updateRemoteAction, AttachAuthorAction $attachAuthorAction)
    {
        // reload to be sure we get latest data
        $repository = Repository::find($this->repository->id);

        // try to get api if not
        if (! $repository->api) {
            $api = Remote::resolveAPI($repository);
            if ($api) {
                $repository->api = $api;
            }
        }

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
