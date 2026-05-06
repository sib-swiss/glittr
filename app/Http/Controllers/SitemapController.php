<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Repository;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $sitemap = Sitemap::create();

        $sitemap->add(
            Url::create(route('homepage'))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        $sitemap->add(
            Url::create(route('contribute'))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.5)
        );

        $sitemap->add(
            Url::create(route('terms-of-use'))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
                ->setPriority(0.3)
        );

        Repository::query()
            ->enabled()
            ->get()
            ->each(function (Repository $repository) use ($sitemap): void {
                $url = Url::create(route('repository', $repository->route_params))
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8);

                if ($repository->repository_updated_at) {
                    $url->setLastModificationDate($repository->repository_updated_at);
                }

                $sitemap->add($url);
            });

        Author::query()
            ->whereHas('repositories', fn ($q) => $q->enabled())
            ->get()
            ->each(function (Author $author) use ($sitemap): void {
                $sitemap->add(
                    Url::create(route('author', ['slug' => $author->slug]))
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.6)
                );
            });

        return $sitemap->toResponse(request());
    }
}
