<?php

namespace App\Jobs;

use App\Models\Contributor;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
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

    public function handle(): void
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (compatible; training-collection-bot/1.0)',
        ])->get('https://github.com/' . $this->contributor->username);

        if ($response->status() === 429) {
            Log::warning('FetchContributorOrcid: rate limited by GitHub, releasing for 120s', [
                'contributor_id' => $this->contributor->id,
                'username' => $this->contributor->username,
                'attempt' => $this->attempts(),
            ]);

            $this->release(120);

            return;
        }

        $orcid = null;
        if (preg_match('/href="https:\/\/orcid\.org\/([\d]{4}-[\d]{4}-[\d]{4}-[\d]{3}[\dX])"/i', $response->body(), $matches)) {
            $orcid = $matches[1];
        }

        $updateData = [
            'orcid' => $orcid,
            'orcid_fetched_at' => now(),
        ];

        if ($this->contributor->full_name === null) {
            if (preg_match('/<span[^>]+itemprop=["\']name["\'][^>]*>\s*(.*?)\s*<\/span>/si', $response->body(), $nameMatches)) {
                $name = trim(strip_tags($nameMatches[1]));
                if ($name !== '') {
                    $updateData['full_name'] = $name;
                }
            }
        }

        $this->contributor->update($updateData);
    }
}
