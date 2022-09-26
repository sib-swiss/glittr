<?php

namespace App\Console\Commands;

use App\Actions\AttachAuthor;
use App\Actions\RemoteUpdateRepository;
use App\Jobs\StackableUpdateRepositoryData;
use App\Models\Repository;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Sammyjo20\LaravelHaystack\Models\Haystack;

class UpdateRepositoriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repo:update {repository?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update repositories with remote data';

    /**
     * Execute the console command.
     *
     * @param  RemoteUpdateRepository  $remoteUpdateRepository
     * @param  AttachAuthor  $attachAuthorAction
     * @return int
     */
    public function handle(RemoteUpdateRepository $remoteUpdateRepository, AttachAuthor $attachAuthorAction)
    {
        if ($this->hasArgument('repository') && $this->argument('repository')) {
            $this->comment('Start updating repository.');
            $repository = Repository::find($this->argument('repository'));
            if ($repository) {
                $this->comment("Updating repository {$repository->url}");
                $remoteUpdateRepository->execute($repository);
            } else {
                $this->error("Repository not found {$this->argument('repository')}");

                return 1;
            }
        } else {
            $haystack = Haystack::build()
                ->withDelay(10)
                ->then(function () {
                    Cache::forever('last_updated_at', Carbon::now());
                });

            Repository::chunk(500, function ($repositories) use ($haystack) {
                foreach ($repositories as $repository) {
                    $this->comment("Adding repository to the stack {$repository->url}");
                    $haystack->addJob(new StackableUpdateRepositoryData($repository));
                }
            });

            $haystack->dispatch();

            $this->comment('Finished stacking repositories for update.');
        }

        return 0;
    }
}
