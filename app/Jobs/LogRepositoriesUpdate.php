<?php

namespace App\Jobs;

use App\Models\Update;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Sammyjo20\LaravelHaystack\Concerns\Stackable;
use Sammyjo20\LaravelHaystack\Contracts\StackableJob;

class LogRepositoriesUpdate implements ShouldQueue, StackableJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Stackable;

    /**
     * Total repositories to update
     *
     * @var int
     */
    public $total;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $total)
    {
        $this->total = $total;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $update = Update::create([
            'started_at' => Carbon::now(),
            'total' => $this->total,
        ]);

        $this->setHaystackData('update_id', $update->id, 'integer');
    }
}
