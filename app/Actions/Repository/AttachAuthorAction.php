<?php

namespace App\Actions\Repository;

use App\Data\AuthorData;
use App\Models\Author;
use App\Models\Repository;

class AttachAuthorAction
{
    public function execute(Repository $repository, AuthorData $data)
    {
        //Look if author exist
        $author = Author::where('remote_id', $data->remote_id)->where('api', $repository->api)->first();
        if (! $author) {
            $author = Author::create([
                ...$data->toArray(),
                'api' => $repository->api,
            ]);
        }

        $repository->author_id = $author->id;
        $repository->save();
    }
}
