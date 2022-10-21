<?php

namespace App\Jobs;

use App\Actions\RemoteUpdateRepository;
use App\Models\Repository;
use App\Models\Update;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sammyjo20\LaravelHaystack\Concerns\Stackable;
use Sammyjo20\LaravelHaystack\Contracts\StackableJob;
use Throwable;

class StackableUpdateRepositoryData implements ShouldQueue, StackableJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Stackable;

    /**
     * The repository to udpate
     *
     * @var Repository
     */
    public $repository;

    /**
     * Number of allowed tries
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array
     */
    public function backoff()
    {
        return [10, 30, 60];
    }

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(RemoteUpdateRepository $remoteUpdateRepository): void
    {
        $this->repository->refresh();

        $remoteUpdateRepository->execute($this->repository);

        $logger = $this->getUpdateLog();
        if ($logger) {
            $logger->increment('success');
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        //TODO: check exception to catch api limit and pause all the haystack ?
        $logger = $this->getUpdateLog();
        if ($logger) {
            $logger->increment('error');

            $errors = $logger->errors;
            $errors[] = [
                'repository_id' => $this->repository->id,
                'url' => (string) $this->repository->url,
                'error' => $exception->getMessage(),
            ];
            $logger->errors = $errors;
            $logger->save();
        }
    }

    protected function getUpdateLog(): ?Update
    {
        $update_id = $this->getHaystackData('update_id');
        if ($update_id) {
            return Update::find($update_id);
        }

        return null;
    }
}
