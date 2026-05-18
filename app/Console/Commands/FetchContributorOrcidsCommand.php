<?php

namespace App\Console\Commands;

use App\Jobs\FetchContributorOrcid;
use App\Models\Contributor;
use Illuminate\Console\Command;

class FetchContributorOrcidsCommand extends Command
{
    protected $signature = 'contributors:fetch-orcids';

    protected $description = 'Dispatch ORCID fetch jobs for contributors whose ORCID has not been fetched yet';

    public function handle(): int
    {
        $count = 0;

        Contributor::query()
            ->excludingBots()
            ->whereNull('orcid_fetched_at')
            ->chunk(500, function ($contributors) use (&$count) {
                foreach ($contributors as $contributor) {
                    FetchContributorOrcid::dispatch($contributor);
                    $count++;
                }
            });

        $this->comment("Dispatched {$count} ORCID fetch jobs.");

        return 0;
    }
}
