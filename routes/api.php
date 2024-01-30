<?php

use App\Http\Controllers\Api\RepositoryController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Return json formatted list grouped by main category for readme generation
Route::get('list', [RepositoryController::class, 'list'])->name('api.list');

Route::get('repositories', [RepositoryController::class, 'index'])->name('api.repositories.index');

Route::get('tags', [TagController::class, 'index'])->name('api.tags.index');
Route::get('tags/{tag}', [TagController::class, 'show'])->name('api.tags.show');

Route::get('bioschemas', [RepositoryController::class, 'bioschemas'])->name('api.bioschemas');
