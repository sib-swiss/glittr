<?php

namespace App\Actions;

use App\Data\RemoteData;
use App\Facades\Remote;
use App\Models\Repository;
use App\Remote\Helpers;
use Illuminate\Support\Carbon;

class RemoteUpdateRepository
{
    /**
     * Attach author action
     *
     * @var AttachAuthorAction
     */
    protected $attachAuthor;

    public function __construct(AttachAuthor $attachAuthorAction)
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
            /** @var RemoteData $data */
            $data = Remote::for($repository)->getData();
            if ($data) {
                $repository->update([
                    ...$data->toArray(),
                    'refreshed_at' => Carbon::now(),
                ]);
            }
            [$urlUsername] = Helpers::getRepositoryUserAndName($repository->url);
            if (! $repository->author ||
                ($data->author_id && $repository->author && $data->author_id != $repository->author->remote_id) ||
                ($repository->author && strtolower($urlUsername) !== strtolower($repository->author->name))
            ) {
                // Attach author if not linked.
                $authorData = Remote::for($repository)->getAuthorData();
                if ($authorData) {
                    $this->attachAuthorAction->execute($repository, $authorData);
                }
            }
        }
    }
}
