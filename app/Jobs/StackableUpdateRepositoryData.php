<?php

namespace App\Jobs;

use App\Actions\RemoteUpdateRepository;
use App\Models\Repository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sammyjo20\LaravelHaystack\Concerns\Stackable;
use Sammyjo20\LaravelHaystack\Contracts\StackableJob;

class StackableUpdateRepositoryData implements ShouldQueue, StackableJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Stackable;

    /**
     * The repository to udpate
     *
     * @var Repository
     */
    public $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(RemoteUpdateRepository $remoteUpdateRepository): void
    {
        $this->repository->refresh();

        $remoteUpdateRepository->execute($this->repository);
    }
}
