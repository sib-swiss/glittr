<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RepositoryFullResource;
use App\Http\Resources\RepositoryResource;
use App\Models\Category;
use App\Models\Repository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RepositoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return RepositoryFullResource::collection(
            QueryBuilder::for(Repository::enabled())
            ->allowedFilters([
                'name',
                'license',
                'description',
                AllowedFilter::exact('author.name'),
                'author.display_name',
                AllowedFilter::exact('tags.name'),
                'tags.category.name',
            ])
            ->allowedSorts([
                'name',
                'stargazers',
                'last_push',
                'author.name',
            ])
            ->defaultSort('-stargazers')
            ->with('author', 'tags', 'tags.category')
            ->jsonPaginate(100)
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $grouped = Repository::enabled()->with('author')->get()->groupBy('main_category.id');

        return Category::with(
            [
                'tags' => function ($query) {
                    $query->ordered();
                },
            ]
        )
        ->ordered()
        ->get()
        ->map(function ($category) use ($grouped) {
            $data = [
                'name' => $category->name,
                'topics' => $category->tags->map(function ($tag) use ($grouped) {
                    $repositories = $grouped->get($tag->id);
                    if ($repositories) {
                        return  [
                            'name' => $tag->name,
                            'repositories' => RepositoryResource::collection($repositories),
                        ];
                    }

                    return null;
                })->filter()->values(),
            ];

            return $data;
        });
    }
}
