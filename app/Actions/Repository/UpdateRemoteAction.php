<?php

namespace App\Actions\Repository;

use App\Data\RemoteData;
use App\Models\Repository;
use Illuminate\Support\Carbon;

class UpdateRemoteAction
{
    public function execute(Repository $repository, RemoteData $data)
    {
        $repository->update([
            ...$data->toArray(),
            'refreshed_at' => Carbon::now(),
        ]);
    }
}
