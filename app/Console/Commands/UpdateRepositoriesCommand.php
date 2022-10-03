<?php

namespace App\Console\Commands;

use App\Actions\AttachAuthor;
use App\Actions\RemoteUpdateRepository;
use App\Jobs\LogRepositoriesUpdate;
use App\Jobs\StackableUpdateRepositoryData;
use App\Mail\RepositoriesUpdated;
use App\Models\Repository;
use App\Models\Update;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
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
            $this->newLine();

            $repository = Repository::find($this->argument('repository'));
            if ($repository) {
                $this->comment("Updating repository {$repository->url}");
                $remoteUpdateRepository->execute($repository);
            } else {
                $this->error("Repository not found {$this->argument('repository')}");

                return 1;
            }

            $this->newLine();
            $this->comment('Finished updating repository.');
        } else {
            $this->comment('Start updating all repositories.');
            $this->newLine();

            $repositories = Repository::enabled();
            $haystack = Haystack::build()
                //->withDelay(1)
                ->withName('Update Repositories Data')
                ->allowFailures()
                ->addJob(new LogRepositoriesUpdate($repositories->count()))
                ->then(function ($data) {
                    if (isset($data['update_id'])) {
                        $update = Update::find($data['update_id']);
                        $update->finished_at = Carbon::now();
                        $update->save();

                        // If success percentage > 90 add date to cache for footer render
                        if ($update->percentSuccess() > 90) {
                            Cache::forever('last_updated_at', $update->finished_at);
                        }

                        // Send report update.
                        foreach (config('repositories.support_emails', []) as $recipient) {
                            Mail::to($recipient)->queue(new RepositoriesUpdated($update));
                        }
                    }
                });

            $repositories->chunk(500, function ($repositories) use ($haystack) {
                foreach ($repositories as $repository) {
                    $this->comment("Adding repository to the stack {$repository->url}");
                    $haystack->addJob(new StackableUpdateRepositoryData($repository));
                }
            });

            $haystack->dispatch();

            $this->newLine();
            $this->comment('Finished stacking repositories for update.');
        }

        return 0;
    }
}
