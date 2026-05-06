<?php

namespace App\Jobs;

use App\Models\Contributor;
use App\Models\Repository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class FinalizeContributorsSync implements ShouldQueue
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
        public array $accumulated,
    ) {
    }

    public function handle(): void
    {
        $syncData = collect($this->accumulated)
            ->mapWithKeys(fn ($contributions, $id) => [$id => ['contributions' => $contributions]])
            ->all();

        $this->repository->contributors()->syncWithoutDetaching($syncData);

        Cache::forget($this->repository->jsonLdCacheKey());

        Contributor::whereIn('id', array_keys($this->accumulated))
            ->whereNull('orcid_fetched_at')
            ->each(fn (Contributor $contributor) => FetchContributorOrcid::dispatch($contributor));
    }
}
