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
    protected $signature = 'repo:import {--clean} {--clean-topics}';

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

        if ($this->option('clean-topics')) {
            Tag::query()->delete();
            Category::query()->delete();
        }

        $reader = Reader::createFromPath(database_path('import/topics.csv', 'r'));
        $topics = [];
        $colors = config('glittr.colors');
        foreach ($reader->getRecords() as $offset => $topic) {
            if ($offset > 0) {
                $topicData = [
                    'name' => $topic[1],
                    'category' => $topic[2],
                    'definition' => $topic[5],
                ];
                $color = count($colors) > 0 ? array_shift($colors) : '#000000';
                $category = Category::firstOrCreate(
                    ['name' => $topicData['category']],
                    ['color' => $color]
                );
                $topic = Tag::firstOrCreate(
                    [
                        'name' => $topicData['name'],
                        'category_id' => $category->id,
                    ],
                );
                $topics[$topicData['name']] = $topic->id;
            }
        }

        $reader = Reader::createFromPath(database_path('import/slug_tags.csv', 'r'));
        foreach ($reader->getRecords() as $offset => $repo) {
            if (count($repo) > 1) {
                if (isset($repo[0]) && $repo[0] != '') {
                    $url = trim($repo[0]);
                    if (Str::endsWith('/', $url)) {
                        $url = substr($url, 0, -1);
                    }
                    if (! Str::startsWith($url, 'http://') && ! Str::startsWith($url, 'https://')) {
                        $url = 'https://github.com/'.$url;
                    }
                    if (Str::startsWith($url, 'https://')) {
                        $paths = explode('/', $url);
                        if (count($paths) > 2) {
                            try {
                                if (! Repository::where('url', $url)->exists()) {
                                    $total++;
                                    $repoName = strtolower($paths[count($paths) - 2].'/'.end($paths));
                                    $repoTopics = explode(',', $repo[1]);
                                    $topicIds = [];
                                    if (! empty($repoTopics)) {
                                        foreach ($repoTopics as $topic) {
                                            $topic = trim($topic);
                                            if (isset($topics[$topic])) {
                                                $topicIds[] = $topics[$topic];
                                            } else {
                                                $topicIds[] = Tag::firstOrCreate([
                                                    'name' => $topic,
                                                ])->id;
                                            }
                                        }
                                    }
                                    $repository = Repository::firstOrCreate([
                                        'url' => $url,
                                    ]);
                                    $repository->tags()->sync($topicIds);

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
        }

        $this->newLine();
        $this->comment('Importing base repositories ended.');

        return 0;
    }
}
