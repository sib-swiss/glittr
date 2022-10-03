<?php

namespace App\Console\Commands;

use App\Facades\Remote;
use App\Models\Author;
use Illuminate\Console\Command;

class UpdateAuthorsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repo:update-authors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update authors information from apis';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->comment('Start updating authors data.');
        $this->newLine();

        foreach (Author::whereNotNull('api')->get() as $author) {
            $this->comment("Updating author {$author->name} with api {$author->api}.");
            $authorData = Remote::for($author)->getAuthorData();
            if ($authorData) {
                $author->update($authorData->toArray());
            }
        }

        $this->newLine();
        $this->comment('Finished updating authors data.');

        return 0;
    }
}
