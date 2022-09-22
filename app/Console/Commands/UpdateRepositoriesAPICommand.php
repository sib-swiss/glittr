<?php

namespace App\Console\Commands;

use App\Facades\Remote;
use App\Models\Repository;
use Illuminate\Console\Command;

class UpdateRepositoriesAPICommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repo:update-apis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update repositories api column based on manager resolver';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->comment('Start updating repositories linked api.');

        $updated = 0;
        Repository::chunk(100, function ($repositories) use ($updated) {
            foreach ($repositories as $repository) {
                $api = Remote::resolveAPI($repository);
                if ($api != $repository->api) {
                    $repository->api = $api;
                    $repository->save();
                    $updated++;
                    $this->info("Updated repository {$repository->url} with api {$api}.");
                }
            }
        });

        $this->comment("Finished procesing repositories ({$updated} updated).");

        return 0;
    }
}
