<?php

namespace App\Console\Commands;

use App\Jobs\FetchContributorOrcid;
use App\Models\Contributor;
use Illuminate\Console\Command;

class RefreshContributorsCommand extends Command
{
    protected $signature = 'contributors:refresh';

    protected $description = 'Dispatch info refresh jobs for all non-bot contributors';

    public function handle(): int
    {
        $count = 0;

        Contributor::query()
            ->excludingBots()
            ->chunk(500, function ($contributors) use (&$count) {
                foreach ($contributors as $contributor) {
                    FetchContributorOrcid::dispatch($contributor);
                    $count++;
                }
            });

        $this->comment("Dispatched {$count} contributor refresh jobs.");

        return 0;
    }
}
