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
Route::get('list', [RepositoryController::class, 'list']);

Route::get('repositories', [RepositoryController::class, 'index']);

Route::get('tags', [TagController::class, 'index']);

Route::get('bioschemas', [RepositoryController::class, 'bioschemas']);
