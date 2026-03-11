<?php

namespace App\Console\Commands;

use App\Facades\Remote;
use App\Mail\AuthorsUpdated;
use App\Models\Author;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

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

        $failures = [];

        foreach (Author::whereNotNull('api')->get() as $author) {
            $this->comment("Updating author {$author->name} with api {$author->api}.");
            try {
                $authorData = Remote::for($author)->getAuthorData();
                if ($authorData) {
                    $author->update($authorData->toArray());
                }
            } catch (Throwable $e) {
                $this->error("Failed to update author {$author->name}: {$e->getMessage()}");
                $failures[] = [
                    'name' => $author->name,
                    'api' => $author->api,
                    'error' => $e->getMessage(),
                ];
            }
        }

        $this->newLine();
        $this->comment('Finished updating authors data.');

        if (!empty($failures)) {
            $this->warn(count($failures) . ' author(s) failed to update. Sending report...');
            foreach (config('glittr.support_emails', []) as $recipient) {
                if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($recipient)->queue(new AuthorsUpdated($failures));
                }
            }
        }

        return 0;
    }
}
