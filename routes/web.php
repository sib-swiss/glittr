<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RepositoryController;
use App\Http\Controllers\Admin\TagController;
use App\Models\Repository;
use App\Settings\GeneralSettings;
use App\Settings\TermsSettings;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Michelf\MarkdownExtra;

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

// Frontend routes.
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
        return view(
            'repository',
            [
            'title' => 'Repository | ' . $repository->name,
            'repository' => $repository,
            ]
        );
    }
)->name('repository');

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
            Route::get('tags', [TagController::class, 'index'])->name('tags.index');
            Route::get('ontologies', [AdminController::class, 'ontologies'])->name('ontologies.index');
            Route::get('settings', [AdminController::class, 'settings'])->name('settings');
        }
    );
