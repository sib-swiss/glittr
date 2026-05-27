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

            FetchContributorOrcid::dispatch($contributor);
        }

        $repository->update([
            'contributor_names' => $repository->contributors()
                ->excludingBots()
                ->orderByPivot('contributions', 'desc')
                ->get()
                ->map(fn (Contributor $c) => $c->full_name ?: $c->username)
                ->implode(', ') ?: null,
        ]);
    }
}
