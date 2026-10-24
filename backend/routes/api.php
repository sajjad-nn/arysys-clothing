<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('products', ProductController::class);

Route::apiResource('categories', CategoryController::class);
