<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Repository;
use League\Csv\Reader;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class ImportBase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repo:import {--clean}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import base repositories from csv';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('clean')) {
            Author::query()->delete();
            Repository::query()->delete();
        }

        $reader = Reader::createFromPath(database_path('import/tags.csv', 'r'));
        $tags = collect();

        foreach($reader->getRecords() as $offset => $tag) {
            if (isset($tag[1])) {
                $tags->push([
                    'repository' => strtolower($tag[0]),
                    'tags' => explode(', ', $tag[1]),
                ]);
            }
        }

        $reader = Reader::createFromPath(database_path('import/repositories.csv', 'r'));
        foreach ($reader->getRecords() as $offset => $repo) {
            if (isset($repo[0]) && $repo[0] != '') {
                $url = trim($repo[0]);
                if (Str::endsWith('/', $url)) {
                    $url = substr($url, 0, -1);
                }
                if (Str::startsWith($url, 'https://')) {
                    $paths = explode('/', $url);
                    if (count($paths) > 2 ) {
                        $repoName = strtolower($paths[count($paths) - 2].'/'.end($paths));
                        $repoTags = $tags->where('repository' ,$repoName)->first();

                        $tagIds = [];
                        if ($repoTags && isset($repoTags['tags']) && !empty($repoTags['tags'])) {
                            foreach ($repoTags['tags'] as $tag) {
                                dd($tag);
                                //todo: get id or create new
                            }
                        }

                        $repository = Repository::create([
                            'url' => $url,
                        ]);
                        //todo: save and sync tags ids

                    }
                }
            }
        }

        return 0;
    }
}
