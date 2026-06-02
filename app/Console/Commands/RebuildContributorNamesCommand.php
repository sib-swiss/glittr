<?php

namespace App\Console\Commands;

use App\Models\Contributor;
use App\Models\Repository;
use Illuminate\Console\Command;

class RebuildContributorNamesCommand extends Command
{
    protected $signature = 'contributors:rebuild-names';

    protected $description = 'Rebuild the contributor_names column for all repositories from current contributor data';

    public function handle(): int
    {
        $count = 0;

        Repository::query()
            ->chunk(500, function ($repositories) use (&$count) {
                foreach ($repositories as $repository) {
                    $repository->update([
                        'contributor_names' => $repository->contributors()
                            ->excludingBots()
                            ->orderByPivot('contributions', 'desc')
                            ->get()
                            ->map(fn (Contributor $c) => $c->full_name ?: $c->username)
                            ->implode(', ') ?: null,
                    ]);

                    $count++;
                }
            });

        $this->comment("Rebuilt contributor_names for {$count} repositories.");

        return 0;
    }
}
