<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\RepositoryController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\TagController;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

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

/**
 * Frontend routes
 */
Route::get('/', function () {
    return view('welcome');
})->name('homepage');

/**
 * Admin route
 */
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])
->prefix('admin')
->name('admin.')
->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('authors', AuthorController::class);
    Route::resource('submissions', SubmissionController::class)->except(['create', 'store']);
    Route::resource('repositories', RepositoryController::class);
    Route::resource('categories', Category::class);
    Route::resource('tags', TagController::class);
});
