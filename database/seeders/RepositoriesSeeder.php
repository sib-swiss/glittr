<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Repository;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class RepositoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = Tag::select('id')->get();

        Author::query()->delete();
        Repository::query()->delete();

        $authors = Author::factory()->count(50)->create();
        $authorsIds = $authors->pluck('id')->toArray();
        $repositories = Repository::factory()->enabled()->valid(true)->count(500)->create();

        foreach ($repositories as $repository) {
            $repository->author_id = $authorsIds[array_rand($authorsIds, 1)];
            $repository->save();
            $repository->tags()->sync($tags->random(rand(1, 4))->pluck('id')->toArray());
        }
    }
}
