<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RepositoryController;
use App\Http\Controllers\Admin\TagController;
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

// Frontend routes.
Route::get('/', function () {
    return view('homepage');
})->name('homepage');

Route::get('contribute', function () {
    return view('contribute');
})->name('contribute');

// Admin routes.
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])
->prefix('admin')
->name('admin.')
->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('repositories', [RepositoryController::class, 'index'])->name('repositories.index');
    Route::get('tags', [TagController::class, 'index'])->name('tags.index');
});
