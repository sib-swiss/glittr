<?php

namespace App\Jobs;

use App\Actions\FetchContributorInfo;
use App\Models\Contributor;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchContributorOrcid implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Contributor $contributor)
    {
    }

    /**
     * Keep retrying for up to 6 hours instead of using a fixed attempt count.
     * Rate-limited releases consume attempts, so a time window is the right constraint.
     */
    public function retryUntil(): DateTime
    {
        return now()->addHours(6);
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<object>
     */
    public function middleware(): array
    {
        return [new RateLimited('github_scraping')];
    }

    public function failed(Throwable $exception): void
    {
        Log::error('FetchContributorOrcid: job permanently failed', [
            'contributor_id' => $this->contributor->id,
            'username' => $this->contributor->username,
            'error' => $exception->getMessage(),
        ]);
    }

    public function handle(FetchContributorInfo $action): void
    {
        $fetched = $action->execute($this->contributor);

        if (! $fetched) {
            Log::warning('FetchContributorOrcid: rate limited by GitHub, releasing for 120s', [
                'contributor_id' => $this->contributor->id,
                'username' => $this->contributor->username,
                'attempt' => $this->attempts(),
            ]);

            $this->release(120);
        }
    }
}
