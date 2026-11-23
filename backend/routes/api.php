<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ArticleCommentController;
use App\Http\Controllers\Api\HeroSlideController;
use App\Http\Controllers\Api\AboutSlideController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('products', ProductController::class);

Route::apiResource('categories', CategoryController::class);
Route::get('/stories', [StoryController::class, 'index']);

// Articles
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{slug}', [ArticleController::class, 'show']);
Route::middleware('auth:sanctum')->post('/logout', [
    AuthenticatedSessionController::class,
    'destroy',
]);
// Product Reviews
Route::get('/products/{product}/reviews', [
    ProductReviewController::class,
    'index',
]);

Route::middleware('auth:sanctum')->post('/products/{product}/reviews', [
    ProductReviewController::class,
    'store',
]);

// Article Comments
Route::get('/articles/{article}/comments', [
    ArticleCommentController::class,
    'index',
]);

Route::middleware('auth:sanctum')->post('/articles/{article}/comments', [
    ArticleCommentController::class,
    'store',
]);

// Homepage Sliders
Route::get('/hero-slides', [HeroSlideController::class, 'index']);

// About Sliders
Route::get('/about-slides', [AboutSlideController::class, 'index']);
