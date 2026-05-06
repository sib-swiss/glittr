<?php

namespace App\Actions;

use App\Data\ContributorData;
use App\Facades\Remote;
use App\Jobs\FetchContributorOrcid;
use App\Models\Contributor;
use App\Models\Repository;
use App\Remote\Drivers\GithubDriver;

class SyncContributors
{
    public function execute(Repository $repository): void
    {
        if ($repository->api !== 'github') {
            return;
        }

        /** @var GithubDriver $driver */
        $driver = Remote::for($repository);

        /** @var array<ContributorData> $contributorsData */
        $contributorsData = $driver->getContributors();

        foreach ($contributorsData as $data) {
            $updateData = [
                'username' => $data->username,
                'profile_url' => $data->profile_url,
                'avatar_url' => $data->avatar_url,
            ];

            if ($data->full_name !== null) {
                $updateData['full_name'] = $data->full_name;
            }

            $contributor = Contributor::updateOrCreate(
                ['remote_id' => $data->remote_id, 'api' => 'github'],
                $updateData,
            );

            $repository->contributors()->syncWithoutDetaching([
                $contributor->id => ['contributions' => $data->contributions],
            ]);

            if ($contributor->orcid_fetched_at === null) {
                FetchContributorOrcid::dispatch($contributor);
            }
        }
    }
}
