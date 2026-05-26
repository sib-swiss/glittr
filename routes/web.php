<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ContributorController;
use App\Http\Controllers\Admin\RepositoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\SitemapController;
use App\Models\Author;
use App\Models\Repository;
use App\Settings\GeneralSettings;
use App\Settings\TermsSettings;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Michelf\MarkdownExtra;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;
use Spatie\Health\Http\Controllers\SimpleHealthCheckController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Admin routes.
Route::middleware(
    [
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    ]
)
    ->prefix('admin')
    ->name('admin.')
    ->group(
        function () {
            Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('apicuron', [AdminController::class, 'apicuron'])->name('apicuron-leaderboard');
            Route::get('repositories', [RepositoryController::class, 'index'])->name('repositories.index');
            Route::get('contributors', [ContributorController::class, 'index'])->name('contributors.index');
            Route::get('tags', [TagController::class, 'index'])->name('tags.index');
            Route::get('ontologies', [AdminController::class, 'ontologies'])->name('ontologies.index');
            Route::get('settings', [AdminController::class, 'settings'])->name('settings');
        }
    );

// Sitemap.
Route::get('sitemap.xml', SitemapController::class)->name('sitemap');

// Health check routes.
Route::get('simple-health-check', SimpleHealthCheckController::class);
Route::get('full-health-check', HealthCheckResultsController::class)->middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
]);

// Frontend routes.
Route::middleware('early.hints')->group(
    function () {
        Route::get(
            '/',
            function () {
                return view(
                    'homepage',
                    [
                    'title' => app(GeneralSettings::class)->homepage_page_title,
                    ]
                );
            }
        )->name('homepage');

        Route::get(
            '/repository/{repository}',
            function (Repository $repository) {
                return redirect()->route('repository', $repository->route_params, 301);
            }
        )->name('repository.legacy');

        Route::get(
            'contribute',
            function () {
                return view(
                    'contribute',
                    [
                    'title' => 'Contribute | ' . app(GeneralSettings::class)->site_name,
                    ]
                );
            }
        )->name('contribute');

        Route::get(
            'terms-of-use',
            function () {
                $terms = app(TermsSettings::class)->terms;
                $parser = new MarkdownExtra();
                $parser->hard_wrap = true;
                return view(
                    'terms-of-use',
                    [
                    'title' => 'Terms of use | ' . app(GeneralSettings::class)->site_name,
                    'terms' => $parser->transform($terms),
                    ]
                );
            }
        )->name('terms-of-use');

        Route::get(
            '/{slug}',
            function (string $slug) {
                $author = Author::findBySlug($slug);
                if (! $author) {
                    abort(404);
                }

                return view(
                    'author',
                    [
                    'title' => ($author->display_name ?: $author->name) . ' | ' . app(GeneralSettings::class)->site_name,
                    'author' => $author,
                    ]
                );
            }
        )->where('slug', '[a-zA-Z0-9][a-zA-Z0-9_-]*')->name('author');
    }
);

// ORCID OAuth routes.
Route::get(
    'orcid/login',
    function () {
        return Socialite::driver('orcid')
            ->setScopes(['/authenticate'])
            ->redirect();
    }
)->name('orcid.login');

Route::get(
    'orcid/callback',
    function () {
        $user = Socialite::driver('orcid')->user();
        session(['orcid' => [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'token' => $user->token,
        ]]);
        return redirect()->route('contribute');
    }
)->name('orcid.callback');

Route::get(
    'orcid/logout',
    function () {
        session()->forget('orcid');
        return redirect()->route('contribute');
    }
)->name('orcid.logout');

// Repository slug route — must be last to avoid capturing explicit routes above.
Route::middleware('early.hints')->get(
    '/{username}/{reponame}',
    function (string $username, string $reponame) {
        $repository = Repository::where('name', $username . '/' . $reponame)
            ->enabled()
            ->firstOrFail();

        $repository->load(['author', 'tags', 'tags.category', 'tags.ontology']);
        $repository->loadCount(['contributors' => fn ($q) => $q->excludingBots()]);

        $readmeHtml = null;
        if ($repository->readme) {
            $parser = new MarkdownExtra();
            $parser->hard_wrap = true;
            $readmeHtml = $parser->transform($repository->readme);
        }

        return view(
            'repository',
            [
            'title' => $repository->name . ' | Glittr',
            'description' => $repository->description ?: null,
            'repository' => $repository,
            'readme_html' => $readmeHtml,
            'jsonLd' => $repository->getJsonLdArray(),
            ]
        );
    }
)->where('reponame', '.+')->name('repository');
