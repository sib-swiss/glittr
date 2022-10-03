<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Category;
use App\Models\Repository;
use App\Models\Tag;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use League\Csv\Reader;

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
        $this->comment('Start importing base repsitories.');
        $this->newLine();

        $total = 0;
        $otherCategory = Category::where('name', 'Others')->first();

        if ($this->option('clean')) {
            Author::query()->delete();
            Repository::query()->delete();
        }

        $reader = Reader::createFromPath(database_path('import/tags.csv', 'r'));
        $tags = collect();

        foreach ($reader->getRecords() as $offset => $tag) {
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
                    if (count($paths) > 2) {
                        try {
                            if (! Repository::where('url', $url)->exists()) {
                                $total++;

                                $repoName = strtolower($paths[count($paths) - 2].'/'.end($paths));
                                $repoTags = $tags->where('repository', $repoName)->first();

                                $tagIds = collect();
                                if ($repoTags && isset($repoTags['tags']) && ! empty($repoTags['tags'])) {
                                    $tagIds = collect($repoTags['tags'])->map(function ($tag) use ($otherCategory) {
                                        $dbTag = Tag::firstOrCreate([
                                            'name' => $tag,
                                        ]);

                                        // Assign new tags to "Others" category.
                                        if (! $dbTag->category_id && $otherCategory) {
                                            $dbTag->category_id = $otherCategory->id;
                                            $dbTag->save();
                                        }

                                        return $dbTag->id;
                                    });
                                }
                                $repository = Repository::firstOrCreate([
                                    'url' => $url,
                                ]);
                                $repository->tags()->sync($tagIds->toArray());

                                $this->info("Repository {$url} created");
                            } else {
                                $this->comment("Repository {$url} already exists.");
                            }
                        } catch (Exception $e) {
                            $this->error("Error creating repository {$url}.");
                            $this->error("Error: {$e->getMessage()}.");
                        }
                    }
                } else {
                    $this->error("Repository url {$url} doesn't start with https://");
                }
            }
        }

        $this->newLine();
        $this->comment('Importing base repositories ended.');

        return 0;
    }
}
