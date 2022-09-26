<?php

namespace App\Actions\Repository;

use App\Facades\Remote;
use App\Models\Repository;
use Illuminate\Support\Carbon;

class UpdateRemoteAction
{
    /**
     * Attach author action
     *
     * @var AttachAuthorAction
     */
    protected $attachAuthorAction;

    public function __construct(AttachAuthorAction $attachAuthorAction)
    {
        $this->attachAuthorAction = $attachAuthorAction;
    }

    public function execute(Repository $repository)
    {
        // Try to get api if not resolved in db.
        if (! $repository->api) {
            $api = Remote::resolveAPI($repository);
            if ($api) {
                $repository->api = $api;
                $repository->save();
            }
        }

        if ($repository->api) {
            $data = Remote::for($repository)->getData();
            if ($data) {
                $repository->update([
                    ...$data->toArray(),
                    'refreshed_at' => Carbon::now(),
                ]);
            }
            // Attach author if not linked.
            if (! $repository->author) {
                $authorData = Remote::for($repository)->getAuthorData();
                if ($authorData) {
                    $this->attachAuthorAction->execute($repository, $authorData);
                }
            }
        }
    }
}
