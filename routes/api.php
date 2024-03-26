<?php

use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

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

Route::get('/articles', [ArticleController::class, 'index'])->name('api.v1.articles.index');
Route::post('/articles', [ArticleController::class, 'store'])->name('api.v1.articles.create');
Route::patch('/articles/{article}', [ArticleController::class, 'update'])->name('api.v1.articles.update');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('api.v1.articles.show');
