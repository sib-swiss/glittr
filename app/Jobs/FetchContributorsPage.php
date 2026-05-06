<?php

namespace App\Jobs;

use App\Facades\Remote;
use App\Models\Contributor;
use App\Models\Repository;
use App\Remote\Drivers\GithubDriver;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

class FetchContributorsPage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param array<int, int> $accumulated [contributor_id => contributions]
     */
    public function __construct(
        public Repository $repository,
        public int $page,
        public array $accumulated = [],
    ) {
    }

    /**
     * Keep retrying for 24 hours so rate-limit releases never exhaust the job.
     */
    public function retryUntil(): DateTime
    {
        return now()->addHours(24);
    }

    /**
     * @return array<object>
     */
    public function middleware(): array
    {
        return [new RateLimited('github_contributors')];
    }

    public function handle(): void
    {
        $driver = Remote::for($this->repository);

        if (! $driver instanceof GithubDriver) {
            return;
        }

        $contributors = $driver->getContributorsPage($this->page);

        if (empty($contributors)) {
            FinalizeContributorsSync::dispatch($this->repository, $this->accumulated);

            return;
        }

        $accumulated = $this->accumulated;

        foreach ($contributors as $data) {
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

            $accumulated[$contributor->id] = $data->contributions;
        }

        self::dispatch($this->repository, $this->page + 1, $accumulated);
    }
}
