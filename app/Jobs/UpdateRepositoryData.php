<?php

namespace App\Jobs;

use App\Actions\RemoteUpdateRepository;
use App\Models\Repository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateRepositoryData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
