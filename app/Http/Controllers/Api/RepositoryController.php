<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RepositoryFullResource;
use App\Http\Resources\RepositoryResource;
use App\Models\Category;
use App\Models\Repository;
use Artesaos\SEOTools\Contracts\JsonLd;
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
        $queryBuilder = $this->getQueryBuilder();

        // Paginate if request has a page parameter
        if (request()->has('page')) {
            return RepositoryFullResource::collection($queryBuilder->jsonPaginate(100));
        } else {
            return RepositoryFullResource::collection($queryBuilder->get());
        }
    }

    /**
     * Repositories bioschemas endpoint.
     *
     * @return \Illuminate\Http\Response
     */
    public function bioschemas()
    {
        $queryBuilder = $this->getQueryBuilder();
        $jsonLd = [];

        /** @var Repository $repository */
        foreach ($queryBuilder->get() as $repository) {
            $jsonLd[] = array_merge(
                [
                    '@context' => 'https://schema.org',
                ],
                $repository->getJsonLd()->convertToArray(),
            );
        }

        return Response($jsonLd, 200, [
            'Content-Type' => 'application/ld+json',
        ]); // get the JsonLd group as a json string
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
                            return [
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

    /**
     * Get the shared query builder for the bioschemas and repositories endpoint.
     *
     * @return \Spatie\QueryBuilder\QueryBuilder
     */
    protected function getQueryBuilder()
    {

        return QueryBuilder::for(Repository::enabled())
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
            ->with('author', 'tags', 'tags.category', 'tags.ontology');
    }
}
